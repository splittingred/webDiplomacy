<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class PublicPressOnly extends PressType
{
    protected $type = 'PublicPressOnly';

    public function allowPrivateMessages(): bool
    {
        return false;
    }

    /**
     * @param Phase $phase
     * @return bool
     */
    public function allowPublicPress(Phase $phase): bool
    {
        return true;
    }

    public function getLongName(): string
    {
        return 'Public messaging only';
    }
}