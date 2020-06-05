<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class RulebookPress extends PressType
{
    public function getLongName(): string
    {
        return 'Rulebook press';
    }
}