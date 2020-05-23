<?php

namespace Diplomacy\Tournaments;

use Database;
use Diplomacy\Models\Collection;
use Diplomacy\Models\Tournament;

class Service
{
    /** @var Database $database */
    protected $database;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

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
}