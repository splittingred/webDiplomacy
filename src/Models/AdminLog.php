<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class AdminLog extends EloquentBase
{
    protected $table = 'wD_AdminLog';
    protected $hidden = ['params'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID', null, 'user');
    }

    public function timeAsText()
    {
        return \libTime::text($this->time);
    }

    /**
     * @return array
     */
    public function paramsAsHash() : array
    {
        return $this->params ? unserialize($this->params) : [];
    }

    public function paramsAsString() : string
    {
        return print_r($this->paramsAsHash(), true);
    }
}