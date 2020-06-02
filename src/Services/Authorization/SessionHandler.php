<?php

namespace Diplomacy\Services\Authorization;

use Aura\Session\Session;
use Aura\Session\SessionFactory;

/**
 * @package Diplomacy\Services\Authorization
 */
class SessionHandler
{
    const SEGMENT_KEY = 'Diplomacy';

    /** @var SessionFactory $sessionFactory */
    protected $sessionFactory;

    public function __construct()
    {
        $this->sessionFactory = new SessionFactory();
    }

    /**
     * @return Session
     */
    public function get() : Session
    {
        return $this->sessionFactory->newInstance($_COOKIE);
    }

    /**
     * @return int
     */
    public function getUserId() : int
    {
        $session = $this->get();
        $segment = $session->getSegment(static::SEGMENT_KEY);
        return (int)$segment->get('user_id');
    }

    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->getUserId() > 0;
    }

    /**
     * Start a session for a given user
     * @param integer $userId
     */
    public function touch(int $userId = 0)
    {
        try {
            $session = $this->get();
            // $session->setCookieParams(['lifetime' => '1209600']);
            $segment = $session->getSegment(static::SEGMENT_KEY);
            $segment->set('user_id', $userId);
            $segment->set('user_agent_hash', isset($_SERVER['HTTP_USER_AGENT']) ? substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 4) : '0000');
            $segment->set('ip_address', ip2long($_SERVER['REMOTE_ADDR']));
            $segment->set('hits', (int)$segment->get('hits') + 1);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Wipe the session keys
     */
    public function destroy()
    {
        $session = $this->get();
        if ($session) {
            $session->destroy();
        }
    }
}