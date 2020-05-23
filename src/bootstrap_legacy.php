<?php
if (!defined('IN_CODE')) {
    http_response_code(404);
    exit(1);
}

if (strlen(Config::$serverMessages['ServerOffline']) )
    die('<html><head><title>Server offline</title></head>'.
        '<body>'.Config::$serverMessages['ServerOffline'].'</body></html>');


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

ini_set('memory_limit',"8M"); // 8M is the default
ini_set('max_execution_time','8');
//ini_set('session.cache_limiter','public');
ignore_user_abort(TRUE); // Carry on if the user exits before the script gets printed.
// This shouldn't be necessary for data integrity, but either way it may save reprocess time
