<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class Unit extends EloquentBase
{
    protected $table = 'wD_Units';

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', '=', $gameId);
    }

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
    public function territory() : BelongsTo
    {
        return $this->belongsTo(Territory::class, 'terrID');
    }
}