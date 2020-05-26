<?php

namespace Diplomacy\Services\Contact;

/**
 * @package Diplomacy\Services\Contact
 */
abstract class Issue
{
    /** @var string */
    const TYPE_GAME = 'gameIssue';
    /** @var string */
    const TYPE_EMERGENCY = 'emergencyIssue';
    /** @var string */
    const TYPE_OTHER = 'otherIssue';

    /** @var string */
    protected $code;
    /** @var string */
    public $additionalInfo;
    /** @var array */
    protected $games = [];

    /**
     * @param string $code
     * @param string $additionalInfo
     */
    public function __construct(string $code = '', string $additionalInfo = '', $games = [])
    {
        $this->code = $code;
        $this->additionalInfo = $additionalInfo;
        $this->games = $games;
    }

    /**
     * @return array
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @return bool
     */
    public function isForAllGames() : bool
    {
        return $this->games == [1];
    }

    /**
     * @return bool
     */
    public function isForNoSpecificGame() : bool
    {
        return $this->games == [0];
    }
}