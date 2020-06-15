<?php

namespace Diplomacy\Models\Entities\Games\DrawTypes;

use Diplomacy\Models\Entities\Games\DrawType;

/**
 * @package Diplomacy\Models\Entities\Games\DrawTypes
 */
class DrawVotesPublic extends DrawType
{
    public function getLongName(): string
    {
        return 'Public draw votes';
    }

    public function getDescription(): string
    {
        return 'Draw votes are publicly displayed in this game.';
    }

    /**
     * @return bool
     */
    public function hideDrawVotes(): bool
    {
        return false;
    }
}