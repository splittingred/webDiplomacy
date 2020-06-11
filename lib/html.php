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

// No defined() check here; this may be called before header.php

/**
 * A collection of functions which output HTML and manage the main body and layout of the menu,
 * notification bar, and content.
 *
 * @package Base
 */

class libHTML
{
	public static function pageTitle($title, $description=false)
	{
		return '<div class="content-bare content-board-header content-title-header">
					<div class="pageTitle barAlt1">
						'.$title.'
					</div>
					<div class="pageDescription">
						'.$description.'
					</div>
				</div>
				<div class="content content-follow-on">';
	}

	/**
	 * The style which prevents an element from displaying (usually cached HTML to be displayed via JS)
	 * @var string
	 */
	public static $hideStyle='display:none;';

	/**
	 * Print a webDiplomacy page break, where the content block ends and
	 * starts again leaving a gap.
	 */
	static public function pagebreak()
	{
		print '</div><div class="content">';
	}

	/**
	 * The logged-on icon
	 * @return string
	 */
	static function loggedOn($userID)
	{
		return '<img style="'.self::$hideStyle.'" class="userOnlineImg" userID="'.$userID.'" src="'.l_s('images/icons/online.png').'" alt="'.
			l_t('Online').'" title="'.l_t('User currently logged on').'" />';
	}

	static function platinum()
	{
		return ' <img src="'.l_s('images/icons/platinum.png').'" alt="(P)" title="'.l_t('Donator - platinum').'" />';
	}

	static function gold()
	{
		return ' <img src="'.l_s('images/icons/gold.png').'" alt="(G)" title="'.l_t('Donator - gold').'" />';
	}

	static function silver()
	{
		return ' <img src="'.l_s('images/icons/silver.png').'" alt="(S)" title="'.l_t('Donator - silver').'" />';
	}

	static function bronze()
	{
		return ' <img src="'.l_s('images/icons/bronze.png').'" alt="(B)" title="'.l_t('Donator - bronze').'" />';
	}

	static function service()
	{
		return ' <img src="'.l_s('images/icons/service.png').'" alt="(P)" title="'.l_t('Service Award').'" />';
	}

	static function owner()
	{
		return ' <img src="'.l_s('images/icons/owner.png').'" alt="(P)" title="'.l_t('Site Co-Owner').'" />';
	}

	static function adamantium()
	{
		return ' <img src="'.l_s('images/icons/adamantium.png').'" alt="(P)" title="'.l_t('Donator - adamantium').'" />';
	}

	static function goldStar()
	{
		return ' <img height="16" width="16" src="'.l_s('images/icons/GoldStar.png').'" alt="(G)" title="'.l_t('1st Place').'" />';
	}

	static function silverStar()
	{
		return ' <img height="16" width="16" src="'.l_s('images/icons/SilverStar.png').'" alt="(S)" title="'.l_t('2nd Place').'" />';
	}

	static function bronzeStar()
	{
		return ' <img height="16" width="16" src="'.l_s('images/icons/BronzeStar.png').'" alt="(B)" title="'.l_t('3rd Place').'" />';
	}

	/**
	 * The points icon
	 * @return string
	 */
	static function points()
	{
		return ' <img src="'.l_s('images/icons/points.png').'" alt="D" title="'.l_t('webDiplomacy points').'" />';
	}

	static function forumMessage($threadID, $messageID)
	{
		return '<a style="'.self::$hideStyle.'" class="messageIconForum" threadID="'.$threadID.'" messageID="'.$messageID.'" href="forum.php?threadID='.$threadID.'#'.$messageID.'">'.
		'<img src="'.l_s('images/icons/mail.png').'" alt="'.l_t('New').'" title="'.l_t('Unread messages!').'" />'.'</a> ';
	}

	static function forumParticipated($threadID)
	{
		return '<a style="'.self::$hideStyle.'" class="participatedIconForum" threadID="'.$threadID.'" href="forum.php?threadID='.$threadID.'#'.$threadID.'">'.
			'<img src="'.l_s('images/icons/star.png').'" alt="'.l_t('Participated').'" title="'.l_t('You have participated in this thread.').'" />'.'</a> ';
	}

