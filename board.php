<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Board
 */

use Diplomacy\Services\Games\Factory;

require_once('header.php');

if ( ! isset($_REQUEST['gameID']) )
{
	libHTML::error(l_t("You haven't specified a game to view, please go back to the game listings and choose one."));
}

$gameID = (int)$_REQUEST['gameID'];

// If we are trying to join the game lock it for update, so it won't get changed while we are joining it.
if ( $User->type['User'] && ( isset($_REQUEST['join']) || isset($_REQUEST['leave']) ) && libHTML::checkTicket() )
{
	try
	{
		require_once(l_r('gamemaster/game.php'));

		$Variant=libVariant::loadFromGameID($gameID);
		libVariant::setGlobals($Variant);
		$Game = $Variant->processGame($gameID);

		// If viewing an archive page make that the title, otherwise us the name of the game
		libHTML::starthtml($Game->titleBarName());

		if ( isset($_REQUEST['join']) )
		{
			// They will be stopped here if they're not allowed.
			$Game->Members->join(
				( isset($_REQUEST['gamepass']) ? $_REQUEST['gamepass'] : null ),
				( isset($_REQUEST['countryID']) ? $_REQUEST['countryID'] : null ) );
		}
		elseif ( isset($_REQUEST['leave']) )
		{
			$reason=$Game->Members->cantLeaveReason();

			if($reason)
				throw new Exception(l_t("Can't leave game; %s.",$reason));
			else
				$Game->Members->ByUserID[$User->id]->leave();
		}
	}
	catch(Exception $e)
	{
		// Couldn't leave/join game
		libHTML::error($e->getMessage());
	}
}
else
{
	try
	{
		require_once(l_r('objects/game.php'));
		require_once(l_r('board/chatbox.php'));
		require_once(l_r('gamepanel/gameboard.php'));

		$Variant=libVariant::loadFromGameID($gameID);
		libVariant::setGlobals($Variant);
		/** @var panelGameBoard $Game */
		$Game = $Variant->panelGameBoard($gameID);

		// If viewing an archive page make that the title, otherwise us the name of the game
		libHTML::starthtml($Game->titleBarName());

		if ($Game->Members->isJoined() && !$Game->Members->isTempBanned())
		{
			// We are a member, load the extra code that we might need
			require_once(l_r('gamemaster/gamemaster.php'));
			require_once(l_r('board/member.php'));
			require_once(l_r('board/orders/orderinterface.php'));

			global $Member;
			$Game->Members->makeUserMember($User->id);
			$Member = $Game->Members->ByUserID[$User->id];
		}
	}
	catch(Exception $e)
	{
		// Couldn't load game
		libHTML::error(l_t("Couldn't load specified game; this probably means this game was cancelled or abandoned.")." ".
			($User->type['User'] ? l_t("Check your <a href='index.php' class='light'>notices</a> for messages regarding this game."):''));
	}
}

if ($Game->watched() && isset($_REQUEST['unwatch'])) {
	print '<div class="content-notice gameTimeRemaining">'
		.'<form method="post" action="redirect.php">'
		.'Are you sure you wish to remove this game from your spectated games list? '
		.'<input type="hidden" name="gameID" value="'.$Game->id.'">'
		.'<input type="submit" class="form-submit" name="unwatch" value="Confirm">
		</form></div>';
}

// Before HTML pre-generate everything and check input, so game summary header will be accurate

if (isset($Member) && $Member->status == 'Playing' && !$Game->isFinished())
{
	if(!$Game->isPreGame())
	{
		if(isset($_REQUEST['Unpause'])) $_REQUEST['Pause']='on'; // Hack because Unpause = toggle Pause

		foreach(Members::$votes as $possibleVoteType) {
			if( isset($_REQUEST[$possibleVoteType]) && isset($Member) && libHTML::checkTicket() )
				$Member->toggleVote($possibleVoteType);
		}
	}

	$DB->sql_put("COMMIT");

	if (!$Game->isCrashed() && !$Game->isPaused() && $Game->attempts > count($Game->Members->ByID)/2+4  )
	{
		require_once(l_r('gamemaster/game.php'));
		$Game = $Game->Variant->processGame($Game->id);
		$Game->crashed();
		$DB->sql_put("COMMIT");
	}
	else
	{
		if ($Game->Members->votesPassed() && !$Game->isFinished())
		{
		    $g = \Diplomacy\Models\Game::find($Game->id);
		    $g->attempts = $g->attempts + 1;
		    $g->save();

			require_once(l_r('gamemaster/game.php'));
			$Game = $Game->Variant->processGame($Game->id);
			try
			{
				$Game->applyVotes(); // Will requery votesPassed()

                $g = \Diplomacy\Models\Game::find($Game->id);
                $g->attempts = 0;
                $g->save();
			}
			catch(Exception $e)
			{
				if( $e->getMessage() == "Abandoned" || $e->getMessage() == "Cancelled" )
				{
					assert($Game->isPreGame() || $e->getMessage() == 'Cancelled');
					$DB->sql_put("COMMIT");
					libHTML::notice(l_t('Cancelled'), l_t("Game was cancelled or didn't have enough players to start."));
				}
				else
					$DB->sql_put("ROLLBACK");

				throw $e;
			}
		}
		else if( $Game->needsProcess() )
		{
            $g = \Diplomacy\Models\Game::find($Game->id);
            $g->attempts = $g->attempts + 1;
            $g->save();

			require_once(l_r('gamemaster/game.php'));
			$Game = $Game->Variant->processGame($Game->id);
			if( $Game->needsProcess() )
			{
				try
				{
					$Game->process();
                    $g = \Diplomacy\Models\Game::find($Game->id);
                    $g->attempts = 0;
                    $g->save();
				}
				catch(Exception $e)
				{
					if( $e->getMessage() == "Abandoned" || $e->getMessage() == "Cancelled" )
					{
						assert($Game->isPreGame() || $e->getMessage() == 'Cancelled');
						$DB->sql_put("COMMIT");
						libHTML::notice(l_t('Cancelled'), l_t("Game was cancelled or didn't have enough players to start."));
					}
					else
						$DB->sql_put("ROLLBACK");

					throw $e;
				}
			}
		}
	}

	if ($Game instanceof processGame)
	{
		$Game = $Game->Variant->panelGameBoard($Game->id);
		$Game->Members->makeUserMember($User->id);
		$Member = $Game->Members->ByUserID[$User->id];
	}

	if ($Game->isInProgress())
	{
		$OI = OrderInterface::newBoard();
		$OI->load();

		$Orders = '<div id="orderDiv'.$Member->id.'">'.$OI->html().'</div>';
		unset($OI);
	}
}

