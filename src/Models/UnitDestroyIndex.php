<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class UnitDestroyIndex extends EloquentBase
{
    protected $table = 'wD_UnitDestroyIndex';

    /**
     * @return BelongsTo
     */
    public function territory() : BelongsTo
    {
        return $this->belongsTo(Territory::class, 'terrID');
    }
}