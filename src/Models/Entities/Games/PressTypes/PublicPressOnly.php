<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class PublicPressOnly extends PressType
{
    public function getLongName(): string
    {
        return 'Public messaging only';
    }
}