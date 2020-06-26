<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $gameID
 * @property int $userID
 * @property int $countryID
 * @property int $turn
 * @property int $turnDateTime
 *
 * @property Game $game
 * @property User $user
 * @package Diplomacy\Models
 */
class TurnDate extends EloquentBase
{
    protected $table = 'wD_TurnDate';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class, 'gameID');
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId): Builder
    {
        return $query->where($this->getTableName().'.gameID', '=', $gameId);
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTableName().'.userID', '=', $userId);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/
}