<?php

namespace Diplomacy\Services\Tournaments;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Tournament;
use Diplomacy\Models\TournamentScore;
use Illuminate\Database\Eloquent\Builder;

class Service
{
    /**
     * Get all active tournaments
     *
     * @return Collection
     */
    public function getActive() : Collection
    {
        $query = Tournament::where('status', '!=', ['Pre-Start', 'Registration']);
        $count = $query->count();

        $query = $query->orderBy('year', 'desc');
        $tournaments = $query->get();
        return new Collection($tournaments, $count);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function findParticipatingForUser(int $userId) : Collection
    {
        $query = Tournament::select(Tournament::raw('DISTINCT wD_Tournaments.*'))
            ->join('wD_TournamentParticipants', 'wD_Tournaments.id', '=', 'wD_TournamentParticipants.tournamentID')
            ->where('wD_Tournaments.status', '!=', 'Finished')
            ->where(function ($q) use ($userId) {
                $q->where('wD_TournamentParticipants.userID', $userId)
                  ->orWhere('wD_Tournaments.directorID', $userId)
                  ->orWhere('wD_Tournaments.coDirectorID', $userId);
            });

        $count = $query->count();
        $tournaments = $query->get();

        return new Collection($tournaments, $count);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function findSpectatingForUser(int $userId) : Collection
    {
        $query = Tournament::join('wD_TournamentSpectators', 'wD_Tournaments.id', '=', 'wD_TournamentSpectators.tournamentID')
            ->where('wD_Tournaments.status', '<>', 'Finished')
            ->where('wD_TournamentSpectators.userID', $userId);
        $count = $query->count();
        $tournaments = $query->get();

        return new Collection($tournaments, $count);
    }

    /**
     * @param int $tournamentId
     * @param int $userId
     * @param int $round
     * @return TournamentScore
     */
    public function findScoreOrNew(int $tournamentId, int $userId, int $round) : TournamentScore
    {
        return TournamentScore::query()
            ->forTournament($tournamentId)
            ->forUser($userId)
            ->forRound($round)->firstOrNew();
    }

    /**
     * @param int $tournamentId
     * @param int $userId
     * @param int $round
     * @param int $score
     * @return bool
     */
    public function updateScore(int $tournamentId, int $userId, int $round, int $score)
    {
        $tournamentScore = $this->findScoreOrNew($tournamentId, $userId, $round);
        $tournamentScore->tournamentID = $tournamentId;
        $tournamentScore->userID = $userId;
        $tournamentScore->score = $score;
        try {
            $tournamentScore->save();
            return true;
        } catch (\Exception $e) {
            // TODO: proper logging
            return false;
        }
    }
}