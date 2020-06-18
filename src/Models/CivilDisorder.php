<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class CivilDisorder extends EloquentBase
{
    protected $table = 'wD_CivilDisorders';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'gameID');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param integer $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', $userId);
    }

    /**
     * @param Builder $query
     * @param integer $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', $gameId);
    }

    /**
     * @param Builder $query
     * @param integer $countryId
     * @return Builder
     */
    public function scopeForCountry(Builder $query, int $countryId) : Builder
    {
        return $query->where('countryID', $countryId);
    }

    /**
     * @param Builder $query
     * @param integer $turn
     * @return Builder
     */
    public function scopeForTurn(Builder $query, int $turn) : Builder
    {
        return $query->where('turn', $turn);
    }

    /*****************************************************************************************************************
     * QUERY METHODS
     ****************************************************************************************************************/

    /**
     * @param int $userId
     * @return Builder
     */
    public static function takenOverByUser(int $userId): Builder
    {
        $gamesTable = Game::getTableName();
        $membersTable = Member::getTableName();
        $cdTable = static::getTableName();
        $q = static::query();
        return $q->join($gamesTable, $gamesTable.'.id', '=', $cdTable.'.gameID')
            ->leftJoin($membersTable, function($join) use ($userId, $cdTable, $membersTable) {
                $join->where($cdTable.'.gameID', '=', Member::raw($membersTable.'.gameID'))
                    ->where($cdTable.'.userID', '=', Member::raw($userId));
            })
            ->where($cdTable.'.userID','=', $userId)
            ->whereNull($membersTable.'.userID');
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/
}