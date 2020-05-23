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

ob_start(); // Buffer output. libHTML::footer() flushes.

// Support the legacy request variables
if ( isset($_REQUEST['gid']) ) $_REQUEST['gameID'] = $_REQUEST['gid'];
if ( isset($_REQUEST['uid']) ) $_REQUEST['userID'] = $_REQUEST['uid'];

// Reset globals
// FIXME: Resetting this means $GLOBALS['asdf'] is no longer kept in sync with global $asdf. This causes problems during construction
$GLOBALS = array();
$GLOBALS['scriptStartTime'] = microtime(true);

// All the standard includes.
require_once('lib/cache.php');
require_once('lib/time.php');
require_once('lib/html.php');
require_once('locales/layer.php');

global $Locale;
require_once('locales/'.Config::$locale.'/layer.php'); // This will set $Locale
$Locale->initialize();

require_once(l_r('objects/silence.php'));
require_once(l_r('objects/user.php'));
require_once(l_r('objects/game.php'));

require_once(l_r('global/error.php'));
// Set up the error handler

date_default_timezone_set('UTC');

// Create database object
require_once(l_r('objects/database.php'));
$DB = new Database();

// Set up the misc values object
require_once(l_r('objects/misc.php'));
global $Misc;
$Misc = new Misc();

if ( $Misc->Version != VERSION )
{
	require_once(l_r('install/install.php'));
}

// Taken from the php manual to disable cacheing.
header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

require_once(l_r('lib/auth.php'));

if( !defined('AJAX') )
{
	if( isset($_REQUEST['logoff']) )
	{
		$success=libAuth::keyWipe();
		$User = new User(GUESTID); // Give him a guest $User
		header('refresh: 4; url=logon.php?noRefresh=on');
		libHTML::notice(l_t("Logged out"),l_t("You have been logged out, and are being redirected to the logon page."));
	}

	global $User;
	$User = libAuth::auth();

	if ( $User->type['Admin'] )
	{
		Config::$debug=true;

		if ( isset($_REQUEST['auid']) || isset($_SESSION['auid']) )
			$User = libAuth::adminUserSwitch($User);
		else
			define('AdminUserSwitch',$User->id);
	}
	elseif ( $Misc->Maintenance )
	{
		list($contents) = $DB->sql_row("SELECT message FROM wD_Config WHERE name = 'Maintenance'");
		unset($DB); // This lets libHTML know there's a problem
		libHTML::error($contents);

	}
}

// This gets called by libHTML::footer
function close()
{
	global $DB, $Misc;

	// This isn't put into the database destructor in case of dieing due to an error

	if ( is_object($DB) )
	{
		$Misc->write();

		if( !defined('ERROR'))
			$DB->sql_put("COMMIT");

		unset($DB);
	}

	ob_end_flush();

	die();
}