$gameFactory = new Factory();
$gameModel = \Diplomacy\Models\Game::find($Game->id);
$gameEntity = $gameFactory->build($gameModel);
$userModel = \Diplomacy\Models\User::find($User->id);
$userEntity = $userModel->toEntity();
$currentMember = $User ? $gameEntity->members->byUser($userEntity) : null;

require_once 'board/OldChatbox.php';

if (!$Game->isPreGame())
{
    /** @var Chatbox $CB */
	$CB = $Game->Variant->OldChatbox();

	// Now that we have retrieved the latest messages we can update the time we last viewed the messages
	// Post messages we sent, and get the user we're speaking to
    $msgCountryID = $CB->findTab();

	$CB->postMessage($msgCountryID);
	$DB->sql_put("COMMIT");

	$forum = $CB->output($msgCountryID);

	unset($CB);

	libHTML::$footerScript[] = 'makeFormsSafe();';
}

$map = $Game->mapHTML();

/*
 * Now there is $orders, $form, and $map. That's all the HTML cached, now begin printing
 */

print '</div>';
print '<div class="content-bare content-board-header">';
print '<div class="boardHeader">'.$Game->contentHeader().'</div>';
print '</div>';
print '<div class="content content-follow-on variant'.$Game->Variant->name.'">';

// Now print the forum, map, orders, and summary
if ( isset($forum) )
{
	print $forum.'<div class="hr"></div>';
}

print $map.'<div class="hr"></div>';

if (isset($Orders))
{
	print $Orders.'<div class="hr"></div>';
}

echo $Game->summary();


if ($User->isModerator())
{
	$modActions=array();

	if (!$Game->isGameOver())
	{
		$modActions[] = libHTML::admincpType('Game',$Game->id);

		$modActions[] = libHTML::admincp('resetMinimumBet',array('gameID'=>$Game->id), l_t('Reset Min Bet'));
		$modActions[] = libHTML::admincp('togglePause',array('gameID'=>$Game->id), l_t('Toggle pause'));
		if($Game->isInNotProcessing())
		{
			$modActions[] = libHTML::admincp('setProcessTimeToNow',array('gameID'=>$Game->id), l_t('Process now'));
			$modActions[] = libHTML::admincp('setProcessTimeToPhase',array('gameID'=>$Game->id), l_t('Reset Phase'));
		}

		if ($User->isAdmin())
		{
			if ($Game->isCrashed()) {
                $modActions[] = libHTML::admincp('unCrashGames', array('excludeGameIDs' => ''), l_t('Un-crash all crashed games'));
            }
		}

		if (!$Game->isPreGame() && !$Game->isMemberInfoHidden())
		{
			$userIDs=implode('%2C',array_keys($Game->Members->ByUserID));
			$modActions[] = '<br /></br>'.l_t('Multi-check:');
			foreach($Game->Members->ByCountryID as $countryID=>$Member)
			{
				$modActions[] = '<a href="admincp.php?tab=Multi-accounts&aUserID='.$Member->userID.'" class="light">'.
					$Member->memberCountryName().'('.$Member->username.')</a>';
			}
		}
	}

	if($modActions)
	{
		print '<div class="hr"></div>';
		print '<p class="notice">';
		print implode(' - ', $modActions);
		print '</p>';
		print '<div class="hr"></div>';
	}
}

// TODO: Have this loaded up when the game object is loaded up
list($directorUserID) = $DB->sql_row("SELECT directorUserID FROM wD_Games WHERE id = ".$Game->id);
list($tournamentDirector, $tournamentCodirector) = $DB->sql_row("SELECT directorID, coDirectorID FROM wD_Tournaments t INNER JOIN wD_TournamentGames g ON t.id = g.tournamentID WHERE g.gameID = ".$Game->id);
if((isset($directorUserID) && $directorUserID == $User->id) || (isset($tournamentDirector) && $tournamentDirector == $User->id) || (isset($tournamentCodirector) && $tournamentCodirector == $User->id) )
{
	// This guy is the game director
	define('INBOARD', true);

	require_once(l_r("admin/adminActionsForms.php"));
}

print '</div>';

libHTML::footer();
