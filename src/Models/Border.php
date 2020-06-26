<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $mapID
 * @property int $fromTerrID
 * @property int $toTerrID
 * @property string $fleetsPass
 * @property string $armysPass
 *
 * @property Territory $fromTerritory
 * @property Territory $toTerritory
 * @package Diplomacy\Models
 */
class Border extends EloquentBase
{
    protected $table = 'wD_Borders';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

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

}