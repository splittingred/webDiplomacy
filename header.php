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
 * The header file; sanitize, initialize, get everything set up quickly
 *
 * @package Base
 */

use Diplomacy\Services\Authorization\Service as AuthorizationService;

if( strpos($_SERVER['PHP_SELF'], 'header.php') )
{
	die("You can't view this document by itself.");
}

define('ROOT_PATH', dirname(__FILE__) . '/');

if( !defined('IN_CODE') )
	define('IN_CODE', 1); // A flag to tell scripts they aren't being executed by themselves

require_once ROOT_PATH . 'src/bootstrap.php';

ob_start(); // Buffer output. libHTML::footer() flushes.

// Support the legacy request variables
if ( isset($_REQUEST['gid']) ) $_REQUEST['gameID'] = $_REQUEST['gid'];
if ( isset($_REQUEST['uid']) ) $_REQUEST['userID'] = $_REQUEST['uid'];

// Reset globals
// FIXME: Resetting this means $GLOBALS['asdf'] is no longer kept in sync with global $asdf. This causes problems during construction
$GLOBALS = [];
$GLOBALS['scriptStartTime'] = microtime(true);

if ($Misc->Version != VERSION)
{
    // auto-upgrades
	require_once 'install/install.php';
}

if (strlen(Config::$serverMessages['ServerOffline']) )
    die('<html><head><title>Server offline</title></head>'.
        '<body>'.Config::$serverMessages['ServerOffline'].'</body></html>');

// Taken from the php manual to disable caching
header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);


if( !defined('AJAX') )
{
    global $User;
    $authService = new AuthorizationService();
    $User = $authService->getCurrentLegacyUser();
    $app->instance('user', $User);
    $app->instance('User', $User);

    if ($User->isAdmin())
	{
		Config::$debug = true;

		if ( isset($_REQUEST['auid']) || isset($_SESSION['auid']) )
			$User = libAuth::adminUserSwitch($User);
//		else
			//define('AdminUserSwitch', $User->id);
	}
	elseif ($Misc->Maintenance)
	{
	    $contents = \Diplomacy\Models\Config::forName('Maintenance')->pluck('message')[0];
		unset($DB); // This lets libHTML know there's a problem
		libHTML::error($contents);
	}
}
