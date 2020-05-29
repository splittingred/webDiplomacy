<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @package Diplomacy\Models
 */
class TerritoryStatus extends EloquentBase
{
    protected $table = 'wD_TerrStatus';

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', '=', $gameId);
    }
}