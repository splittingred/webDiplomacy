<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\PressTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PressTypes\NoPress;
use Diplomacy\Models\Entities\Games\PressTypes\PublicPressOnly;
use Diplomacy\Models\Entities\Games\PressTypes\Regular;
use Diplomacy\Models\Entities\Games\PressTypes\RulebookPress;

abstract class PressType
{
    const TYPE_REGULAR = 'Regular';
    const TYPE_PUBLIC_ONLY = 'PublicPressOnly';
    const TYPE_NONE = 'NoPress';
    const TYPE_RULEBOOK = 'RulebookPress';

    protected string $type;

    abstract public function getLongName() : string;
    abstract public function allowPrivateMessages(): bool;
    abstract public function allowPublicPress(Phase $phase): bool;

    /**
     * @param $type
     * @return NoPress|PublicPressOnly|Regular|RulebookPress
     * @throws InvalidTypeException
     */
    public static function build(string $type): PressType
    {
        $instance = null;
        switch ($type) {
            case static::TYPE_REGULAR:
                $instance = new Regular();
                break;
            case static::TYPE_PUBLIC_ONLY:
                $instance = new PublicPressOnly();
                break;
            case static::TYPE_NONE:
                $instance = new NoPress();
                break;
            case static::TYPE_RULEBOOK:
                $instance = new RulebookPress();
                break;
            default:
                throw new InvalidTypeException("Press type of $type not found!");
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

