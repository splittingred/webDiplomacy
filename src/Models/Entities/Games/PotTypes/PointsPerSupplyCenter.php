<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
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

    public function getDescription(): string
    {
        return 'This game is scored using points per supply center. In a draw, points are split evenly among all players remaining.';
    }

    public function grantsPointsOnSurvivals(): bool
    {
        return true;
    }

    public function pointsForDraw(Game $game, Member $member): int
    {
        $members = $game->members->allWithStatus('playing');
        return (int) round($this->amount / count($members));
    }

    public function pointsForWin(Game $game, Member $member): int
    {
        $ratios = $this->ratios($game);
        return ceil($ratios[$member->country->id] * $this->amount);
    }

    public function pointsForSurvival(Game $game, Member $member): int
    {
        $ratios = $this->ratios($game);
        return ceil($ratios[$member->country->id] * $this->amount);
    }

    public function pointsForDefeat(Game $game, Member $member): int
    {
        // TODO: Ensure this is correct
        return $member->bet;
    }

    private $ratios;
    private function ratios(Game $game) {
        if ($this->ratios != null) return $this->ratios;

        $ratios = [];

        $playingMembers = $game->members->allWithStatus('Playing');

        /** @var Member $member */
        foreach ($game->members->allWithStatus('Left') as $member)
            $ratios[$member->country->id] = 0.0;

        /** @var Member $member */
        foreach ($playingMembers as $member)
            $ratios[$member->country->id] = 0.0;
        /*
         * PPSC; calculate based on active-player-owned supply-centers, but
         * things are complicated because players with over $SCTarget SCs are limited
         * to the winnings they would get from $SCTarget, and the remainder is
         * distributed among the survivors according to their winnings.
         */
        $SCsInPlayCount = (float)$game->members->supplyCenterCount('playing');

        assert($SCsInPlayCount > 0);

        $SCTarget = $game->variant->supplyCenterTarget;
        foreach($playingMembers as $member)
        {

            if ($member->supplyCenterCount > $SCTarget)
            {
                /*
                 * Winner is greedy and got more SCs than he needed:
                 * - Get the number of extra SCs he has
                 * - Reduce his total to $SCTarget
                 * - Subtract the extra amount from the total SCs so they scale down
                 */

                /*
                 * Subtracting the over-the-limit extra SCs from the winner and
                 * from the total SC count effectively makes the algorithm behave
                 * as if they didn't exist
                 */
                $SCsInPlayCount -= ($member->supplyCenterCount - $SCTarget);
                $ratios[$member->country->id] = $SCTarget / $SCsInPlayCount;
            }
        }

        foreach($playingMembers as $member)
        {
            if ($member->supplyCenterCount > $SCTarget) continue;

            $ratios[$member->country->id] = $member->supplyCenterCount / $SCsInPlayCount;
        }
        return $ratios;
    }
}