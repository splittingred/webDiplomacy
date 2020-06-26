<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $toUserID
 * @property int $fromID
 * @property string $type
 * @property string $keep
 * @property string $private
 * @property string $text
 * @property string $linkName
 * @property int $linkID
 * @property int $timeSent
 * @package Diplomacy\Models
 */
class Notice extends EloquentBase
{
    protected $table = 'wD_Notices';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function toUser() : BelongsTo
    {
        return $this->belongsTo(User::class, 'toUserID');
    }

    /**
     * @return BelongsTo
     */
    public function fromUser() : BelongsTo
    {
        return $this->belongsTo(User::class, 'fromID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/
}