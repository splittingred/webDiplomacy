<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Message
{
    public int $gameId = 0;
    public int $timeSent = 0;
    public string $message = '';
    public ?Turn $turn;
    public ?Country $toCountry;
    public ?Country $fromCountry;

    /**
     * @return string
     */
    public function timeSentAsText(): string
    {
        return \libTime::text($this->timeSent);
    }
}
