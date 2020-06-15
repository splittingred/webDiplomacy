<?php

namespace Diplomacy\Models\Entities\Games\DrawTypes;

use Diplomacy\Models\Entities\Games\DrawType;

/**
 * @package Diplomacy\Models\Entities\Games\DrawTypes
 */
class DrawVotesHidden extends DrawType
{
    public function getLongName(): string
    {
        return 'Hidden draw votes';
    }

    public function getDescription(): string
    {
        return 'Draw votes are not publicly known in this game.';
    }

    /**
     * @return bool
     */
    public function hideDrawVotes(): bool
    {
        return true;
    }
}