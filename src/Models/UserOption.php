<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $userID
 * @property string $colourblind
 * @property string $displayUpcomingLive
 * @property string $showMoves
 * @property string $orderSort
 * @property string $darkMode
 * @package Diplomacy\Models
 */
class UserOption extends EloquentBase
{
    protected $table = 'wD_UserOptions';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/
}