	/**
	 * The icon to mute an unmuted player, optionally with link
	 * @param $url URL to link to
	 * @return string
	 */
	static function unmuted($url=false)
	{
		$buf = '';
		if($url) $buf .= '<a onclick="return confirm(\''.l_t("Are you sure you want to mute the messages from this player?").'\');" href="'.$url.'">';
		$buf .= '<img src="'.l_s('images/icons/unmute.png').'" alt="'.l_t('Mute player').'" title="'.l_t('Mute player').'" />';
		if($url) $buf .= '</a>';
		return $buf;
	}

	/**
	 * The icon to unmute an muted player, optionally with link
	 * @param $url URL to link to
	 * @return string
	 */
	static function muted($url=false)
	{
		$buf = '';
		if($url) $buf .= '<a href="'.$url.'">';
		$buf .= '<img src="'.l_s('images/icons/mute.png').'" alt="'.l_t('Muted. Click to un-mute.').'" title="'.l_t('Muted. Click to un-mute.').'" />';
		if($url) $buf .= '</a>';
		return $buf;
	}

	/**
	 * The unread messages icon, optionally with link
	 * @param $url URL to link to
	 * @return string
	 */
	static function unreadMessages($url=false)
	{
		$buf = '';
		if($url) $buf .= '<a href="'.$url.'">';
		$buf .= '<img src="'.l_s('images/icons/mail.png').'" alt="'.l_t('Unread message').'" title="'.l_t('Unread message').'" />';
		if($url) $buf .= '</a>';
		return $buf;
	}

	/**
	 * The maybe read messages icon, optionally with link
	 * @param $url URL to link to
	 * @return string
	 */
	static function maybeReadMessages($url=false)
	{
		$buf = '';
		if($url) $buf .= '<a href="'.$url.'">';
		$buf .= '<img src="'.l_s('images/icons/mail_faded.png').'" alt="'.l_t('Recent message').'" title="'.l_t('Recent message').'" />';
		if($url) $buf .= '</a>';
		return $buf;
	}

	public static function serveImage($filename, $contentType='image/png')
	{
		if ( ob_get_contents() != "" ) { die(); }

		header('Content-Length: '.filesize($filename));
		header('Content-Type: '.$contentType);

		print file_get_contents($filename);

		if( DELETECACHE ) {	unlink($filename); }

		die();
	}

	/**
	 * An external link icon
	 * @return string
	 */
	static function link()
	{
		return '<img src="'.l_s('images/historyicons/external.png').'" alt="'.l_t('Link').'" title="'.l_t('Click this to follow the link').'" />';
	}

	/**
	 * Var to alternate back and forth 1,2,1,2 to make things clearer
	 * @var int
	 */
	static $alternate=1;

	/**
	 * Alternates $alternate, and returns it
	 * @return int
	 */
	static function alternate()
	{
		self::$alternate = 3-self::$alternate;
		return self::$alternate;
	}

	/**
	 * Keeps track of whether first() has been called
	 * @var boolean
	 */
	static $first=true;

	/**
	 * Returns 'first' the first time it's called, and nothing from then on until $first is set to true.
	 * @return string
	 */
	static public function first()
	{
		if ( self::$first )
		{
			self::$first = false;
			return 'first';
		}
	}

	/**
	 * Creates a form ticket in the user's session, to make sure that a form is submitted only once.
	 * Embed the returned ticket into an <input type="hidden" name="formTicket" />.
	 *
	 * @return int
	 */
	static public function formTicket()
	{
		if ( !isset($_SESSION['formTickets']) )
			$_SESSION['formTickets'] = array();

		do {
			$ticket = rand(1,999999);
		} while ( isset($_SESSION['formTickets'][$ticket]) );

		$_SESSION['formTickets'][$ticket] = true;

		return $ticket;
	}

