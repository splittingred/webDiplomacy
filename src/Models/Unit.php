<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class Unit extends EloquentBase
{
    protected $table = 'wD_Units';

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