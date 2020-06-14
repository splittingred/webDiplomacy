<?php
if (!defined('IN_CODE')) {
    http_response_code(404);
    exit(1);
}

// All the standard includes.
require_once ROOT_PATH . 'lib/cache.php';
require_once ROOT_PATH . 'lib/time.php';
require_once ROOT_PATH . 'lib/html.php';
require_once ROOT_PATH . 'locales/layer.php';

// objects
require_once ROOT_PATH . 'objects/basic/baseset.php';   // class baseSet
require_once ROOT_PATH . 'objects/basic/set.php';       // abstract class set and others
require_once ROOT_PATH . 'objects/database.php';        // class Database
require_once ROOT_PATH . 'objects/mailer.php';          // class Mailer
require_once ROOT_PATH . 'objects/silence.php';         // class Silence
require_once ROOT_PATH . 'objects/user.php';            // class User
require_once ROOT_PATH . 'objects/useroptions.php';     // class UserOptions
require_once ROOT_PATH . 'objects/game.php';            // class Game
require_once ROOT_PATH . 'objects/member.php';          // class Member

// GM stuff
require_once ROOT_PATH . 'gamemaster/gamemaster.php';   // class libGameMaster
require_once ROOT_PATH . 'gamemaster/game.php';         // class processGame extends Game
require_once ROOT_PATH . 'gamemaster/member.php';       // class processMember extends Member
require_once ROOT_PATH . 'gamemaster/members.php';      // class processMembers extends Members
require_once ROOT_PATH . 'gamemaster/misc.php';         // class miscUpdate

// game stuff
require_once ROOT_PATH . 'gamepanel/game.php';          // class panelGame extends Game
require_once ROOT_PATH . 'gamepanel/member.php';        // class panelMember extends Member
require_once ROOT_PATH . 'gamepanel/memberhome.php';    // class panelMemberHome extends panelMember
require_once ROOT_PATH . 'gamepanel/gameboard.php';     // class panelGameBoard extends panelGame
require_once ROOT_PATH . 'board/member.php';            // class userMember extends panelMember
require_once ROOT_PATH . 'board/chatbox.php';           // class Chatbox
require_once ROOT_PATH . 'objects/members.php';         // class Members
require_once ROOT_PATH . 'gamepanel/members.php';       // class panelMembers extends Members
require_once ROOT_PATH . 'gamepanel/membershome.php';   // class panelMembersHome extends panelMembers
require_once ROOT_PATH . 'variants/variant.php';        // class WDVariant
require_once ROOT_PATH . 'board/orders/orderinterface.php'; // class OrderInterface
require_once ROOT_PATH . 'board/orders/base/order.php'; // abstract class order
require_once ROOT_PATH . 'board/orders/base/territory.php'; // class Territory
require_once ROOT_PATH . 'board/orders/base/unit.php';  // class Unit
require_once ROOT_PATH . 'board/orders/order.php';      // abstract class userOrder extends order
require_once ROOT_PATH . 'board/orders/diplomacy.php';
require_once ROOT_PATH . 'board/orders/retreats.php';
require_once ROOT_PATH . 'board/orders/builds.php';

global $Locale;
$loc = !empty(Config::$locale) ? Config::$locale : 'English';
require_once ROOT_PATH . "locales/$loc/layer.php"; // This will set $Locale
$Locale->initialize();

// Set up the error handler
if (!defined('libError')) {
    require_once ROOT_PATH . 'global/error.php';
}

date_default_timezone_set('UTC');

global $app;
// Create database object
require_once ROOT_PATH . 'objects/database.php';
$DB = new Database();
$app->instance('DB', $DB);

// Set up the misc values object
require_once ROOT_PATH . 'objects/misc.php';
global $Misc;
$Misc = new Misc();
$app->instance('Misc', $Misc);

require_once ROOT_PATH . 'lib/auth.php';

if( ini_get('request_order') !== false ) {

    // There is a request_order php.ini variable; this must be PHP 5.3.0+

    /*
     * This variable determines whether $_COOKIE is included in $_REQUEST;
     * if request_order contains no 'c' then $_COOKIE is not included.
     *
     * $_COOKIE shouldn't be included in $_REQUEST, however since webDip
     * has historically relied on it being there this code is here
     * temporarily, while the improper $_REQUEST references are found and
     * switched to $_COOKIE.
     */
    if( substr_count(strtolower(ini_get('request_order')), 'c') == 0 ) {
        /*
         * No 'c' in request_order, so no $_COOKIE variables in $_REQUEST;
         * $_COOKIE will need to be merged into $_REQUEST manually.
         *
         * The default config used to be GPC ($_GET, $_POST, $_COOKIE), so
         * to get the standard behaviour $_COOKIE overwrites variables
         * already in $_REQUEST.
         */

        foreach($_COOKIE as $key=>$value)
        {
            $_REQUEST[$key] = $value;
            // array_merge could be used here, but creating a new array
            // for use as a super-global can have weird results.
        }
    }
}

/*
 * If register_globals in enabled remove globals.
 */
if (ini_get('register_globals') or get_magic_quotes_gpc())
{
    function stripslashes_deep(&$value)
    {
        if ( is_array($value) )
            return array_map('stripslashes_deep', $value);
        else
            return stripslashes($value);
    }

    $defined_vars = get_defined_vars();
    while( list($var_name, $var_value) = each($defined_vars) )
    {
        switch( $var_name )
        {
            case "_COOKIE":
            case "_POST":
            case "_GET":
            case "_REQUEST":
                if (get_magic_quotes_gpc())
                {
                    // Strip slashes if magic quotes added slashes
                    ${$var_name} = stripslashes_deep(${$var_name});
                }
                break;
            case "_SERVER":
                break; // Don't strip slashes on _SERVER variables, slashes aren't added to these
            case "_FILES":
                break; // Don't strip slashes on _FILES (file uploads, currently only used for locale text lookup changes)
            default:
                unset( ${$var_name} ); // Remove register_globals variables
                break;
        }
    }

    unset($defined_vars);
}

ini_set('memory_limit','256M'); // 8M is the default
ini_set('max_execution_time','8');
//ini_set('session.cache_limiter','public');
ignore_user_abort(TRUE); // Carry on if the user exits before the script gets printed.
// This shouldn't be necessary for data integrity, but either way it may save reprocess time
