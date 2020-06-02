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

defined('IN_CODE') or die('This script can not be run by itself.');

require_once(l_r('gamepanel/members.php'));

/**
 * The game panel class; it extends the Game class, which contains the information, with a set
 * of functions which display HTML giving info on the game and allowing certain interactions with it.
 *
 * This class is also extended to behave differently when viewed in a game board, or on the user's home
 * page. The plain class is used on the game-listings and profile page.
 *
 * The panelGame class has corresponding panelMembers and panelMember classes, which extend Members
 * and Member in similar ways.
 *
 * Nothing in panelGame will change the objects being displayed in any way, however they may provide
 * interfaces to do so (e.g. voting, leaving, joining), but other code like board.php will actually
 * act on any received form data; these classes are for display only.
 *
 * With a few exceptions all panel* functions return HTML strings. Also the convention is that if
 * HTML data is enclosed in a <div> it will leave its caller to create the div for it. So
 * '<div class="titleBar">'.$this->titleBar().'</div>' is seen, instead of titleBar() adding the div
 * itself.
 *
 * @package GamePanel
 */
class panelGame extends Game
{
	/**
	 * print the HTML for this game panel; header, members info, voting info, links
	 */
	public function summary() : string
	{
		print '
		<div class="gamePanel variant'.$this->Variant->name.'">
			'.$this->header().'
			'.$this->members().'
			'.$this->votes().'
			'.$this->links().'
			<div class="bar lastBar"> </div>
		</div>
		';
		return '';
	}

	/**
	 * Load panelMembers, instead of Members
	 */
	public function loadMembers() : void
	{
		$this->Members = $this->Variant->panelMembers($this);
	}

	/**
	 * The full bar with a notice about the game; used for game-over and game-starting details.
	 *
	 * @return string
	 */
	public function gameNoticeBar() : string
	{
		if ($this->isFinished()) {
			return $this->gameGameOverDetails();
		}
		elseif( $this->isPreGame() && count($this->Members->ByID) == count($this->Variant->countries))
		{
			if ($this->isLiveGame()) {
				return l_t('%s players joined; game will start at the scheduled time', count($this->Variant->countries));
			} else {
				return l_t('%s players joined; game will start on next process cycle', count($this->Variant->countries));
			}
		}
		elseif ($this->missingPlayerPolicy == 'Wait' && !$this->Members->isCompleted() && time() >= $this->processTime) {
			return l_t("One or more players need to complete their orders before this wait-mode game can go on");
		}
		return '';
	}

	public function pausedInfo() : string
	{
		return l_t('Paused').' <img src="'.l_s('images/icons/pause.png').'" title="'.l_t('Game paused').'" />';
	}

	/**
	 * The next-process data, depending on whether paused/crashed/finished/etc
	 *
	 * @return string
	 */
	public function gameTimeRemaining() : string
	{

		if ($this->isFinished()) {
			return '<span class="gameTimeRemainingNextPhase">' . l_t('Finished:') . '</span> ' . libTime::detailedText($this->processTime);
		}

		if ($this->processStatus == 'Paused') {
			return $this->pausedInfo();
		} elseif ($this->processStatus == 'Crashed') {
			return l_t('Crashed');
		}

		if (!isset($timerCount)) {
			static $timerCount = 0;
		}

		$timerCount++;

		if ($this->isPreGame()) {
			$buf = '<span class="gameTimeRemainingNextPhase">' . l_t('Start:') . '</span> ' . $this->processTimetxt() . ' (' . libTime::detailedText($this->processTime) . ')';
		} else {
			$buf = '<span class="gameTimeRemainingNextPhase">'.l_t('Next:').'</span> '. $this->processTimetxt().' ('.libTime::detailedText($this->processTime).')';
		}
		return $buf;
	}

	/**
	 * What circumstances did the game end in? Who won, etc
	 * @return string
	 */
	public function gameGameOverDetails() : string
	{
		if( $this->gameOver == 'Won' )
		{
			// TODO: replace with just getting last element
			$Winner = end($this->Members->ByStatus['Won']);
			return l_t('Game won by %s',$Winner->memberName());
		}
		elseif( $this->gameOver == 'Drawn' )
		{
			return l_t('Game drawn');
		}
	}

