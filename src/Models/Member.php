<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int userID
 * @property int gameID
 * @property int countryID
 * @property string status
 * @property int timeLoggedIn
 * @property int bet
 * @property int missedPhases
 * @property string newMessagesFrom
 * @property int supplyCenterNo
 * @property int unitNo
 * @property string votes
 * @property int pointsWon
 * @property int gameMessagesSent
 * @property string orderStatus
 * @property int hideNotifications
 * @property int excusedMissedTurns
 *
 * @property Game $game
 * @property User $user
 * @package Diplomacy\Models
 */
class Member extends EloquentBase
{
    protected $table = 'wD_Members';
    protected $with = ['user'];
    protected $hidden = [];

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
     * @param integer $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where(static::getTableName().'.userID', $userId);
    }

    /**
     * @param Builder $query
     * @param integer $gameId
     * @return Builder
     */
    public function scopeForGame(Builder $query, int $gameId) : Builder
    {
        return $query->where('gameID', $gameId);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLeftJoinUser(Builder $query) : Builder
    {
        $usersTable = User::getTableName();
        return $query->leftJoin($usersTable, $usersTable . '.id', '=', static::getTableName() . '.userID');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinGame(Builder $query): Builder
    {
        $membersTable = $this->getTable();
        $gamesTable = Game::getTableName();
        return $query->join($gamesTable, $gamesTable.'.id', '=', $membersTable.'.gameID');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePlaying(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', '=', 'Playing');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotPlaying(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', '!=', 'Playing');
    }

    /**
     * Get only finished Gunboat games with humans for the Classic Variant
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedClassicHumanGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans()->classic();
        });
    }

    /**
     * Get only finished Gunboat games with humans for the Classic Variant
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedClassicHumanGunboatGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans()->classic()->gunboat();
        });
    }

    /**
     * Get only finished games with humans
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedHumanGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans();
        });
    }

    /**
     * Get only finished press games with humans for the Classic Variant
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedClassicHumanPressGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans()->classic()->press();
        });
    }

    /**
     * Get only finished ranked games for the Classic Variant
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedClassicRankedGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans()->classic()->ranked();
        });
    }

    /**
     * Get only finished ranked games for the Classic Variant
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinishedHumanVariantGames(Builder $query): Builder
    {
        return $query->whereHas('game', function($q) {
            $q->gameOver()->onlyHumans()->nonClassic();
        });
    }

    /*****************************************************************************************************************
     * QUERY METHODS
     ****************************************************************************************************************/

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

    /**
     * Register that you have viewed the messages from a certain countryID and
     * no longer need notification of them
     *
     * @param int $countryId The countryID who's messages were read
     * @return bool
     */
    public function markMessageSeen(int $countryId): bool
    {
        $messages = explode(',', $this->newMessagesFrom);
        foreach ($messages as $i => $fromCountryId) {
            if ($fromCountryId == $countryId) {
                unset($messages[$i]);
                break;
            }
        }
        $this->newMessagesFrom = implode(',', $messages);
        return $this->save();
    }

    /**
     * Mark message unseen from a country
     *
     * @param int $countryId
     * @return bool
     */
    public function markMessageUnseen(int $countryId): bool
    {
        $messages = explode(',', $this->newMessagesFrom);
        $messages[] = $countryId;
        $messages = array_unique($messages);
        $this->newMessagesFrom = implode(',', $messages);
        return $this->save();
    }
}