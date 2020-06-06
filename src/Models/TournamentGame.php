<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Diplomacy\Models
 */
class TournamentGame extends EloquentBase
{
    use HasCompositePrimaryKey;
    protected $table = 'wD_TournamentGames';
    public $incrementing = false;
    protected $primaryKey = ['tournamentID', 'gameID'];

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
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournamentID');
    }
}