	/**
	 * Icons for the game, e.g. private padlock and featured star
	 * @return string
	 */
	public function gameIcons() : string
	{
		$buf = '';
		if( $this->pot > $this->misc->GameFeaturedThreshold )
			$buf .= '<img src="'.l_s('images/icons/star.png').'" alt="'.l_t('Featured').'" title="'.l_t('This is a featured game, one of the highest stakes games on the server!').'" /> ';

		if( $this->private )
			$buf .= '<img src="'.l_s('images/icons/lock.png').'" alt="'.l_t('Private').'" title="'.l_t('This is a private game; invite code needed!').'" /> ';

		return $buf;
	}

	public function phaseSwitchInfo() : string
	{
		$buf = '';
		
		if ($this->phase == 'Finished' or $this->phaseSwitchPeriod <= 0 or $this->nextPhaseMinutes == $this->phaseMinutes)
		{
			return $buf;
		}
			
		$buf .= '<div>Changing phase length: <span><strong>'.libTime::timeLengthText($this->nextPhaseMinutes * 60).'</strong> /phase</span></div>';
		if ($this->startTime > 0) 
		{
			$timeWhenSwitch = (($this->phaseSwitchPeriod * 60) + $this->startTime);

			if (time() >= $timeWhenSwitch) 
			{
				$buf .= '<div><strong> At: End Of Phase</strong></div>';
			} 
			else 
			{
				$buf .= '<div> In: <strong>'.libTime::remainingText($timeWhenSwitch).'</strong>' . ' (' . libTime::detailedText($timeWhenSwitch) . ')</div>';
			}
		}

		else 
		{
			$timeTillNextPhase = libTime::timeLengthText($this->phaseSwitchPeriod * 60);
			
			$buf .= '<div><span><strong>'.$timeTillNextPhase.'</strong> after game start</span></div></br>';	
		}
		
		
								
		return $buf;
	}

