<?php

namespace Models;

class Tournament extends Base {

    public function __toString()
    {
        return var_export($this->attributes, true);
    }

    public function isRunning()
    {
        return $this->status != 'PreStart';
    }

    public function isActive()
    {
        return $this->status == 'Active';
    }

    public function isRegistrationComplete()
    {
        return $this->status != 'Registration';
    }

    public static function findParticipatingForUser(int $userId) : array
    {
        global $DB;
        $userId = intval($userId);
        
        $sql = "SELECT DISTINCT t.* from wD_Tournaments t INNER JOIN wD_TournamentParticipants s on s.tournamentID = t.id 
	where t.status <> 'Finished' and ( s.userID = " . $userId . " or t.directorID = " . $userId . " or t.coDirectorID = " . $userId . ")";
        $sqlCounter = "select count(distinct t.id) from wD_Tournaments t INNER JOIN wD_TournamentParticipants s on s.tournamentID = t.id 
	where t.status <> 'Finished' and ( s.userID =" . $userId . " or t.directorID = " . $userId . " or t.coDirectorID = " . $userId . ")";

        $tableChecked = $DB->sql_tabl($sql);
        list($resultsParticipating) = $DB->sql_row($sqlCounter);

        $tournaments = [];
        while ($row = $DB->tabl_hash($tableChecked)) {
            $tournaments[] = static::fromRow($row);
        }
        return [
            'entities' => $tournaments,
            'count' => $resultsParticipating,
        ];
    }

    public static function findSpectatingForUser(int $userId) : array
    {
        global $DB;
        $userId = intval($userId);

        $sql = "select t.id, t.name, t.status from wD_Tournaments t inner join wD_TournamentSpectators s on s.tournamentID = t.id where t.status <> 'Finished' and s.userID =".$userId;
        $sqlCounter = "select count(1) from wD_Tournaments t inner join wD_TournamentSpectators s on s.tournamentID = t.id where t.status <> 'Finished' and s.userID =".$userId;

        $tableChecked = $DB->sql_tabl($sql);
        list($resultsParticipating) = $DB->sql_row($sqlCounter);

        $tournaments = [];
        while ($row = $DB->tabl_hash($tableChecked)) {
            $tournaments[] = static::fromRow($row);
        }
        return [
            'entities' => $tournaments,
            'count' => $resultsParticipating,
        ];
    }
}