	/**
	 * Checks the submitted formTicket, that it exists, and that it is valid.
	 *
	 * @return boolean True if valid, false otherwise
	 */
	static public function checkTicket()
	{
		if( isset($_SESSION['formTickets']) && isset($_REQUEST['formTicket']) && isset($_SESSION['formTickets'][$_REQUEST['formTicket']]) )
		{
			unset($_SESSION['formTickets'][$_REQUEST['formTicket']]);
			return true;
		}
		else
			return false;
	}

	/**
	 * A link to an admin control panel action
	 *
	 * @param string $actionName The name of the action
	 * @param array $args The args in a $name=>$value array
	 * @param string $linkName The name to give the link, the URL is returned if no linkName is given
	 * @param boolean $confirm Boolean to determine whether the action needs javascript confirmation
	 * @return string A link URL or an <a href>
	 */
	static function admincp($actionName, $args=null, $linkName=null,$confirm=false)
	{
		$output = 'admincp.php?tab=Control%20Panel&amp;actionName='.$actionName;

		if( is_array($args) )
			foreach($args as $name=>$val)
			{
				if ( $name == 'gameID' )
					$output .= '&amp;globalGameID='.$val;
				elseif ( $name == 'userID' )
					$output .= '&amp;globalUserID='.$val;
				elseif ( $name == 'postID' )
					$output .= '&amp;globalPostID='.$val;

				$output .= '&amp;'.$name.'='.$val;
			}

		$output .= '#'.$actionName;

		if($linkName)
			return '<a href="'.$output.'" '
			      .($confirm ? 'onclick="return confirm(\''.$linkName.': '.l_t('Please confirm this action.').'\')"' :'')
			      .' class="light">'.$linkName.'</a>';
		else
			return $output;
	}

	public static function threadLink($postID)
	{
        global $app;
        $DB = $app->make('DB');

		$postID = (int)$postID;

		list($toID) = $DB->sql_row("SELECT toID FROM wD_ForumMessages WHERE id=".$postID);

		if( $toID == null || $toID == 0 )
			$toID = $postID;

		return '<a href="forum.php?threadID='.$toID.'#'.$postID.'">'.l_t('Go to thread').'</a>';
	}
	static function admincpType($actionType, $id)
	{
		return '<a href="admincp.php?tab=Control%20Panel&amp;global'.$actionType.'ID='.$id.'#'.strtolower($actionType).'Actions">
			'.l_t('View %s admin-actions',l_t(strtolower($actionType))).'</a>';
	}

	/**
	 * Wipe everything done so far, output a notice and end the script. Can be run in
	 * the event of errors.
	 *
	 * @param string $title The title to display
	 * @param string $message The message/notice to show
	 */
	static public function notice($title, $message)
	{
		ob_clean();

		libHTML::starthtml($title);

		print '<div class="content-notice"><p>'.$message.'</p></div>';

		print '</div>';
		libHTML::footer();
	}

