<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @package Diplomacy\Models
 */
class Tournament extends EloquentBase
{
    protected $table = 'wD_Tournaments';
    protected $hidden = [];
    protected $primaryKey = 'id';

    /**
     * @return HasManyThrough
     */
    public function users() : HasManyThrough
    {
        return $this->hasManyThrough(User::class, 'userID', 'tournamentID', 'id', 'id');
    }

    /**
     * @return HasMany
     */
    public function participants() : HasMany
    {
        return $this->hasMany(TournamentParticipant::class, 'tournamentID');
    }

    /**
     * @return HasMany
     */
    public function scores() : HasMany
    {
        return $this->hasMany(TournamentScore::class, 'tournamentID');
    }

    /**
     * @return BelongsTo
     */
    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'directorID');
    }

    /**
     * @return BelongsTo
     */
    public function coDirector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coDirectorID');
    }

    /**
     * @return HasMany
     */
    public function tournamentGames(): HasMany
    {
        return $this->hasMany(TournamentGame::class, 'tournamentID');
    }

    /**
     * @return HasMany
     */
    public function scoresOrderedByUser() : HasMany
    {
        return $this->scores()
            ->select([
                TournamentScore::raw('wD_TournamentScoring.*'),
                User::raw('wD_Users.username AS username'),
            ])
            ->join('wD_Users', 'wD_Users.id', '=', 'wD_TournamentScoring.userID')
            ->orderBy('userID', 'asc');
    }

    /**
     * @return array
     */
    public function scoresTabulated() : array
    {
        $scores = $this->scoresOrderedByUser()->get();
        $result = [];
        foreach ($scores as $score) {
            if (!array_key_exists($score->userID, $result)) $result[$score->userID] = ['rounds' => [], 'total' => 0];
            $result[$score->userID]['user_id'] = $score->userID;
            $result[$score->userID]['username'] = $score->username;
            $result[$score->userID]['total'] += $score->score;
            $result[$score->userID]['rounds'][$score->round] = $score->score;
        }
        return $result;
    }

    /**
     * @param \User $user
     * @return bool
     */
    public function isEditor(\User $user) : bool
    {
        return $user->isModerator() || $user->id == $this->directorID || $user->id == $this->coDirectorID;
    }

    /**
     * @return HasMany
     */
    public function acceptedParticipants() : HasMany
    {
        return $this->hasMany(TournamentParticipant::class, 'tournamentID')->inTournament();
    }

    /**
     * @return bool
     */
    public function isRunning() : bool
    {
        return $this->status != 'PreStart';
    }

    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->status == 'Active';
    }

    /**
     * @return bool
     */
    public function isRegistrationComplete() : bool
    {
        return $this->status != 'Registration';
    }

    public function toEntity(): \Diplomacy\Models\Entities\Tournament
    {
        /** @var User $director */
        $director = $this->director()->first();
        /** @var User $coDirector */
        $coDirector = $this->coDirector()->first();

        $tournament = new \Diplomacy\Models\Entities\Tournament();
        $tournament->id = $this->id;
        $tournament->name = $this->name;
        $tournament->totalRounds = $this->totalRounds;
        if ($director) $tournament->director = $director->toEntity();
        if ($coDirector) $tournament->coDirector = $coDirector->toEntity();
        return $tournament;
    }
}