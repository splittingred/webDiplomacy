<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $userID
 * @property int $postID
 * @property int $moderatorUserID
 * @property bool $enabled
 * @property int $startTime
 * @property int $length
 * @property string $reason
 *
 * @property User $user
 * @property User $moderator
 * @package Diplomacy\Models
 */
class Silence extends EloquentBase
{
    protected $table = 'wD_Silence';

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

    /**
     * @return BelongsTo
     */
    public function moderator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'moderatorUserID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', $userId);
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeFromModerator(Builder $query, int $userId) : Builder
    {
        return $query->where('moderatorUserID', $userId);
    }
}