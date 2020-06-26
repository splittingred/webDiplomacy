<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $variantID
 * @property int $gameID
 * @property int $systemToken
 * @property int $typeID
 * @property int $userID
 * @property int $offset
 * @property int $val_int
 * @property float $val_float
 * @property Game $game
 * @property User $user
 * @package Diplomacy\Models
 */
class VariantData extends EloquentBase
{
    protected $table = 'wD_VariantData';

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

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

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

    /**
     * @param Builder $query
     * @param int $variantId
     * @return Builder
     */
    public function scopeForVariant(Builder $query, int $variantId) : Builder
    {
        return $query->where('variantID', '=', $variantId);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

}