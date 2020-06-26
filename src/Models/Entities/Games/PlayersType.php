<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\PlayersTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PlayersTypes\Members;
use Diplomacy\Models\Entities\Games\PlayersTypes\MemberVsBots;
use Diplomacy\Models\Entities\Games\PlayersTypes\Mixed;

abstract class PlayersType
{
    const TYPE_MEMBERS = 'members';
    const TYPE_MEMBERS_VS_BOTS = 'members-vs-bots';
    const TYPE_MIXED = 'mixed';

    abstract public function getLongName() : string;
    abstract public function hasBots(): bool;

    /**
     * @param string $type
     * @return Members|MemberVsBots|Mixed
     * @throws InvalidTypeException
     */
    public static function build(string $type)
    {
        $instance = null;
        switch (strtolower($type)) {
            case static::TYPE_MEMBERS:
                $instance = new Members();
                break;
            case static::TYPE_MEMBERS_VS_BOTS:
                $instance = new MemberVsBots();
                break;
            case static::TYPE_MIXED:
                $instance = new Mixed();
                break;
            default:
                throw new InvalidTypeException("Players type of $type not found!");
        }
        return $instance;
    }
}

