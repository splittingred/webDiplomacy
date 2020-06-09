<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Message
{
    /** @var int $gameId */
    public $gameId;
    /** @var int $timeSent */
    public $timeSent;
    /** @var string $message */
    public $message;
    /** @var Turn $turn */
    public $turn;
    /** @var Country $toCountry */
    public $toCountry;
    /** @var Country $fromCountry */
    public $fromCountry;

    /**
     * @return string
     */
    public function timeSentAsText(): string
    {
        return \libTime::text($this->timeSent);
    }
}
