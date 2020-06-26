<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $gameID
 * @property int $countryID
 * @property string $type
 * @property int $unitID
 * @property int $toTerrID
 * @property int $fromTerrID
 * @property string $viaConvoy
 * @package Diplomacy\Models
 */
class Order extends EloquentBase
{
    protected $table = 'wD_Orders';

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
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unitID');
    }

    /**
     * @return BelongsTo
     */
    public function fromTerritory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'fromTerrID');
    }

    /**
     * @return BelongsTo
     */
    public function toTerritory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'toTerrID');
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
     * @param int $unitId
     * @return Builder
     */
    public function scopeForUnit(Builder $query, int $unitId): Builder
    {
        return $query->where($this->getTableName().'.unitID', '=', $unitId);
    }

    /**
     * @param Builder $query
     * @param int $countryId
     * @return Builder
     */
    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where($this->getTableName().'.countryID', '=', $countryId);
    }
}