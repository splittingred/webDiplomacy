<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\PotType;

/**
 * @package Diplomacy\Models\Entities\Games\PotTypes
 */
class Unranked extends PotType
{
    public function getLongName(): string
    {
        return 'Unranked';
    }

    public function getDescription(): string
    {
        return 'This game is unranked. In a draw, all points are returned to their previous owners.';
    }

    public function grantsPointsOnSurvivals(): bool
    {
        return false;
    }

    public function pointsForDraw(Game $game, Member $member): int
    {
        return $member->bet;
    }

    public function pointsForWin(Game $game, Member $member): int
    {
        return $member->bet;
    }

    public function pointsForSurvival(Game $game, Member $member): int
    {
        return $member->bet;
    }

    public function pointsForDefeat(Game $game, Member $member): int
    {
        return $member->bet;
    }
}