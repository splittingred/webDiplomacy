<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tournamentID
 * @property int $userID
 * @property int $round
 * @property float $score
 *
 * @property Tournament $tournament
 * @property User $user
 * @package Diplomacy\Models
 */
class TournamentScore extends EloquentBase
{
    use HasCompositePrimaryKey;
    public $incrementing = false;

    protected $table = 'wD_TournamentScoring';
    protected $hidden = [];
    protected $primaryKey = ['tournamentID', 'userID', 'round'];

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function tournament() : BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournamentID');
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
     * @param int $tournamentId
     * @return Builder
     */
    public function scopeForTournament(Builder $query, int $tournamentId) : Builder
    {
        return $query->where('tournamentID', $tournamentId);
    }

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
     * @param int $round
     * @return Builder
     */
    public function scopeForRound(Builder $query, int $round) : Builder
    {
        return $query->where('round', $round);
    }
}