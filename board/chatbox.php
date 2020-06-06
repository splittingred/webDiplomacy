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

use \Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use \Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\GameMessage;
use \Diplomacy\Services\Games\MessagesService;

defined('IN_CODE') or die('This script can not be run by itself.');

require_once 'lib/gamemessage.php';

/**
 * The chat-box for the board. From the tabs to the messages to the send-box, also
 * takes responsibility for sending any messages it recieves.
 *
 * @package Board
 */
class Chatbox
{
    /** @var MessagesService $messagesService */
    protected $messagesService;

    public function __construct()
    {
        $this->messagesService = new MessagesService();
    }

    /**
	 * Find the tab which the user has requested to see, and return the country name. (Can also be 'Global')
	 *
	 * Will set the countryID as a session var, so will remember the countryID selected once even if not specified afterwards.
	 *
     * @param Game $game
     * @param Member $member
	 * @return string
	 */
	public function findTab(Game $game, Member $member = null)
	{
		$msgCountryID = 0;

		// Find which member's messages we're looking at
		if( isset($_REQUEST['msgCountryID']) )
		{
			$msgCountryID = (int)$_REQUEST['msgCountryID'];
		}
		elseif (isset($_SESSION[$game->id.'_msgCountryID']))
		{
			/*
			 * This should only be used when entering a board, while within the board the msgCountryID
			 * should be passed with REQUEST, or else problems arise with multiple tabs
			 */
			$msgCountryID = $_SESSION[$game->id.'_msgCountryID'];
		}
		$msgCountryID = (int)$msgCountryID;

		if ($msgCountryID <= 0 || $msgCountryID > $game->getCountryCount()) {
            $msgCountryID = 0;
        }

		// Enforce Global and Notes tabs when its not Regular or Rulebook press game, and not looking at
        // own messages
        $isMemberBanned = $member && $member->isBanned();
		$isSelf = $member && $member->country->id == $msgCountryID;
		if ((!$game->pressType->allowPrivateMessages() || $isMemberBanned) && !$isSelf) {
            $msgCountryID = 0;
        }

		$_SESSION[$game->id.'_msgCountryID'] = $msgCountryID;

		if (in_array($msgCountryID, $member->newMessagesFrom))
		{
			/*
			 * The countryID we are viewing has new messages, which we are about to view.
			 * Register the new messages as seen
			 */
            $memberModel = \Diplomacy\Models\Member::find($member->id);
            $memberModel->markMessageSeen($msgCountryID);
        }
		return $msgCountryID;
	}

