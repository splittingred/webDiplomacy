<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $mapID
 * @property int $countryID
 * @property int $terrID
 * @property string $unitType
 * @property int $destroyIndex
 * @property Territory $territory
 * @package Diplomacy\Models
 */
class UnitDestroyIndex extends EloquentBase
{
    protected $table = 'wD_UnitDestroyIndex';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function territory() : BelongsTo
    {
        return $this->belongsTo(Territory::class, 'terrID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/
}