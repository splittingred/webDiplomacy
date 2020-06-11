<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class NoPress extends PressType
{
    protected $type = 'NoPress';

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
        return $phase->isFinished();
    }

    /**
     * @return string
     */
    public function getLongName(): string
    {
        return 'No messaging';
    }
}