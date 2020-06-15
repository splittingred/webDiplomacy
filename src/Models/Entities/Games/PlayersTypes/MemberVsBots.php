<?php

namespace Diplomacy\Models\Entities\Games\PlayersTypes;

use Diplomacy\Models\Entities\Games\PlayersType;

/**
 * @package Diplomacy\Models\Entities\Games\PlayerTypes
 */
class MemberVsBots extends PlayersType
{
    public function getLongName(): string
    {
        return 'Bot Game';
    }
    
    public function hasBots(): bool
    {
        return true;
    }
}