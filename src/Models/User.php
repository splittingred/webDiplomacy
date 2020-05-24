<?php

namespace Diplomacy\Models;

/**
 * @package Diplomacy\Models
 */
class User extends EloquentBase
{
    protected $table = 'wD_Users';
    protected $hidden = ['password'];

    public function missedTurns()
    {
        return $this->hasMany('Diplomacy\Models\MissedTurn', 'userID');
    }

    public function nonLiveUnexcusedMissedTurns()
    {
        return $this->missedTurns()->nonLive()->unexcused();
    }

    public function nonLiveUnexcusedRecentMissedTurns()
    {
        return $this->missedTurns()->nonLive()->unexcused()->recent();
    }

    public function liveUnexcusedMissedTurns()
    {
        return $this->missedTurns()->live()->unexcused();
    }

    public function liveRecentUnExcusedMissedTurns()
    {
        return $this->missedTurns()->live()->unexcused()->recent();
    }
}