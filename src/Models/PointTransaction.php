<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $type
 * @property int $userID
 * @property int $gameID
 * @property int $memberID
 * @property int $points
 *
 * @property User $user
 * @property Game $game
 * @property Member $member
 * @package Diplomacy\Models
 */
class PointTransaction extends EloquentBase
{
    protected $table = 'wD_PointsTransactions';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class, 'gameID');
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /**
     * @return BelongsTo
     */
    public function member() : BelongsTo
    {
        return $this->belongsTo(Member::class, 'memberID');
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
        return $query->where('userID', $userId);
    }

    /**
     * @param Builder $query
     * @param int $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', $gameId);
    }

    /**
     * @param Builder $query
     * @param int $memberId
     * @return Builder
     */
    public function scopeForMember(Builder $query, int $memberId) : Builder
    {
        return $query->where('memberID', $memberId);
    }
}