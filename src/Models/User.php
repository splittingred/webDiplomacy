<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @package Diplomacy\Models
 */
class User extends EloquentBase
{
    protected $table = 'wD_Users';
    protected $hidden = ['password'];

    /**
     * @return HasMany
     */
    public function missedTurns() : HasMany
    {
        return $this->hasMany(MissedTurn::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function watchedGames() : HasMany
    {
        return $this->hasMany(WatchedGame::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function options() : HasMany
    {
        return $this->hasMany(UserOption::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function connections() : HasMany
    {
        return $this->hasMany(UserConnection::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function turnDates() : HasMany
    {
        return $this->hasMany(TurnDate::class, 'userID');
    }

    /**
     * @return bool
     */
    public function canDoEmergencyPauses() : bool
    {
        return $this->emergencyPauseDate != 1;
    }
}