    /**
     * Post a message to the given countryID, if there is one to be posted. Will also send messages as a
     * GameMaster if the user is a moderator which isn't joined into the game.
     *
     * @param int $msgCountryID The countryID to post to, may include 0 (Global)
     * @param Game $game
     * @param Member|null $member
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
	public function postMessage(int $msgCountryID, Game $game, Member $member = null)
	{
		global $app;
		$DB = $app->make('DB');

		if (!empty($_POST['newmessage']))
		{
			$newmessage = trim($_REQUEST['newmessage']);
            $this->messagesService->sendToCountry($game, $member, $msgCountryID, $newmessage);
		}

		# TODO: Move this to service
		if(isset($_REQUEST['MarkAsUnread']))
		{
			$DB->sql_put("UPDATE wD_Members SET newMessagesFrom = IF( (newMessagesFrom+0) = 0,'".$msgCountryID."', CONCAT_WS(',',newMessagesFrom,'".$msgCountryID."') )
						WHERE gameID = ".$game->id." AND countryID=".$member->country->id);
			$member->newMessagesFrom[] = $msgCountryID;
		}
	}

    /**
     * Output the chatbox HTML; output the tabs, then the information about the player we're talking to,
     * then the correspondance we have with the current msgCountryID at the moment, then the post-box for
     * new messages we want to send
     *
     * @param string $msgCountryID The id of the country/tab which we have open
     * @param Game $game
     * @param Member|null $member
     * @param \User $currentUser The current logged in user, if applicable
     * @return string The HTML for the chat-box
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
	public function output($msgCountryID, Game $game, Member $member = null, \User $currentUser = null)
	{
		$chatbox = '<a id="chatboxanchor"></a><a id="chatbox"></a>';

		// Print each user's tab
		if (isset($member))
			$chatbox .= $this->outputTabs($msgCountryID, $game, $member);

		// Create the chatbox

		// Print info on the user we're messaging
		// Are we viewing another user, or the global chatbox?

		$chatbox .= '<div class = "chatWrapper"><DIV class="chatbox '.(!isset($member)?'chatboxnotabs':'').'">
					<TABLE class="chatbox">
					<TR class="barAlt2 membersList">
					<TD>';

		if ($msgCountryID == Country::GLOBAL)
		{
			$memList = [];
			for ($countryID = 1; $countryID <= $game->getCountryCount(); $countryID++) {
                $memList[] = $game->members->byCountryId($countryID)->getRenderedUserName($game, $currentUser);
            }
			$chatbox .= '<div class="chatboxMembersList">'.implode(', ',$memList).'</div>';
		}
		elseif (!$member || !$member->isCountry($msgCountryID))
		{
		    // TODO: FINISH THIS
			$chatbox .= '';//$Game->Members->ByCountryID[$msgCountryID]->memberBar();
		}

		$chatbox .= '</TD></TR></TABLE></DIV>';

		// Print the messages in the chatbox
		$chatbox .= '<DIV id="chatboxscroll" class="chatbox"><TABLE class="chatbox">';

		$messages = $this->getMessages($msgCountryID, $game, $member, $currentUser);

		if ( $messages == "" )
		{
			$chatbox .= '<TR class="barAlt1"><td class="notice">
					'.l_t('No messages yet posted.').
					'</td></TR>';
		}
		else
		{
			$chatbox .= $messages;
		}

		$chatbox .= '</TABLE></DIV>';

        $currentUserIsSudo = $currentUser->isModerator()
            || $game->isDirector($currentUser->id)
            || $game->isTournamentDirector($currentUser->id)
            || $game->isTournamentCoDirector($currentUser->id);

        if (($msgCountryID == Country::GLOBAL && $currentUserIsSudo) || $this->messagesService->canSend($game, $member, $msgCountryID)) {
            \libHTML::$footerIncludes[] = l_j('message.js');

            $chatbox .= '<DIV class="chatbox">
					<form method="post" class="safeForm" action="message.php?gameID='.$game->id.'&amp;msgCountryID='.$msgCountryID.'" id="chatForm">
					<TABLE>
					<TR class="barAlt2">
						<TD class="left">
						'.(($msgCountryID == 0) ? '' : '
							<a href="#" onclick="document.markUnread.submit(); return false;" tabindex="3">Mark unread</a>
						').'
						</TD>
						<TD class="right" rowspan="2">
							<TEXTAREA id="sendbox" tabindex="1" NAME="newmessage" style="width:98% !important" width="100%" ROWS="5"></TEXTAREA>
						</TD>
					</TR>
					<TR class="barAlt2">
						<TD class="left send">
							<input type="hidden" name="formTicket" value="'.libHTML::formTicket().'" />
							<input type="submit" tabindex="2" class="form-submit" value="Send" name="Send" onclick="return false;" id="message-send"/><br/>
						</TD>
					</TR>
				</TABLE>
				</form>
				</DIV></div>'.
                (($msgCountryID == Country::GLOBAL) ? '' : '
						<form method="post" name="markUnread" class="safeForm" action="/board.php?gameID='.$game->id.'&amp;msgCountryID='.$msgCountryID.'#chatboxanchor">
							<input type="hidden" tabindex="2" value="" name="MarkAsUnread" />
						</form>
					');
		}
		else if ((string)$game->pressType == 'RulebookPress' && (string)$game->phase != 'Diplomacy' && $msgCountryID != Country::GLOBAL)  {
				$chatbox .= '
    <div class="chatbox">
    <TABLE><TR class="barAlt2"><TD class="center">
						<form method="post" name="markUnread" class="safeForm" action="/board.php?gameID='.$game->id.'&amp;msgCountryID='.$msgCountryID.'#chatboxanchor">
							<input type="hidden" tabindex="2" value="" name="MarkAsUnread" />
                        </form>
							<a href="#" onclick="document.markUnread.submit(); return false;" tabindex="3">Mark unread</a>
							</TD></TR>
    </TABLE>
	</div>';

		}
		else
		{
			$chatbox .= '</div>';
		}

		libHTML::$footerScript[] = '
			var cbs = $("chatboxscroll");

			cbs.scrollTop = cbs.scrollHeight;
			';

		// Don't focus the chatbox if the user is entering orders
		if ( isset($_REQUEST['msgCountryID']) )
			libHTML::$footerScript[] = '
				var sb = $("sendbox");
				if( sb != null && !Object.isUndefined(sb) ) {
					$("sendbox").focus();
				}
			';

		return $chatbox;
	}

    /**
     * Output the tabs which go on top of the chat-box, along with online notifications and message notifications
     * where applicable
     * @param int $msgCountryID The name of the countryID/tab which we have open
     * @param Game $game
     * @param Member|null $currentMember
     * @return string The HTML for the chat-box tabs
     */
	protected function outputTabs(int $msgCountryID, Game $game, Member $currentMember = null)
	{
		$tabs = '<div id="chatboxtabs" class="gamelistings-tabs">';

		for($countryID = 0; $countryID <= $game->getCountryCount(); $countryID++)
		{
		    $member = $game->members->byCountryId($countryID);

			// Do not allow country specific tabs for restricted press games.
            if (!$game->pressType->allowPrivateMessages()) continue;

			$hrefPrefix = '<a href="/games/'.$game->id.'/view?msgCountryID='.$member->country->id.'#chatboxanchor" '.
				'class="country'.$countryID.' '.( $msgCountryID == $countryID ? ' current"'
					: '" title="'.l_t('Open %s chatbox tab"',( $countryID == 0 ? 'the global' : $member->country->name."'s" )) ).'>';

			if ($currentMember->id == $member->id)
			{
				$tabs .= $hrefPrefix . 'Notes';
			}
			elseif ($member->isFilled())
			{
				$tabs .= $member->getRenderedCountryName($game, $currentMember->user);
			}
			else
			{
				$tabs .= $hrefPrefix . 'Global';
			}

			if ($msgCountryID != $countryID && in_array($countryID, $currentMember->newMessagesFrom) )
			{
				// This isn't the tab I am currently viewing, and it has sent me new messages
				$tabs .= ' '.libHTML::unreadMessages();
			} elseif ($msgCountryID == $countryID && isset($_REQUEST['MarkAsUnread'])) {
                // Mark as unread patch!
                $tabs .= ' ' . libHTML::unreadMessages();
            }

			$tabs .= '</a>';
		}

		$tabs .= '</div>';

		return $tabs;
	}

