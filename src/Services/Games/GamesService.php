<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;
use Diplomacy\Models\WatchedGame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Handles operations around games generally
 *
 * @package Diplomacy\Services\Games
 */
class GamesService
{
    /**
     * @param int $gameId
     * @return Game
     */
    public function find(int $gameId) : Game
    {
        return Game::find($gameId);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getActiveForUser(int $userId) : Collection
    {
        $gameTable = Game::getTableName();
        $membersTable = Member::getTableName();
        $query = Game::query();
        $query->select($gameTable . '.*')
            ->join('wD_Members', function($join) use ($userId, $gameTable, $membersTable) {
            /** @var $join JoinClause */
            $join
                ->on($membersTable . '.userID', '=', Game::raw($userId))
                ->on($membersTable . '.gameID', '=', $gameTable . '.id');
        })
            ->where($gameTable . '.phase', '!=', 'Finished')
            ->where($membersTable . '.status', '!=', 'Defeated')
            ->orderBy($gameTable . '.processStatus', 'asc')
            ->orderBy($gameTable . '.processTime', 'asc');

        $count = $query->count();
        $games = $query->get();

        return new Collection($games, $count);
    }

    /**
     * Get all defeats for a given user
     *
     * @param int $userId
     * @return Collection
     */
    public function getDefeatsForUser(int $userId) : Collection
    {
        $gameTable = Game::getTableName();
        $membersTable = Member::getTableName();
        $query = Game::query();
        $query->select($gameTable.'.*')
            ->join('wD_Members', function($join) use ($userId, $gameTable, $membersTable) {
                /** @var $join JoinClause */
                $join
                    ->on($membersTable . '.userID', '=', Game::raw($userId))
                    ->on($membersTable . '.gameID', '=', $gameTable . '.id');
            })
            ->where($gameTable . '.phase', '=', 'Finished')
            ->whereIn($membersTable . '.status', ['Resigned', 'Defeated'])
            ->orderBy($gameTable . '.processStatus', 'asc')
            ->orderBy($gameTable . '.processTime', 'asc');

        $count = $query->count();
        $games = $query->get();

        return new Collection($games, $count);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getWatchedForUser(int $userId) : Collection
    {
        $watchedGameTable = WatchedGame::getTableName();
        $gameTable = Game::getTableName();
        $query = Game::query();
        $query->notFinished()
            ->join($watchedGameTable, $watchedGameTable.'.gameID', '=', $gameTable.'.id')
            ->where($watchedGameTable.'.userID','=',$userId)
            ->orderBy('processStatus', 'asc')
            ->orderBy('processTime','ASC');

        $count = $query->count();
        $games = $query->get();

        return new Collection($games, $count);
    }
}