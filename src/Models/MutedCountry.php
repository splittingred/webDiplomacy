<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $userID
 * @property int $gameID
 * @property int $muteCountryID
 * @property int $timestamp
 * @package Diplomacy\Models
 */
class MutedCountry extends EloquentBase
{
    use HasCompositePrimaryKey;
    protected $table = 'wD_MuteCountry';
    public $primaryKey = ['userID', 'countryID', 'gameID'];

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'gameID');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $countryId
     * @return Builder
     */
    public function scopeForCountry(Builder $query, int $countryId) : Builder
    {
        return $query->where('countryID', '=', $countryId);
    }

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', '=', $gameId);
    }

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