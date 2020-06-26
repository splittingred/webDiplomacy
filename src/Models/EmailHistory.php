<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $userID
 * @property string $oldEmail
 * @property string $newEmail
 * @property int $date
 * @property string $reason
 * @property string $changedBy
 * @package Diplomacy\Models
 */
class EmailHistory extends EloquentBase
{
    protected $table = 'wD_EmailHistory';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTableName().'.userID', '=', $userId);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/
}