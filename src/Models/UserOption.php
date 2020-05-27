<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class UserOption extends EloquentBase
{
    protected $table = 'wD_UserOptions';

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }
}