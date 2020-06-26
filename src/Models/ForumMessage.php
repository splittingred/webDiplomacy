<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $toUserID
 * @property int $fromUserID
 * @property int $timeSent
 * @property string $message
 * @property string $subject
 * @property string $type
 * @property int $replies
 * @property int latestReplySent
 * @property int $silenceID
 * @property int $likeCount
 * @package Diplomacy\Models
 */
class ForumMessage extends EloquentBase
{
    protected $table = 'wD_ForumMessages';

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeRoot(Builder $query) : Builder
    {
        return $query->where('type', 'ThreadStart');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeReply(Builder $query) : Builder
    {
        return $query->where('type', 'ThreadReply');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLive(Builder $query) : Builder
    {
        return $query->where('liveGame', 1);
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWhereFromUser(Builder $query, int $userId) : Builder
    {
        return $query->where('fromUserID', $userId);
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWhereToUser(Builder $query, int $userId) : Builder
    {
        return $query->where('toUserID', $userId);
    }

    /**
     * @return BelongsTo
     */
    public function toUser()
    {
        return $this->belongsTo('\Diplomacy\Models\User', 'toUserID');
    }

    /**
     * @return BelongsTo
     */
    public function fromUser()
    {
        return $this->belongsTo('\Diplomacy\Models\User', 'fromUserID');
    }

    /**
     * @return bool
     */
    public function isRoot() : bool
    {
        return $this->type == 'ThreadStart';
    }

    /**
     * @return bool
     */
    public function isReply() : bool
    {
        return $this->type == 'ThreadReply';
    }

    /**
     * @return string
     */
    public function timeSentAsText() : string
    {
        return \libTime::text($this->timeSent);
    }
}
