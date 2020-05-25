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

    /**
     * @return bool
     */
    public function canDoEmergencyPauses() : bool
    {
        return $this->emergencyPauseDate != 1;
    }
}