<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tournamentID
 * @property int $userID
 *
 * @property Tournament $tournament
 * @property User $user
 * @package Diplomacy\Models
 */
class TournamentSpectator extends EloquentBase
{
    use HasCompositePrimaryKey;
    protected $table = 'wD_TournamentSpectators';
    public $primaryKey = ['tournamentID', 'userID'];

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return BelongsTo
     */
    public function tournament() : BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournamentID', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeInTournament(Builder $query) : Builder
    {
        return $query->whereIn('status', ['Accepted', 'Left']);
    }
}