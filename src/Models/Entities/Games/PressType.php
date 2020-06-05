<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\PressTypes\InvalidTypeException;
use Diplomacy\Models\Entities\Games\PressTypes\NoPress;
use Diplomacy\Models\Entities\Games\PressTypes\PublicPressOnly;
use Diplomacy\Models\Entities\Games\PressTypes\Regular;
use Diplomacy\Models\Entities\Games\PressTypes\RulebookPress;

abstract class PressType
{
    /**
     * @param $type
     * @return NoPress|PublicPressOnly|Regular|RulebookPress
     * @throws InvalidTypeException
     */
    public static function build(string $type)
    {
        $instance = null;
        switch (strtolower($type)) {
            case 'regular':
                $instance = new Regular();
                break;
            case 'publicpressonly':
                $instance = new PublicPressOnly();
                break;
            case 'nopress':
                $instance = new NoPress();
                break;
            case 'rulebookpress':
                $instance = new RulebookPress();
                break;
            default:
                throw new InvalidTypeException("Press type of $type not found!");
        }
        return $instance;
    }
}

