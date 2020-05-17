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

use Diplomacy\Controllers\DashboardController;
use Diplomacy\Controllers\IntroController;
use Diplomacy\Controllers\Users\NoticesController;

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
    $controller = new IntroController();
    echo $controller->render();
}
elseif (isset($_REQUEST['notices']))
{
    $controller = new NoticesController();
    echo $controller->render();
}
else
{
    $controller = new DashboardController();
    echo $controller->render();
}

libHTML::$footerIncludes[] = l_j('home.js');
libHTML::$footerScript[] = l_jf('homeGameHighlighter').'();';

$_SESSION['lastSeenHome'] = time();

libHTML::footer();