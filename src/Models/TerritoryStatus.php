<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $terrID
 * @property int $occupiedFromTerrID
 * @property string $standoff
 * @property int $gameID
 * @property int $occupyingUnitID
 * @property int $retreatingUnitID
 * @property int $countryID
 *
 * @property Game $game
 * @property Territory $territory
 * @property Territory $occupiedFromTerritory
 * @property Unit $occupyingUnit
 * @property Unit $retreatingUnit
 * @package Diplomacy\Models
 */
class TerritoryStatus extends EloquentBase
{
    protected $table = 'wD_TerrStatus';

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
    public function territory() : BelongsTo
    {
        return $this->belongsTo(Territory::class, 'terrID');
    }

    /**
     * @return BelongsTo
     */
    public function occupiedFromTerritory() : BelongsTo
    {
        return $this->belongsTo(Territory::class, 'occupiedFromTerrID');
    }

    /**
     * @return BelongsTo
     */
    public function occupyingUnit() : BelongsTo
    {
        return $this->belongsTo(Unit::class, 'occupyingUnitID');
    }

    /**
     * @return BelongsTo
     */
    public function retreatingUnit() : BelongsTo
    {
        return $this->belongsTo(Unit::class, 'retreatingUnitID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

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
     * @param Builder $query
     * @param int $territoryId
     * @return Builder
     */
    public function scopeForTerritory(Builder $query, int $territoryId) : Builder
    {
        return $query->where('terrID', '=', $territoryId);
    }

    /**
     * @param Builder $query
     * @param int $countryId
     * @return Builder
     */
    public function scopeForCountry(Builder $query, int $countryId) : Builder
    {
        return $query->where('countryID', '=', $countryId);
    }
}