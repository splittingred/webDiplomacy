<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\DrawTypes\DrawVotesPublic;
use Diplomacy\Models\Entities\Games\DrawTypes\DrawVotesHidden;
use Diplomacy\Models\Entities\Games\DrawTypes\InvalidTypeException;

abstract class DrawType
{
    protected string $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    abstract public function getLongName(): string;
    abstract public function getDescription(): string;
    abstract public function hideDrawVotes(): bool;

    /**
     * @param string $type
     * @throws InvalidTypeException
     */
    public static function build(string $type) : DrawType
    {
        $instance = null;
        switch (strtolower($type)) {
            case 'draw-votes-public':
                $instance = new DrawVotesPublic($type);
                break;
            case 'draw-votes-hidden':
                $instance = new DrawVotesHidden($type);
                break;
            default:
                throw new InvalidTypeException("Draw type of $type not found!");
        }
        return $instance;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}

