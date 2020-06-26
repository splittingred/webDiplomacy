<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * Value object for a turn (year) in a game
 *
 * @package Diplomacy\Models\Entities\Games
 */
class Turn
{
    public int $id;
    public string $name;

    /**
     * @param int $id The ID of the turn
     * @param string $name The text representation of the year
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}