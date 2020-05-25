<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

class Member extends EloquentBase
{
    protected $table = 'wD_Members';
    protected $hidden = [];

    public function game()
    {
        return $this->belongsTo(Member::class, 'gameID');
    }

    /**
     * @param Builder $query
     * @param integer $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', $userId);
    }
}