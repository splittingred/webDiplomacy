<?php

namespace Diplomacy\Models;

use Aura\Session\Segment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class Session extends EloquentBase
{
    protected $table = 'wD_Sessions';
    protected $hidden = ['userAgent', 'ip', 'cookieCode'];
    public $incrementing = false;
    protected $primaryKey = 'userID';

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

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', '=', $userId);
    }
}