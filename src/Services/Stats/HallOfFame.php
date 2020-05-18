<?php

namespace Diplomacy\Services\Stats;

class HallOfFame
{
    protected $database;

    public function __construct(\Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        $dataSet = $this->database->sql_tabl("SELECT * FROM wD_Users order BY points DESC LIMIT 100");
        $rankedUsers = [];

        $rank = 1;
        while ($hash = $this->database->tabl_hash($dataSet)) {
            $hash['position'] = $rank;
            $rankedUsers[] = $hash;
            $rank++;
        }
        return $rankedUsers;
    }

    /**
     * @return array
     */
    public function getActiveUsers()
    {
        $sixMonths = time() - 15552000;
        $dataSet = $this->database->sql_tabl("SELECT id, username, points FROM wD_Users WHERE timeLastSessionEnded > ".$sixMonths." order BY points DESC LIMIT 100 ");
        $rankedUsers = [];
        $rank = 1;
        while ($hash = $this->database->tabl_hash($dataSet)) {
            $hash['position'] = $rank;
            $rankedUsers[] = $hash;
            $rank++;
        }
        return $rankedUsers;
    }
}