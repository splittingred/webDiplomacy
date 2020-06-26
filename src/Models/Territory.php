<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $mapID
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $supply
 * @property int $mapX
 * @property int $mapY
 * @property int $smallMapX
 * @property int $smallMapY
 * @property int $countryID
 * @property string $coast
 * @property int $coastParentID
 * @package Diplomacy\Models
 */
class Territory extends EloquentBase
{
    protected $table = 'wD_Territories';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

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

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

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
