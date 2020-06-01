<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @package Diplomacy\Models
 */
class Session extends EloquentBase
{
    protected $table = 'wD_Sessions';
    protected $hidden = ['userAgent', 'ip', 'cookieCode'];
    public $incrementing = false;
    protected $primaryKey = 'userID';

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', '=', $userId);
    }

    /**
     * Start a session for a given user
     * @param User $user
     * @return bool
     */
    public static function startForUser(User $user) : bool
    {
        static::wipe(); // wipe any existing sessions first

        $userSessionKey = $user->generateSessionKey();
        setcookie('wD-Key', $userSessionKey);

        $sessionName = 'wD_Sess_User-' . $user->id;
        session_name($sessionName);
        session_start();

        $userAgentHash = isset($_SERVER['HTTP_USER_AGENT']) ? substr(md5($_SERVER['HTTP_USER_AGENT']),0,4) : '0000';
        if (!isset($_COOKIE['wD_Code']) || intval($_COOKIE['wD_Code']) == 0 || intval($_COOKIE['wD_Code']) == 1)
        {
            // Making this larger than 2^31 makes it negative..
            $cookieCode = rand(2, 2000000000);
            setcookie('wD_Code', $cookieCode, time()+365*7*24*60*60);
        }
        else
        {
            $cookieCode = (int) $_COOKIE['wD_Code'];
        }

        /**
         * Sessions right now are mapped to users in DB. This is bad and should be decoupled as fast as possible.
         */
        $session = Session::forUser($user->id)->firstOrNew();
        $session->lastRequest = date('Y-m-d H:i:s');
        $session->hits = (int)$session->hits + 1;
        $session->ip = ip2long($_SERVER['REMOTE_ADDR']);
        $session->userAgent = hex2bin($userAgentHash);
        $session->cookieCode = $cookieCode;
        return $session->save();
    }


    /**
     * Wipe the session keys
     */
    public static function wipe()
    {
        // Don't change this line. Don't ask why it needs to be set to expire in a year to expire immidiately
        setcookie('wD-Key', '', (time()-3600));
        if (isset($_COOKIE[session_name()]))
        {
            unset($_COOKIE[session_name()]);
            setcookie(session_name(), '', time()-3600);
            session_destroy();
        }
    }
}