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
 * @package Diplomacy\Models
 */
class Member extends EloquentBase
{
    protected $table = 'wD_Members';
    protected $hidden = [];

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

    /**
     * @param Builder $query
     * @param integer $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId) : Builder
    {
        return $query->where('userID', $userId);
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

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/


    /**
     * Register that you have viewed the messages from a certain countryID and
     * no longer need notification of them
     *
     * @param string $seenCountryID The countryID who's messages were read
     * @return bool
     */
    public function markMessageSeen($seenCountryID): bool
    {
        $messages = explode(',', $this->newMessagesFrom);
        foreach ($messages as $i => $countryID) {
            if ($countryID == $seenCountryID) {
                unset($messages[$i]);
                break;
            }
        }
        $this->newMessagesFrom = implode(',', $messages);
        return $this->save();
    }
}