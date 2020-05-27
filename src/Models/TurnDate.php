<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class TurnDate extends EloquentBase
{
    protected $table = 'wD_TurnDate';

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
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }
}