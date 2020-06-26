<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\PotTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PotTypes\PointsPerSupplyCenter;
use Diplomacy\Models\Entities\Games\PotTypes\SumOfSquares;
use Diplomacy\Models\Entities\Games\PotTypes\Unranked;
use Diplomacy\Models\Entities\Games\PotTypes\WinnerTakesAll;

abstract class PotType
{
    public int $amount;

    /**
     * @param int $amount
     */
    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    abstract public function getLongName() : string;

    /**
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * @return bool
     */
    abstract public function grantsPointsOnSurvivals(): bool;

    /**
     * How many points does this scoring system give on a draw?
     * @param Game $game
     * @param Member $member
     * @return int
     */
    abstract public function pointsForDraw(Game $game, Member $member): int;

    /**
     * How many points does this game give for wins?
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    abstract public function pointsForWin(Game $game, Member $member): int;

    /**
     * How many points does this game give for survivals?
     *
     * @param Game $game
     * @param Member $member
     * @return int
     */
    abstract public function pointsForSurvival(Game $game, Member $member): int;

    /**
     * How many points does this game give for defeats?
     * @param Game $game
     * @param Member $member
     * @return int
     */
    abstract public function pointsForDefeat(Game $game, Member $member): int;

    /**
     * @return string
     */
    public function getPointsIcon() : string
    {
        return \libHTML::points();
    }

    /**
     * @param string $type
     * @param int $amount
     * @return PointsPerSupplyCenter|SumOfSquares|Unranked|WinnerTakesAll
     * @throws InvalidTypeException
     */
    public static function build(string $type, int $amount)
    {
        $instance = null;
        switch (strtolower($type)) {
            case 'winner-takes-all':
                $instance = new WinnerTakesAll($amount);
                break;
            case 'points-per-supply-center':
                $instance = new PointsPerSupplyCenter($amount);
                break;
            case 'unranked':
                $instance = new Unranked($amount);
                break;
            case 'sum-of-squares':
                $instance = new SumOfSquares($amount);
                break;
            default:
                throw new InvalidTypeException("Pot type of $type not found!");
        }
        return $instance;
    }
}

