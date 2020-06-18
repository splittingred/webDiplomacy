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
    /** @var Factory $entityFactory */
    protected $entityFactory;

    public function __construct()
    {
        $this->entityFactory = new Factory();
    }

    /**
     * Legacy way to load a game. Loads the globals needed for variant.
     *
     * @param int $gameId
     * @return \Diplomacy\Models\Entities\Game|mixed
     * @throws \Exception
     */
    public static function getEntityLegacy(int $gameId)
    {
        try {
            $gameModel = \Diplomacy\Models\Game::find($gameId);
            $gameFactory = new \Diplomacy\Services\Games\Factory();
            $gameEntity = $gameFactory->build($gameModel);
            \libVariant::setGlobals($gameEntity->variant);
            return $gameEntity;
        } catch (\Exception $e) {
            throw new \Exception('Failed to load game '.$gameId.': '.$e->getMessage());
        }
    }


    /**
     * @param int $gameId
     * @return Game
     * @throws \Exception
     */
    public function find(int $gameId) : Game
    {
        $game = Game::find($gameId);
        if (!$game) {
            throw new \Exception("Game with ID {$gameId} not found.");
        }
        return $game;
    }

    /**
     * @param int $userId
     * @return Collection<\Diplomacy\Models\Entities\Game>
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
        $results = $query->get();

        $games = [];
        foreach ($results as $entry) {
            $games[] = $this->entityFactory->build($entry, false);
        }
        return new Collection($games, $count);
    }

    /**
     * Get all defeats for a given user
     *
     * @param int $userId
     * @return Collection<\Diplomacy\Models\Entities\Game>
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
        $results = $query->get();

        $games = [];
        foreach ($results as $entry) {
            $games[] = $this->entityFactory->build($entry, false);
        }
        return new Collection($games, $count);
    }

    /**
     * @param int $userId
     * @return Collection<\Diplomacy\Models\Entities\Game>
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
        $results = $query->get();

        $games = [];
        foreach ($results as $entry) {
            $games[] = $this->entityFactory->build($entry, false);
        }

        return new Collection($games, $count);
    }
}