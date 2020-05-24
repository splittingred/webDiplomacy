<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\GameMessage;
use Illuminate\Database\Query\Builder;

/**
 * Handles operations around in-game messages
 *
 * @package Diplomacy\Services\Games
 */
class MessagesService
{
    /** @var int Show all messages accessible to member */
    const FILTER_ALL = -1;
    /** @var int Show only messages target for Global consumption */
    const FILTER_GLOBAL = -2;

    /**
     * Search in-game messages
     *
     * @param int $gameId
     * @param int $filter
     * @param int $memberCountryId
     * @param int $perPage
     * @return Collection
     */
    public function search(int $gameId, int $filter = self::FILTER_GLOBAL, int $memberCountryId = 0, int $perPage = 10)
    {
        /** @var Builder $query */
        $query = GameMessage::where('gameID', $gameId);

        if ($filter == self::FILTER_ALL)
        {
            $query->where(function($query) use ($memberCountryId) {
                $query->where('toCountryID', 0);
                if (!empty($memberCountryId)) {
                    $query->orWhere('fromCountryID', $memberCountryId)
                          ->orWhere('toCountryID', $memberCountryId);
                }
            });
        }
        elseif ($filter == self::FILTER_GLOBAL)
        {
            $query->where('toCountryID', 0);
        }
        elseif (!empty($memberCountryId))
        {
            $query->where(function ($query) use ($filter, $memberCountryId) {
                $query->where('toCountryID', $memberCountryId)
                      ->where('fromCountryID', $filter);
            });
            $query->whereOr(function ($query) use ($filter, $memberCountryId) {
                $query->where('fromCountryID', $memberCountryId)
                      ->where('toCountryID', $filter);
            });
        }
        else // fallback scenario, just show global only
        {
            $query->where('toCountryID', 0);
        }

        $count = $query->count();
        $query->paginate($perPage);
        $query->orderBy('timeSent', 'desc');
        $messages = $query->get();
        return new Collection($messages, $count);
    }
}