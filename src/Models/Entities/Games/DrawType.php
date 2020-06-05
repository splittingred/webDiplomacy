<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\DrawTypes\DrawVotesPublic;
use Diplomacy\Models\Entities\Games\DrawTypes\DrawVotesHidden;
use Diplomacy\Models\Entities\Games\DrawTypes\InvalidTypeException;

abstract class DrawType
{
    abstract public function getLongName(): string;

    /**
     * @param string $type
     * @throws InvalidTypeException
     */
    public static function build(string $type) : DrawType
    {
        $instance = null;
        switch (strtolower($type)) {
            case 'draw-votes-public':
                $instance = new DrawVotesPublic();
                break;
            case 'draw-votes-hidden':
                $instance = new DrawVotesHidden();
                break;
            default:
                throw new InvalidTypeException("Draw type of $type not found!");
        }
        return $instance;
    }
}

