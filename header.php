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

if( strpos($_SERVER['PHP_SELF'], 'header.php') )
{
	die("You can't view this document by itself.");
}

define('ROOT_PATH', dirname(__FILE__) . '/');

if( !defined('IN_CODE') )
	define('IN_CODE', 1); // A flag to tell scripts they aren't being executed by themselves

require_once 'src/bootstrap.php';
global $app;

ob_start(); // Buffer output. libHTML::footer() flushes.

// Support the legacy request variables
if ( isset($_REQUEST['gid']) ) $_REQUEST['gameID'] = $_REQUEST['gid'];
if ( isset($_REQUEST['uid']) ) $_REQUEST['userID'] = $_REQUEST['uid'];

// Reset globals
// FIXME: Resetting this means $GLOBALS['asdf'] is no longer kept in sync with global $asdf. This causes problems during construction
$GLOBALS = [];
$GLOBALS['scriptStartTime'] = microtime(true);

// All the standard includes.
require_once('lib/cache.php');
require_once('lib/time.php');
require_once('lib/html.php');
require_once('locales/layer.php');

global $Locale;
require_once('locales/'.Config::$locale.'/layer.php'); // This will set $Locale
$Locale->initialize();

require_once 'objects/silence.php';
require_once 'objects/user.php';
require_once 'objects/game.php';

if (!defined('libError')) {
    require_once 'global/error.php';
}
// Set up the error handler

date_default_timezone_set('UTC');

// Create database object
require_once 'objects/database.php';
$DB = new Database();
$app->instance('DB', $DB);

// Set up the misc values object
require_once 'objects/misc.php';
global $Misc;
$Misc = new Misc();
$app->instance('Misc', $Misc);

if ( $Misc->Version != VERSION )
{
	require_once 'install/install.php';
}

// Taken from the php manual to disable caching
header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

require_once 'lib/auth.php';

if( !defined('AJAX') )
{
    global $User;
//    var_dump($_COOKIE); die();
	if( isset($_REQUEST['logoff']) )
	{
		$success=libAuth::keyWipe();
		$User = new User(GUESTID); // Give him a guest $User
		header('refresh: 4; url=/users/login.php?noRefresh=on');
		libHTML::notice(l_t("Logged out"),l_t("You have been logged out, and are being redirected to the logon page."));
	}

//	$User = libAuth::auth();
    $authService = new \Diplomacy\Services\Authorization\Service();
    $User = $authService->getCurrentLegacyUser();
    $app->instance('user', $User);

    if ($User->isAdmin())
	{
		Config::$debug=true;

		if ( isset($_REQUEST['auid']) || isset($_SESSION['auid']) )
			$User = libAuth::adminUserSwitch($User);
		else
			define('AdminUserSwitch',$User->id);
	}
	elseif ( $Misc->Maintenance )
	{
	    $contents = \Diplomacy\Models\Config::forName('Maintenance')->pluck('message')[0];
		unset($DB); // This lets libHTML know there's a problem
		libHTML::error($contents);
	}
}

// This gets called by libHTML::footer
function close()
{
    global $app;
    $DB = $app->make('DB');
    $Misc = $app->make('Misc');

	// This isn't put into the database destructor in case of dieing due to an error

	if ( is_object($DB) )
	{
		$Misc->write();

		if( !defined('ERROR'))
			$DB->sql_put("COMMIT");

		unset($DB);
	}

	$sessionHandler = new \Diplomacy\Services\Authorization\SessionHandler();
	$session = $sessionHandler->get();
	if ($session) {
        $session->commit();
    }

	ob_end_flush();

	die();
}
