<?php

namespace Diplomacy\Models;

class Member extends EloquentBase
{
    protected $table = 'wD_Members';
    protected $hidden = [];

    public function game()
    {
        return $this->belongsTo(Member::class, 'gameID');
    }
}