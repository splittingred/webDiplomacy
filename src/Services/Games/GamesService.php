<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Illuminate\Database\Query\JoinClause;

/**
 * Handles operations around games generally
 *
 * @package Diplomacy\Services\Games
 */
class GamesService
{
    /**
     * @param int $userId
     * @return Collection
     */
    public function getActiveForUser(int $userId) : Collection
    {
        $query = Game::query();
        $query->select('wD_Games.*')
            ->join('wD_Members', function($join) use ($userId) {
            /** @var $join JoinClause */
            $join
                ->on('wD_Members.userID', '=', Game::raw($userId))
                ->on('wD_Members.gameID', '=', 'wD_Games.id');
        })
            ->where('wD_Games.phase', '!=', 'Finished')
            ->where('wD_Members.status', '!=', 'Defeated')
            ->orderBy('wD_Games.processStatus', 'asc')
            ->orderBy('wD_Games.processTime', 'asc');

        $count = $query->count();
        $games = $query->get();

        return new Collection($games, $count);
    }
}