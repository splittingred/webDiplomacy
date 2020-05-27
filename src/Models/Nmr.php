<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class Nmr extends EloquentBase
{
    protected $table = 'wD_NMRs';

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

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', $userId);
    }

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', $gameId);
    }
}