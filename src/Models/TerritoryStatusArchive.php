<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $terrID
 * @property int $turn
 * @property string $standoff
 * @property int $gameID
 * @property int $countryID
 *
 * @property Game $game
 * @property Territory $territory
 * @package Diplomacy\Models
 */
class TerritoryStatusArchive extends EloquentBase
{
    use HasCompositePrimaryKey;
    protected $table = 'wD_TerrStatusArchive';
    protected $hidden = [];
    public $primaryKey = ['gameID', 'turn'];

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
    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'terrID');
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
        return $query->where($this->getTable().'.gameID', '=', $gameId);
    }

    /**
     * @param Builder $query
     * @param int $countryId
     * @return Builder
     */
    public function scopeForCountry(Builder $query, int $countryId) : Builder
    {
        return $query->where($this->getTable().'.countryID', '=', $countryId);
    }

    /**
     * @param Builder $query
     * @param int $territoryId
     * @return Builder
     */
    public function scopeForTerritory(Builder $query, int $territoryId) : Builder
    {
        return $query->where($this->getTable().'.terrID', '=', $territoryId);
    }

    /**
     * @param Builder $query
     * @param int $turn
     * @return Builder
     */
    public function scopeForTurn(Builder $query, int $turn) : Builder
    {
        return $query->where($this->getTable().'.turn', '=', $turn);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

    /**
     * @return bool
     */
    public function isStandoff(): bool
    {
        return $this->standoff == 'Yes';
    }
}
