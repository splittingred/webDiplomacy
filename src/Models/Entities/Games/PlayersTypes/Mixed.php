<?php

namespace Diplomacy\Models\Entities\Games\PlayersTypes;

use Diplomacy\Models\Entities\Games\PlayersType;

/**
 * @package Diplomacy\Models\Entities\Games\PlayerTypes
 */
class Mixed extends PlayersType
{
    public function getLongName(): string
    {
        return 'Fill with Bots';
    }

    public function hasBots(): bool
    {
        return true;
    }
}