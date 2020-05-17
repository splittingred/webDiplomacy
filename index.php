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
 * @package Base
 */
require_once('header.php');
require_once(l_r('lib/message.php'));
require_once(l_r('objects/game.php'));
require_once(l_r('gamepanel/gamehome.php'));
require_once(l_r('lib/libHome.php'));

libHTML::starthtml(l_t('Home'));

if( !isset($_SESSION['lastSeenHome']) || $_SESSION['lastSeenHome'] < $User->timeLastSessionEnded )
{
	$_SESSION['lastSeenHome'] = $User->timeLastSessionEnded;
}

global $DB;
$gameToggleID = 0;

if(isset($_POST['submit']))
{
	if(isset($_POST['gameToggleName']))
	{
		$gameToggleID = (int)$_POST['gameToggleName'];
	}

	if ($User->type['User'] and $gameToggleID > 0)
	{
		$noticesStatus = 5;
		list($noticesStatus) = $DB->sql_row("SELECT hideNotifications FROM wD_Members WHERE userID =".$User->id." and gameID =".$gameToggleID);

		if ($noticesStatus == 0)
		{
			$DB->sql_put("UPDATE wD_Members SET hideNotifications = 1 WHERE userID =".$User->id." and gameID =".$gameToggleID);
		}
		else if ($noticesStatus == 1)
		{
			$DB->sql_put("UPDATE wD_Members SET hideNotifications = 0 WHERE userID =".$User->id." and gameID =".$gameToggleID);
		}
	}
}

if (!$User->isAuthenticated())
{
    echo $twig->render('pages/home/intro.twig', [
        'globalInfo' => libHome::globalInfo()
    ]);
}
elseif (isset($_REQUEST['notices']))
{
	$User->clearNotification('PrivateMessage');

	print '<div class="content"><a href="index.php" class="light">&lt; '.l_t('Back').'</a></div>';

	print '<div class="content-bare content-home-header">';
	print '<table class="homeTable"><tr>';

	notice::$noticesPage=true;
	if( !isset(Config::$customForumURL) ) 
	{
		print '<td class="homeNoticesPMs">';
		print '<div class="homeHeader">'.l_t('Private messages').'</a></div>';
		print libHome::NoticePMs();
		print '</td>';
		print '<td class="homeSplit"></td>';
	}
	print '<td class="homeNoticesGame">';
	print '<div class="homeHeader">'.l_t('Game messages').'</a></div>';
	print libHome::NoticeGame();
	print '</td>';

	print '</tr></table>';
	print '</div>';
	print '</div>';
}
else
{
	print '<div class="content-bare content-home-header">';// content-follow-on">';

	print '<table class="homeTable"><tr>';

	print '<td class="homeMessages">';

    echo $twig->render('pages/home/live_games.twig',[
        'live_games' => libHome::upcomingLiveGames(),
    ]);

	print '</td>';

	print '<td class="homeSplit"></td>';

    echo $twig->render('pages/home/notices.twig',[
        'notices' => libHome::Notice(),
    ]);

	print '<td class="homeSplit"></td>';

	print '<td class="homeGamesStats">';
	print '<div class="homeHeader">'.l_t('My games').' <a href="gamelistings.php?page=1&gamelistType=My games">'.libHTML::link().'</a></div>';
	print libHome::gameNotifyBlock();
	print '<div class="homeHeader">'.l_t('Defeated games').'</div>';
	print libHome::gameDefeatedNotifyBlock();
	print '<div class="homeHeader">'.l_t('Spectated games').'</div>';
	print libHome::gameWatchBlock();

	$result = \Models\Tournament::findParticipatingForUser($User->id);
	if (!empty($result['count'])) {
	    echo $twig->render('pages/home/tournaments.twig', [
            'title' => 'My Tournaments',
	        'tournaments' => $result['entities'],
        ]);
    }

    $result = \Models\Tournament::findSpectatingForUser($User->id);
    if (!empty($result['count'])) {
        echo $twig->render('pages/home/tournaments.twig', [
            'title' => 'Spectated Tournaments',
            'tournaments' => $result['entities'],
        ]);
    }

	print '</td>
	</tr></table>';

	print '</div>';
	print '</div>';
}

libHTML::$footerIncludes[] = l_j('home.js');
libHTML::$footerScript[] = l_jf('homeGameHighlighter').'();';

$_SESSION['lastSeenHome']=time();

libHTML::footer();