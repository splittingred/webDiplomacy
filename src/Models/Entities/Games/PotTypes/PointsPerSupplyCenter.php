<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Games\PotType;

/**
 * @package Diplomacy\Models\Entities\Games\PotTypes
 */
class PointsPerSupplyCenter extends PotType
{
    public function getLongName(): string
    {
        return 'Survivors-Win Scoring';
    }
}