<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Games\PotType;

/**
 * Also called "Draw-Size Scoring"
 *
 * @package Diplomacy\Models\Entities\Games\PotTypes
 */
class WinnerTakesAll extends PotType
{
    public function getLongName(): string
    {
        return 'Draw-Size Scoring';
    }
}