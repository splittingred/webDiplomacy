<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class TournamentParticipant extends EloquentBase
{
    protected $table = 'wD_TournamentParticipants';
    protected $hidden = [];

    public function tournament() : BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournamentID', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function scopeInTournament(Builder $query) : Builder
    {
        return $query->whereIn('status', ['Accepted', 'Left']);
    }
}