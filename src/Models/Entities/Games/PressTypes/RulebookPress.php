<?php

namespace Diplomacy\Models\Entities\Games\PressTypes;

use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PressType;

/**
 * @package Diplomacy\Models\Entities\Games\PressTypes
 */
class RulebookPress extends PressType
{
    protected string $type = PressType::TYPE_RULEBOOK;

    public function allowPrivateMessages(): bool
    {
        return true;
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
        return 'Rulebook press';
    }
}