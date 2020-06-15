<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
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

    public function getDescription(): string
    {
        return 'This game is scored using draw size scoring. In a draw, points are split evenly among all players remaining.';
    }

    public function grantsPointsOnSurvivals(): bool
    {
        return false;
    }

    /**
     * Split the pot for draws in WTA games.
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    public function pointsForDraw(Game $game, Member $member): int
    {
        $members = $game->members->allWithStatus('playing');
        return (int)(round($this->amount / count($members)));
    }

    /**
     * When you win a WTA game, you take it all.
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    public function pointsForWin(Game $game, Member $member): int
    {
        return $this->amount;
    }

    /**
     * No points for survival in a WTA game.
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    public function pointsForSurvival(Game $game, Member $member): int
    {
        return 0;
    }

    /**
     * No points for defeats in WTA games.
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    public function pointsForDefeat(Game $game, Member $member): int
    {
        return 0;
    }
}