	/**
	 * The title bar, giving the vital game related data
	 *
	 * @return string
	 */
	public function titleBar() : string
	{
		$rightTop = '
			<div class="titleBarRightSide">
				<div>
				<span class="gameTimeRemaining">'.$this->gameTimeRemaining().'</span></div>'.
			'</div>';

		$rightMiddle = '<div class="titleBarRightSide">'.
				'<div>'.
					'<span class="gameHoursPerPhase">'.$this->gameHoursPerPhase().'</span>'.$this->phaseSwitchInfo().
				'</div>';
			

				
		$rightMiddle .= '</div>';
		
		$rightBottom = '<div class="titleBarRightSide">'.
					l_t('%s excused missed turn','<span class="excusedNMRs">'.$this->excusedMissedTurns.'</span>
					').
				'</div>';

		$date=' - <span class="gameDate">'.$this->datetxt().'</span>, <span class="gamePhase">'.l_t($this->phase).'</span>';

		$leftTop = '<div class="titleBarLeftSide">
				'.$this->gameIcons().
				'<span class="gameName">'.$this->titleBarName().'</span>';

		$leftBottom = '<div class="titleBarLeftSide"><div>
				'.l_t('Pot:').' <span class="gamePot">'.$this->pot.' '.libHTML::points().'</span>';

		$leftBottom .= $date.'</div>';
		
		$leftBottom .= '<div>'.$this->gameVariants().'</div>';

		$leftTop .= '</div>';
		$leftBottom .= '</div>';

		return '
			'.$rightTop.'
			'.$leftTop.'
			<div style="clear:both"></div>
			'.$rightMiddle.'
			'.$leftBottom.'
			<div style="clear:both"></div>
			'.$rightBottom.'
			<div style="clear:both"></div>';
	}

	public function gameVariants() : string
	{
		$alternatives=array();
		$alternatives[]=$this->Variant->link();

		if ( $this->pressType=='NoPress')
			$alternatives[]=l_t('No messaging');
		elseif( $this->pressType=='RulebookPress')
			$alternatives[]=l_t('Rulebook press');
		elseif( $this->pressType=='PublicPressOnly' )
			$alternatives[]=l_t('Public messaging only');
		
		if($this->playerTypes=='Mixed')
			$alternatives[]=l_t('Fill with Bots');

		if($this->playerTypes=='MemberVsBots')
			$alternatives[]=l_t('Bot Game');
		
		if( $this->anon=='Yes' )
			$alternatives[]=l_t('Anonymous players');

		$alternatives[]=$this->Scoring->longName();

		if( $this->drawType=='draw-votes-hidden')
			$alternatives[]=l_t('Hidden draw votes');

		if( $this->missingPlayerPolicy=='Wait' )
			$alternatives[]=l_t('Wait for orders');

		if ( $alternatives )
			return '<div class="titleBarLeftSide">
				<span class="gamePotType">'.implode(', ',$alternatives).'</span>
				</div>
			';
		else
			return '';
	}

	/**
	 * Hours per phase, whether the game is slow or fast etc
	 * @return string
	 */
	public function gameHoursPerPhase() : string
	{
		return l_t('<strong>%s</strong> /phase',libTime::timeLengthText($this->phaseMinutes*60));
	}

	/**
	 * The notifications list, not yet used, for showing notifications data related to a game within its game-panel
	 * @return string
	 */
	public function notificationsList() : string
	{
		return '';
	}

	/**
	 * Votes form data, only available in the board and if a member, so returns nothing here
	 * @return string
	 */
	public function votes() : string
	{
		return '';
	}

	/**
	 * The header; the vital game info and the vital notice bar
	 * @return string
	 */
	public function header() : string
	{
		$buf = '<div class="bar titleBar"><a name="gamePanel"></a>
				'.$this->titleBar().'
			</div>';

		$noticeBar = $this->gameNoticeBar();
		if ( $noticeBar )
			return $buf.'
				<div class="bar gameNoticeBar barAlt'.libHTML::alternate().'">
					'.$noticeBar.'
				</div>';
		else
			return $buf;
	}

	/**
	 * Members data; info about each member is given surrounded by the occupation-bar
	 * @return string
	 */
	public function members() : string
	{
		return $this->renderer->render('games/members/summaryList.twig',[
			'moderator_sees_member_info' => $this->moderatorSeesMemberInfo(),
			'occupation_bar' => $this->Members->occupationBar(),
			'members_list' => $this->Members->membersList(),
		]);
	}

	/**
	 * The links allowing players to join/view games and see the archive data
	 * @return string
	 */
	public function links() : string
	{
		return '
			<div class="bar enterBar">
				<div class="enterBarJoin">
					'.$this->joinBar().'
				</div>
				<div class="enterBarOpen">
					'.$this->openBar().'
				</div>
				<div style="clear:both"></div>
			</div>
			';
	}

	/**
	 * Links to the games archived data, maps/orders/etc
	 * @return string
	 */
	public function archiveBar() : string
	{
		return $this->renderer->render('games/board/archive_bar.twig', [
			'id' => $this->id,
		]);
	}

	/**
	 * The invite code box for joining private games
	 * @return string
	 */
	private static function passwordBox() : string
	{
		return ' <span class="gamePasswordBox"><label>'.l_t('Invite Code:').'</label> <input type="password" name="gamepass" size="10" /></span> ';
	}

	/**
	 * A bar with form buttons letting you join/leave a game
	 * @return string
	 */
	public function joinBar() : string
	{
		if ( $this->Members->isJoined() )
		{
			if ( $this->phase == 'Pre-game' )
			{
				$reason=$this->Members->cantLeaveReason();

				if($reason)
					return l_t("(Can't leave game; %s.)",$reason);
				else
					return '<form onsubmit="return confirm(\''.l_t('Are you sure you want to leave this game?').'\');" method="post" action="board.php?gameID='.$this->id.'"><div>
					<input type="hidden" name="formTicket" value="'.libHTML::formTicket().'" />
					<input type="submit" name="leave" value="'.l_t('Leave game').'" class="form-submit" />
					</div></form>';
			}
			else
				return '';
		}
		else
		{
			$buf = '';

			if ($this->minimumReliabilityRating > 0 && $this->user->type['User'])
			{
				$buf .= l_t('Required Reliability: <span class="%s">%s%%</span><br/>',
					($this->user->reliabilityRating < $this->minimumReliabilityRating ? 'Austria' :'Italy'),
					($this->minimumReliabilityRating));
			}

			if ( $this->isJoinable() )
			{
				if( $this->minimumBet <= 100 && !$this->user->type['User'] && !$this->private )
					return l_t('A newly registered account can join this game; '.
						'<a href="register.php" class="light">register now</a> to join.');

				$question = l_t('Are you sure you want to join this game?').'\n\n';
				if ( $this->isLiveGame() )
				{
					$question .= l_t('The game will start at the scheduled time even if all %s players have joined.', count($this->Variant->countries));
				}
				else
				{
					$question .= l_t('The game will start when all %s players have joined.', count($this->Variant->countries));
				}

				if ($this->user->reliabilityRating >= $this->minimumReliabilityRating)
				{
					if (!($this->user->userIsTempBanned()))
					{
						$buf .= '<form onsubmit="return confirm(\''.$question.'\');" method="post" action="board.php?gameID='.$this->id.'"><div>
							<input type="hidden" name="formTicket" value="'.libHTML::formTicket().'" />';

						if( $this->phase == 'Pre-game' )
						{
							$buf .= l_t('Bet to join: %s: ','<em>'.$this->minimumBet.libHTML::points().'</em>');
						}
						else
						{
							$buf .= $this->Members->selectCivilDisorder();
						}

						if ( $this->private )
							$buf .= '<br />'.self::passwordBox();

						$buf .= ' <input type="submit" name="join" value="'.l_t('Join').'" class="form-submit" />';

						$buf .= '</div></form>';
					}
				}
			}
			if ($this->user->type['User'])
			{
				if ($this->user->userIsTempBanned())
				{
					$buf .= '<span style="font-size:75%;">(Due to a temporary ban you cannot join games.)</span>';
				}
				elseif ($this->user->reliabilityRating < $this->minimumReliabilityRating)
				{
					$buf .= '<span style="font-size:80%;">(You are not reliable enough to join this game.)</span>';
				}
				elseif ($this->user->points < $this->minimumBet)
				{
					$buf .= '<span style="font-size:80%;">(You have too few points to join this game.)</span>';
				}
			}
			if( $this->user->type['User'] && $this->phase != 'Finished')
			{
				$buf .= '<form method="post" action="redirect.php">'
				       .'<input type="hidden" name="gameID" value="'.$this->id.'">';
				if( ! $this->watched() ) {
					$buf .= '<input style="margin-top: 0.5em;" type="submit" title="'.l_t('Adds this game to the watched games list on your home page, and subscribes you to game notifications').'" '
					       .'class="form-submit" name="watch" value="'.l_t('Spectate game').'">';
				} else {
					$buf .= '<input type="submit" title="'.l_t('Removes this game from the watch list on your home page, and unsubscribes you from game notifications').'" '
						       .'class="form-submit" name="unwatch" value="'.l_t('Stop spectating game').'">';
				}
				$buf .= '</form>';
			}
		}

		return $buf;
	}

	/**
	 * A bar with a button letting people view the game
	 * @return string
	 */
	public function openBar() : string
	{
		if( !$this->Members->isJoined() && $this->phase == 'Pre-game' )
			return '';

		return '<a href="board.php?gameID='.$this->id.'#gamePanel">'.
			l_t($this->Members->isJoined()?'Open':'View').'</a>';

		return '<form method="get" action="board.php#gamePanel"><div>
			<input type="hidden" name="gameID" value="'.$this->id.'" />
			<input type="submit" value="" class="form-submit" />
			</div></form>';
	}
}