<?php

namespace Diplomacy\Models\Entities\Games\PlayersTypes;

use Diplomacy\Models\Entities\Games\PlayersType;

/**
 * @package Diplomacy\Models\Entities\Games\PlayerTypes
 */
class Members extends PlayersType
{
    public function getLongName(): string
    {
        return ''; // no name for normal mode
    }

    public function hasBots(): bool
    {
        return false;
    }
}