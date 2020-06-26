<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $linkIDType
 * @property int $linkID
 * @property string $type
 * @property int $fromUserID
 * @property string $note
 * @property int $timeSent
 * @package Diplomacy\Models
 */
class ModeratorNote extends EloquentBase
{
    protected $table = 'wD_ModeratorNotes';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fromUserID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForFromUser(Builder $query, int $userId) : Builder
    {
        return $query->where('fromUserID', '=', $userId);
    }
}