<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\PlayersTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PlayersTypes\Members;
use Diplomacy\Models\Entities\Games\PlayersTypes\MemberVsBots;
use Diplomacy\Models\Entities\Games\PlayersTypes\Mixed;

abstract class PlayersType
{
    abstract public function getLongName() : string;

    /**
     * @param $type
     * @return Members|MemberVsBots|Mixed
     * @throws InvalidTypeException
     */
    public static function build(string $type)
    {
        $instance = null;
        switch (strtolower($type)) {
            case 'members':
                $instance = new Members();
                break;
            case 'member-vs-bots':
                $instance = new MemberVsBots();
                break;
            case 'mixed':
                $instance = new Mixed();
                break;
            default:
                throw new InvalidTypeException("Players type of $type not found!");
        }
        return $instance;
    }
}