	protected function countryName(Game $game, int $countryID) {
		if ($countryID == 0)
			return 'Global';
		else
			return $game->variant->getCountryName($countryID);
	}

    /**
     * Retrieve and parse the messages which have been sent via this tab into an HTML table
     *
     * @param int $msgCountryID The name of the countryID/tab which we have open
     * @param Game $game
     * @param Member|null $currentMember
     * @param User $currentUser
     * @param int $limit
     * @return string The HTML for the messages we have sent/recieved
     */
	public function getMessages(int $msgCountryID, Game $game, Member $currentMember = null, \User $currentUser = null, int $limit = 50)
	{
		if (!isset($currentMember)) $msgCountryID = 0;

        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = GameMessage::forGame($game->id);

		if ($msgCountryID == -1) // 'All' ?
		{
		    $q->where('toCountryID', '=', 0);
		    if ($currentMember) {
		        $q->orWhere('fromCountryID', $currentMember->country->id);
                $q->orWhere('toCountryID', $currentMember->country->id);
            }
		}
		elseif ($msgCountryID == Country::GLOBAL) // Global
		{
			// Get all messages addressed to everyone
            $q->where('toCountryID', '=', 0);
		}
		else
		{
		    // To current user, from another country
		    $q->where(function ($query) use ($msgCountryID, $currentMember) {
		        $query->where('toCountryID', '=', $currentMember->country->id)
                      ->where('fromCountryID', '=', $msgCountryID);
            });
		    // From current user, to another country
		    $q->orWhere(function ($query) use ($msgCountryID, $currentMember) {
                $query->where('fromCountryID', '=', $currentMember->country->id)
                      ->where('toCountryID', '=', $msgCountryID);
            });
		}

        $q->orderBy('id', 'desc')
          ->limit($limit);

		return $this->renderMessages($msgCountryID, $q->get(), $game, $currentMember, $currentUser);
	}

	public function renderMessages(int $msgCountryID, $messages, Game $game, Member $currentMember = null, \User $currentUser = null)
	{
		$messagestxt = "";

		$alternate = false;
		/** @var \Diplomacy\Models\GameMessage $message */
        foreach ($messages as $message)
		{
			$alternate = !$alternate;

			$messagestxt .= '<TR class="replyalternate'.($alternate ? '1' : '2' ).
				' gameID'.$game->id.'countryID'.$message->fromCountryID.'">'.
				// Add gameID####countryID### to allow muted countries to be hidden
					'<TD class="left time">'.libTime::text($message->timeSent);

			$messagestxt .=  '</TD><TD class="right ';

			if (!empty($message->fromCountryID)) // GameMaster
			{
				// If the message isn't from the GameMaster color it in the countryID's color
				$messagestxt .= 'country'.$message->fromCountryID;
			}

			$messagestxt .= '">';

			if ($msgCountryID == -1 && $currentMember) // -1 = All
			{
				if ($currentMember->country->id == $message->fromCountryID)
					$fromtxt = l_t(', from <strong>you</strong>');
				elseif( 0==$message['fromCountryID'] )
					$fromtxt = l_t(', from <strong>Gamemaster</strong>');
				else
					$fromtxt = l_t(', from <strong>%s</strong>',l_t($this->countryName($game, $message->fromCountryID)));

				if ($currentMember->country->id == $message->toCountryID)
					$messagestxt .=  '('.l_t('To: <strong>You</strong>').$fromtxt.') - ';
				else
					$messagestxt .=  '('.l_t('To: <strong>%s</strong>',l_t($this->countryName($game, $message->toCountryID))).$fromtxt.') - ';
			}

			if ($message->turn < $game->currentTurn->id )
			{
				$messagestxt .= '<strong>'.$game->getTurnAsText($message->turn).'</strong>: ';
			}

			if ($currentMember && $message->fromCountryID == $currentMember->country->id )
				$message->message = '<span class="messageFromMe">'.$message->message.'</span>';

			$messagestxt .= $message->message.
					'</TD>
				</TR>';
		}
		return $messagestxt;
	}
}