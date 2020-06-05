<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * Value object for a phase in a game
 *
 * @package Diplomacy\Models\Entities\Games
 */
class Phase
{
    public $name;
    public $minutes;
    public $nextSwitchPeriod;

    /**
     * @param string $name
     * @param int $minutes
     * @param int $nextSwitchPeriod
     */
    public function __construct(string $name, int $minutes, int $nextSwitchPeriod)
    {
        $this->name = $name;
        $this->minutes = $minutes;
        $this->nextSwitchPeriod = $nextSwitchPeriod;
    }
}