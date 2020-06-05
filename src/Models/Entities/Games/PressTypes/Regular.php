<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class Regular extends PressType
{
    public function getLongName(): string
    {
        return ''; // no alt name for regular
    }
}