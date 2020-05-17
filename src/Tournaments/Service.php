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
        $sqlCount = "SELECT COUNT(t.id) FROM wD_Tournaments t WHERE status != 'PreStart' AND status != 'Registration'";
        $sql = "SELECT * FROM wD_Tournaments t WHERE status != 'PreStart' AND status != 'Registration' ORDER BY year DESC";

        $result = $this->database->sql_tabl($sql);
        list($count) = $this->database->sql_row($sqlCount);

        $tournaments = [];
        while ($row = $this->database->tabl_hash($result)) {
            $tournaments[] = Tournament::fromRow($row);
        }
        return new Collection($tournaments, $count);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function findParticipatingForUser(int $userId) : Collection
    {
        $sql = "SELECT DISTINCT t.* from wD_Tournaments t INNER JOIN wD_TournamentParticipants s on s.tournamentID = t.id 
	where t.status <> 'Finished' and ( s.userID = " . $userId . " or t.directorID = " . $userId . " or t.coDirectorID = " . $userId . ")";
        $sqlCounter = "select count(distinct t.id) from wD_Tournaments t INNER JOIN wD_TournamentParticipants s on s.tournamentID = t.id 
	where t.status <> 'Finished' and ( s.userID =" . $userId . " or t.directorID = " . $userId . " or t.coDirectorID = " . $userId . ")";

        $tableChecked = $this->database->sql_tabl($sql);
        list($count) = $this->database->sql_row($sqlCounter);

        $tournaments = [];
        while ($row = $this->database->tabl_hash($tableChecked)) {
            $tournaments[] = Tournament::fromRow($row);
        }
        return new Collection($tournaments, $count);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function findSpectatingForUser(int $userId) : Collection
    {
        $sql = "select t.id, t.name, t.status from wD_Tournaments t inner join wD_TournamentSpectators s on s.tournamentID = t.id where t.status <> 'Finished' and s.userID =".$userId;
        $sqlCounter = "select count(1) from wD_Tournaments t inner join wD_TournamentSpectators s on s.tournamentID = t.id where t.status <> 'Finished' and s.userID =".$userId;

        $tableChecked = $this->database->sql_tabl($sql);
        list($count) = $this->database->sql_row($sqlCounter);

        $tournaments = [];
        while ($row = $this->database->tabl_hash($tableChecked)) {
            $tournaments[] = Tournament::fromRow($row);
        }
        return new Collection($tournaments, $count);
    }
}