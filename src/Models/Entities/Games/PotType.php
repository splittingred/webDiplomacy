<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\PotTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PotTypes\PointsPerSupplyCenter;
use Diplomacy\Models\Entities\Games\PotTypes\SumOfSquares;
use Diplomacy\Models\Entities\Games\PotTypes\Unranked;
use Diplomacy\Models\Entities\Games\PotTypes\WinnerTakesAll;

abstract class PotType
{
    /** @var int $amount */
    public $amount;

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

