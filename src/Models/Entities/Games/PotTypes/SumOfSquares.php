<?php

namespace Diplomacy\Models\Entities\Games\PotTypes;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\PotType;

/**
 * @package Diplomacy\Models\Entities\Games\PotTypes
 */
class SumOfSquares extends PotType
{
    public function getLongName(): string
    {
        return 'Sum-of-Squares Scoring';
    }

    public function getDescription(): string
    {
        return 'This game is scored using sum of squares. In a draw, points are split among remaining players based upon how many supply centers they have.';
    }

    public function grantsPointsOnSurvivals(): bool
    {
        return false;
    }

    public function pointsForDraw(Game $game, Member $member): int
    {
        $scoreTotal = $this->getScoreTotal($game);
        $memberSquare = $member->supplyCenterCount * $member->supplyCenterCount;
        $multiplier = $memberSquare / $scoreTotal;
        return (int)ceil($this->amount * $multiplier);
    }

    public function pointsForWin(Game $game, Member $member): int
    {
        return $this->amount;
    }

    public function pointsForSurvival(Game $game, Member $member): int
    {
        return 0;
    }

    public function pointsForDefeat(Game $game, Member $member): int
    {
        return 0;
    }

    /**
     * @param Game $game
     * @return int
     */
    protected function getScoreTotal(Game $game)
    {
        $scoreTotal = 0;
        $playersLeft = $game->members->allWithStatus('left');
        $playersPlaying = $game->members->allWithStatus('playing');

        /** @var Member $member */
        foreach ($playersLeft as $member) {
            $scoreTotal += $member->supplyCenterCount * $member->supplyCenterCount;
        }
        foreach( $playersPlaying as $member) {
            $scoreTotal += $member->supplyCenterCount * $member->supplyCenterCount;
        }
        return (int)$scoreTotal;
    }
}