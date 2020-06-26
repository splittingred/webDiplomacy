<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tournamentID
 * @property int $userID
 * @property string $status
 *
 * @property Tournament $tournament
 * @property User $user
 * @package Diplomacy\Models
 */
class TournamentParticipant extends EloquentBase
{
    use HasCompositePrimaryKey;
    protected $table = 'wD_TournamentParticipants';
    public $primaryKey = ['tournamentID', 'userID'];

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    public function tournament() : BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournamentID', 'id');
    }

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