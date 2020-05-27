<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @package Diplomacy\Models
 */
class Territory extends EloquentBase
{
    protected $table = 'wD_Territories';

    /**
     * @return HasMany
     */
    public function statuses() : HasMany
    {
        return $this->hasMany(TerritoryStatus::class, 'terrID');
    }

    /**
     * @return HasMany
     */
    public function archivedStatuses() : HasMany
    {
        return $this->hasMany(TerritoryStatusArchive::class, 'terrID');
    }

    /**
     * @return HasMany
     */
    public function ordersFrom() : HasMany
    {
        return $this->hasMany(Order::class, 'fromTerrID');
    }

    /**
     * @return HasMany
     */
    public function ordersTo() : HasMany
    {
        return $this->hasMany(Order::class, 'toTerrID');
    }

    /**
     * @return HasMany
     */
    public function units() : HasMany
    {
        return $this->hasMany(Unit::class, 'terrID');
    }

    /**
     * @return HasMany
     */
    public function unitDestroys() : HasMany
    {
        return $this->hasMany(UnitDestroyIndex::class, 'terrID');
    }

    /**
     * @return bool
     */
    public function isSupplyCenter() : bool
    {
        return $this->supply == 'Yes';
    }

    /**
     * @return bool
     */
    public function isCoast() : bool
    {
        return $this->type == 'Coast';
    }

    /**
     * @return bool
     */
    public function isLand() : bool
    {
        return $this->type == 'Land';
    }

    /**
     * @return bool
     */
    public function isSea() : bool
    {
        return $this->type == 'Sea';
    }
}
