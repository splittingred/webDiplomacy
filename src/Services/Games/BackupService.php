<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;

class BackupService
{
    /**
     * @param Game $game
     * @return Result
     */
    public function create(Game $game): Result
    {
        // TODO: Get this working. Need to create backup table models
        /** @see processGame::backupGame */
//        [
//            'Games'=>'id',
//            'Members'=>'gameID','Orders'=>'gameID','TerrStatus'=>'gameID','Units'=>'gameID',
//            'GameMessages'=>'gameID','TerrStatusArchive'=>'gameID','MovesArchive'=>'gameID'
//        ];
//
//
//
//        foreach(self::$gameTables as $tableName=>$idColName)
//            $DB->sql_put("DELETE FROM wD_Backup_".$tableName." WHERE ".$idColName." = ".$gameID);
//
//        foreach(self::$gameTables as $tableName=>$idColName)
//            $DB->sql_put(
//                "INSERT INTO wD_Backup_".$tableName."
//				SELECT * FROM wD_".$tableName." WHERE ".$idColName." = ".$gameID
//            );

        return new Success();
    }
}

