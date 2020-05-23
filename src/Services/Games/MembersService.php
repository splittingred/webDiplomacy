<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Member;

/**
 * Handles operations around members of games
 *
 * @package Diplomacy\Services\Games
 */
class MembersService
{
    public function findForGame(int $userId, int $gameId)
    {
        return Member::where('userID', $userId)->where('gameID', $gameId)->first();
    }
}