	/**
	 * Print an error message and end the script
	 *
	 * @param string $message
	 */
	static public function error($message)
	{
		global $app;
        $Misc = $app->make('Misc');

		if ( !isset($Misc) )
		{
			die('<html><head><title>'.l_t('webDiplomacy fatal error').'</title></head>
				<body><p>'.l_t('Error occurred during script startup, usually a result of inability to connect to the database:').'</p>
				<p>'.$message.'</p></body></html>');
		}

		if(!defined('ERROR'))
			define('ERROR',true);

		self::notice(l_t('Error'), $message);
	}

	/**
	 * Name of the script filename which was requested e.g. ajax.php, set in starthtml()
	 * @var unknown_type
	 */
	private static $scriptname;

	/**
	 * The first HTML to be output; the various header tags, before anything is visible
	 *
	 * @param string $title The title of the page
	 * @return string The pre-body HTML
	 */
	static public function prebody ( $title )
	{
		require_once(l_r('global/definitions.php'));

		global $app;
		$renderer = $app->make('renderer');
		return $renderer->render('common/layout/header/head.twig', [
            'title' => $title,
            'css_version' => CSSVERSION,
            'js_version' => JSVERSION,
			'variants' => \Config::$variants,
        ]);
	}

	/**
	 * Print the HTML which comes before the main content; title, menu, notification bar.
	 *
	 * @param string|bool[optional] $title If a string is given it will be used as the page title
     * @param bool $echo
	 */
	static public function starthtml($title = false, $echo = true) : string
	{
		global $app;
        $renderer = $app->make('renderer');
        $User = $app->make('user');

		self::$scriptname = $scriptname = basename($_SERVER['PHP_SELF']);

		$pages = self::pages();

		if (isset($User) and ! isset($pages[$scriptname]))
		{
			die(l_t('Access to this page denied for your account type.'));
		}

		$bannedMessages = '';
		if (isset($User) && $User->userIsTempBanned() )
		{
			if ($User->tempBanReason != 'System' && $User->tempBanReason != '')
			{
				$bannedMessages = $renderer->render('common/users/banned/custom.twig', [
					'remaining_time' => libTime::remainingText($User->tempBan),
					'reason' => $User->tempBanReason,
					'moderator_email' => \Config::$modEMail,
				]);
			}
			else if (($User->tempBan - time() ) > (60*60*24*180))
			{
				$bannedMessages = $renderer->render('common/users/banned/year.twig', [
					'moderator_email' => \Config::$modEMail,
				]);
			}
			else
			{
				$bannedMessages = $renderer->render('common/users/banned/system.twig', [
					'moderator_email' => \Config::$modEMail,
					'remaining_time' => libTime::remainingText($User->tempBan),
				]);
			}
		}

		$gameNotification = is_object($User) && $User->isAuthenticated() ? self::gameNotifyBlock() : '';

		$output = $renderer->render('common/layout/header.twig', [
			'head' => self::prebody($title===FALSE ? l_t($pages[$scriptname]['name']) : $title),
			'menu' => self::menu(),
			'global_notices' => self::globalNotices(),
			'banned_messages' => $bannedMessages,
			'game_notification' => $gameNotification
		]);
		if ($echo) {
		    echo $output;
        }
        return $output;
	}

	/**
	 * The server-wide notices, displayed at the top of the page if enabled, and defined in config.php
	 *
	 * @return string
	 */
	static private function globalNotices()
	{
		global $app;
		$User = $app->make('user');
		$DB = $app->make('DB');
        $Misc = $app->make('Misc');
		$notice=array();
		if ($Misc->Maintenance && isset($User) && $User->isAdmin())
		{
			/*
			 * If the user is regular they are being shown the message as part of the main page,
			 * no need to add it as a notice except for admins (who might be wondering why noone
			 * else is online)
			 */
			$notice[]='Server in maintenance/development mode; only admins can interact with
				it until it is <a href="admincp.php?tab=Control Panel#maintenance">turned off</a>.';
		}

		if ( $Misc->Panic )
		{
			list($contents) = $DB->sql_row("SELECT message FROM wD_Config WHERE name = 'Panic'");
			$notice[]=$contents;
		}

		if ( $Misc->Notice )
		{
			list($contents) = $DB->sql_row("SELECT message FROM wD_Config WHERE name = 'Notice'");
			$notice[]=$contents;
		}

		if ( ( time() - $Misc->LastProcessTime ) > Config::$downtimeTriggerMinutes*60 )
			$notice[] = l_t("The last process time was over %s minutes ".
				"ago (at %s); the server ".
				"is not processing games until the cause is found and games are given extra time.",
				Config::$downtimeTriggerMinutes,libTime::text($Misc->LastProcessTime));

		if ( $notice )
			return '<div class="content-notice"><p class="notice">'.implode('</p><div class="hr"></div><p class="notice">',$notice).'</p></div>';
		else
			return '';
	}

	/**
	 * The notification block HTML, containing links to games which need
	 * the user's attention.
	 *
	 * @return string The notification block HTML
	 */
	static public function gameNotifyBlock()
	{
		global $app;
		$DB = $app->make('DB');
        $User = $app->make('user');

		$tabl = $DB->sql_tabl(
			"SELECT g.id, g.variantID, g.name, g.phase, m.orderStatus, m.countryID, (m.newMessagesFrom+0) as newMessagesFrom, g.processStatus
			FROM wD_Members m
			INNER JOIN wD_Games g ON ( m.gameID = g.id )
			WHERE m.userID = ".$User->id."
				AND ( ( NOT m.orderStatus LIKE '%Ready%' AND NOT m.orderStatus LIKE '%None%' AND g.phase != 'Finished' ) OR NOT ( (m.newMessagesFrom+0) = 0 ) ) ".
				( ($User->userIsTempBanned()) ? "AND m.status != 'Left'" : "" ) // ignore left games of temp banned user who are banned from rejoining
				." ORDER BY  g.processStatus ASC, g.processTime ASC");
		$gameIDs = array();
		$notifyGames = array();
		while ( $game = $DB->tabl_hash($tabl) )
		{
			$id = (int)$game['id'];
			$gameIDs[] = $id;
			$notifyGames[$id] = $game;
		}

		$gameNotifyBlock = '';

		if ( $User->notifications->PrivateMessage and ! isset($_REQUEST['notices']))
		{
			$gameNotifyBlock .= '<span class=""><a href="index.php?notices=on">'.
				l_t('PM').' <img src="'.l_s('images/icons/mail.png').'" alt="'.l_t('New private messages').'" title="'.l_t('New private messages!').'" />'.
				'</a></span> ';
		}

		if( isset(Config::$customForumURL) ) 
		{
			// We are using a PHPBB install; pull private messages from the phpBB install for this user
			$tabl = $DB->sql_tabl(
			"SELECT p.msg_id, p.pm_new, p.pm_unread, fromm.webdip_user_id, fromU.username, fromU.points, fromU.type
				FROM phpbb_privmsgs_to p
				INNER JOIN phpbb_users toU ON p.user_id = toU.user_id
				INNER JOIN phpbb_users fromm ON fromm.user_id = p.author_id
				INNER JOIN wD_Users fromU ON fromU.Id = fromm.webdip_user_id
				WHERE (pm_new = 1 OR pm_unread = 1) AND toU.webdip_user_id = ".$User->id);
			while($row_hash = $DB->tabl_hash($tabl)) 
			{
				$profile_link = $row_hash['username'];
				$profile_link.=' ('.$row_hash['points'].libHTML::points().User::typeIcon($row_hash['type']).')';

				$gameNotifyBlock .= '<span class=""><a href="'.Config::$customForumURL.'ucp.php?i=pm&mode=view&p='.$row_hash['msg_id'].'">'.
						l_t('PM from %s',$profile_link).' <img src="'.l_s('images/icons/mail.png').'" alt="'.l_t('New private message').'" title="'.l_t('New private message!').'" />'.
						'</a></span> ';
			}
		}

		foreach ( $gameIDs as $gameID )
		{
			$notifyGame = $notifyGames[$gameID];
			require_once(l_r('objects/basic/set.php'));

			// Games that are finished should show as 'no orders'
			if ( $notifyGame['phase'] != 'Finished') 
			{
					$notifyGame['orderStatus'] = new setMemberOrderStatus($notifyGame['orderStatus']);
			} 
			else 
			{
					$notifyGame['orderStatus'] = new setMemberOrderStatus('None');
			}

			// Don't print the game if we're looking at it.
			if ( isset($_REQUEST['gameID']) and $_REQUEST['gameID'] == $gameID )
				continue;

			$gameNotifyBlock .= '<span class="variant'.Config::$variants[$notifyGame['variantID']].'">'.
				'<a gameID="'.$gameID.'" class="country'.$notifyGame['countryID'].'" href="board.php?gameID='.$gameID.'">'.
				$notifyGame['name'];

			if ( $notifyGame['processStatus'] == 'Paused' )
				$gameNotifyBlock .= '-<img src="'.l_s('images/icons/pause.png').'" alt="'.l_t('Paused').'" title="'.l_t('Game paused').'" />';

			$gameNotifyBlock .= ' ';

			$gameNotifyBlock .= $notifyGame['orderStatus']->icon();
			if ( $notifyGame['newMessagesFrom'] )
				$gameNotifyBlock .= '<img src="'.l_s('images/icons/mail.png').'" alt="'.l_t('New messages').'" title="'.l_t('New messages!').'" />';

			$gameNotifyBlock .= '</a></span> ';
		}
		return $gameNotifyBlock;
	}

	/**
	 * Return an array of links, along with their names, who can view them, and
	 * whether they appear in the menu.
	 *
	 * @return array
	 */
	static public function pages()
	{
        global $app;
        $User = $app->make('user');

		$allUsers = array('Guest','User','Moderator','Admin');
		$loggedOnUsers = array('User','Moderator','Admin');

		$links=array();

		// Items displayed in the menu
		$links['index.php']=array('name'=>'Home', 'inmenu'=>TRUE, 'title'=>"See what's happening");

		if( isset(Config::$customForumURL) ) 
		{
			$links[Config::$customForumURL]=array('name'=>'Forum', 'inmenu'=>TRUE, 'title'=>"The forum; chat, get help, help others, arrange games, discuss strategies");
			$links['forum.php']=array('name'=>'Old Forum', 'inmenu'=>false, 'title'=>"The old forum; chat, get help, help others, arrange games, discuss strategies");
		} 
		else 
		{
			$links['forum.php']=array('name'=>'Forum', 'inmenu'=>TRUE, 'title'=>"The forum; chat, get help, help others, arrange games, discuss strategies");
		}
		
		$links['games/search']=array('name'=>'Games', 'inmenu'=>TRUE, 'title'=>"Game listings; a searchable list of the games on this server");

		if (is_object($User))
		{
			if( !$User->type['User'] )
			{
				$links['/users/login']=array('name'=>'Log on', 'inmenu'=>false, 'title'=>"Log onto webDiplomacy using an existing user account");
				$links['/users/register']=array('name'=>'Register', 'inmenu'=>TRUE, 'title'=>"Make a new user account");
			}
			else
			{
				$links['/users/login']=array('name'=>'Log off', 'inmenu'=>false, 'title'=>"Log onto webDiplomacy using an existing user account");
				$links['gamecreate.php']=array('name'=>'New game', 'inmenu'=>TRUE, 'title'=>"Start up a new game");
				$links['detailedSearch.php']=array('name'=>'Search', 'inmenu'=>TRUE, 'title'=>"advanced search of users and games");
				$links['usercp.php']=array('name'=>'Settings', 'inmenu'=>TRUE, 'title'=>"Change your user specific settings");
			}
		}

		$links['help']=array('name'=>'Help/Donate', 'inmenu'=>TRUE, 'title'=>'Get help and information; guides, intros, FAQs, stats, links');

		// Items not displayed on the menu
		$links['map.php']=array('name'=>'Map', 'inmenu'=>FALSE);
		$links['help/faq']=array('name'=>'FAQ', 'inmenu'=>FALSE);
		$links['help/contact']=array('name'=>'Contact Info', 'inmenu'=>FALSE);
		$links['help/contact-direct']=array('name'=>'Contact Us', 'inmenu'=>FALSE);
		$links['help/donations']=array('name'=>'Donations', 'inmenu'=>FALSE);
		$links['tournaments.php']=array('name'=>'Tournaments', 'inmenu'=>FALSE);
		$links['tournamentManagement.php']=array('name'=>'Manage Tournaments', 'inmenu'=>FALSE);
		$links['help/rules']=array('name'=>'Rules', 'inmenu'=>FALSE);
		$links['help/recent-changes']=array('name'=>'Recent changes', 'inmenu'=>FALSE);
		$links['intro']=array('name'=>'Intro', 'inmenu'=>FALSE);
		$links['credits.php']=array('name'=>'Credits', 'inmenu'=>FALSE);
		$links['board.php']=array('name'=>'Board', 'inmenu'=>FALSE);
		$links['profile.php']=array('name'=>'Profile', 'inmenu'=>FALSE);
		$links['help/points']=array('name'=>'Points', 'inmenu'=>FALSE);
		$links['stats/hall-of-fame']=array('name'=>'Hall of fame', 'inmenu'=>FALSE);
		$links['help/developers']=array('name'=>'Developer info', 'inmenu'=>FALSE);
		$links['datc.php']=array('name'=>'DATC', 'inmenu'=>FALSE);
		$links['variants/list']=array('name'=>'Variants', 'inmenu'=>FALSE);
		$links['adminInfo.php']=array('name'=>'Admin Info', 'inmenu'=>FALSE);
		$links['tournaments/info']=array('name'=>'Tournament Info', 'inmenu'=>FALSE);
		$links['/tournaments/{:id}']=array('name'=>'Tournament Scoring', 'inmenu'=>FALSE);
		$links['tournamentRegistration.php']=array('name'=>'Tournament Registration', 'inmenu'=>FALSE);
		$links['botgamecreate.php']=array('name'=>'New Bot Game', 'inmenu'=>TRUE, 'title'=>"Start up a new bot game");

		if ( is_object($User) )
		{
			if ($User->isAdmin() or $User->isModerator())
			{
				$links['profile.php']=array('name'=>'Find user', 'inmenu'=>true);  // Overrides the previous one with one that appears in the menu
				$links['admincp.php']=array('name'=>'Admin CP', 'inmenu'=>true);
			}
			$links['gamemaster.php']=array('name'=>'GameMaster', 'inmenu'=>FALSE);
		}
		return $links;
	}

	/**
	 * Prints the logo, welcome text and menu.
	 *
	 * @return string The logo, welcome text and menu HTML
	 */
	static public function menu()
	{
		global $app;
        $User = $app->make('user');
		$renderer = $app->make('renderer');
		$authenticated = $User && $User->isAuthenticated();

        if (!$authenticated) {
            $menu = $renderer->render('common/layout/menu/unauthenticated.twig');
        } else {
            $menu = $renderer->render('common/layout/menu/authenticated.twig',[
                'user' => $User,
                'is_admin' => $User->isAdmin(),
                'is_moderator' => $User->isModerator(),
                'admin_user_switch' => defined('AdminUserSwitch'),
            ]);
        }
		return $renderer->render('common/layout/menu/menu.twig', [
		    'menu' => $menu,
            'authenticated' => $authenticated,
            'user_profile_link' => $User ? $User->profile_link(true) : '',
            'admin_user_switch' => defined('AdminUserSwitch'),
        ]);
	}

	/**
	 * Output the footer HTML and call the close() function to perform final clean-ups. If $DB and $User
	 * are available then the script has ended successfully, and some statistics around outputted.
     *
     * @param boolean echo
     * @return string
	 */
	static public function footer($echo = true) : string
	{
        global $app;
        $renderer = $app->make('renderer');
		$output = $renderer->render('common/layout/footer.twig', [
			'stats' => self::footerStats(),
			'webdiplomacy_version' => number_format(VERSION/100,2),
			'moderator_email' => \Config::$modEMail,
			'footer_scripts' => self::footerScripts(),
		]);
        if ($echo) {
            echo $output;
            close();
        }
        return $output;
	}

	private static function footerStats()
	{
		global $app;
        $renderer = $app->make('renderer');
        $User = $app->make('user');
        $Misc = $app->make('Misc');

		return $renderer->render('common/layout/footer/stats.twig',[
			'logged_on' 		=> (int)$Misc->OnlinePlayers,
			'playing' 			=> (int)$Misc->ActivePlayers,
			'registered' 		=> (int)$Misc->TotalPlayers,
			'pages_served' 		=> (int)$Misc->Hits,
			'games_starting' 	=> (int)$Misc->GamesNew,
			'games_open' 		=> (int)$Misc->GamesOpen,
			'games_active' 		=> (int)$Misc->GamesActive,
			'games_finished' 	=> (int)$Misc->GamesFinished,
			'is_moderator' 		=> !empty($User) && $User->isModerator(),
			'last_process_time' => $Misc->LastProcessTime ? libTime::text($Misc->LastProcessTime): l_t('Never'),
			'last_mod_action' 	=> $Misc->LastModAction ? libTime::text($Misc->LastModAction) : l_t('Never'),
			'error_logs' 		=> $Misc->ErrorLogs,
			'games_paused' 		=> (int)$Misc->GamesPaused,
			'games_crashed' 	=> (int)$Misc->GamesCrashed,
		]);
	}

	public static $footerScript = [];
	public static $footerIncludes = [];

	public static function likeCount($likeCount)
	{
		if($likeCount==0) return '';
		return ' <span class="likeCount">(+'.$likeCount.')</span>';
	}

	static private function footerScripts()
	{
        global $app, $Locale;
        $User = $app->make('user');

		$buf = '';

		if( !is_object($User) ) return $buf;
		elseif( $User->type['User'] ) // Run user-specific page modifications
		{
			// Muted users
			$gameMutePairs = array();
			foreach($User->getMuteCountries() as $gameMutePair)
				$gameMutePairs[] = '['.$gameMutePair[0].','.$gameMutePair[1].']';

			$buf .= '
			<script type="text/javascript">
			let muteUsers = ['.implode(',',$User->getMuteUsers()).'];
			let muteCountries = ['.implode(',',$gameMutePairs).'];
			let muteThreads = ['.implode(',',$User->getMuteThreads()).'];
			</script>';
			unset($gameMutePairs);
			self::$footerIncludes[] = l_j('mute.js');
			self::$footerScript[] = l_jf('muteAll').'();';

			// Participated threads
			$cacheUserParticipatedThreadIDsFilename = libCache::dirID('users',$User->id).'/readThreads.js';

			if( file_exists($cacheUserParticipatedThreadIDsFilename) )
			{
				$buf .= '<script type="text/javascript" src="'.STATICSRV.$cacheUserParticipatedThreadIDsFilename.'?nocache='.rand(0,999999).'"></script>';
				libHTML::$footerScript[]=l_jf('setForumParticipatedIcons').'();';
			}
		}

		if( is_object($Locale) )
			$Locale->onFinish();

		// Add the javascript includes:
		$footerIncludes = array();
		$footerIncludes[] ='contrib/sprintf.js';
		$footerIncludes[] = 'utility.js';
		$footerIncludes[] = 'cacheUpdate.js';
		$footerIncludes[] = 'timeHandler.js';
		$footerIncludes[] = 'forum.js';

		// Don't localize all the footer includes here, as some of them may be dynamically generated
		foreach( array_merge($footerIncludes,self::$footerIncludes) as $includeJS ) // Add on the dynamically added includes
			$buf .= '<script type="text/javascript" src="/javascript/'.$includeJS.'?ver='.JSVERSION.'"></script>';

		// Utility (error detection, message protection), HTML post-processing,
		// time handling functions. Only logged-in users need to run these
		$buf .= '
		<script type="text/javascript">
			let UserClass = function () {
				this.id='.$User->id.';
				this.username="'.htmlentities($User->username).'";
				this.points='.$User->points.'
				this.lastMessageIDViewed='.$User->lastMessageIDViewed.';
				this.timeLastSessionEnded='.$User->timeLastSessionEnded.';
				this.token="'.md5(Config::$secret.$User->id.'Array').'";
				this.darkMode="'.$User->options->value['darkMode'].'";
			}
			User = new UserClass();
			let headerEvent = document.getElementsByClassName("clickable");

			let WEBDIP_DEBUG='.(Config::$debug ? 'true':'false').';

			$(window).on("load", function() {
                setForumMessageIcons();
                setPostsItalicized();
                updateTimestamps();
                updateTimestampGames();
                updateUTCOffset();
                updateTimers();
                '.implode("\n", self::$footerScript).'
			});
		</script>';
		return $buf;
	}
}
