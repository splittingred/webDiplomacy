<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\TerritoryStatusArchive;

/**
 * Handles operations around graphing of games
 *
 * @package Diplomacy\Services\Games
 */
class GraphService
{
    /**
     * @param int $gameId
     * @param int $mapId
     * @param int $turns
     * @return array
     */
    public function getSupplyCenterOwnershipData(int $gameId, int $mapId, int $turns) : array
    {
        $scCountsByTurn = [];
        for ($turn = 1; $turn < $turns; $turn++) {
            $query = TerritoryStatusArchive::query();
            $query->select(TerritoryStatusArchive::raw('
                wD_TerrStatusArchive.countryID,
                COUNT(wD_TerrStatusArchive.countryID) AS total
            '))
                ->join('wD_Territories', 'wD_Territories.id', '=', 'wD_TerrStatusArchive.terrID')
                ->where('wD_Territories.supply', '=', 'Yes')
                ->where('wD_Territories.coastParentID', '=', TerritoryStatusArchive::raw('wD_Territories.id'))
                ->where('wD_Territories.mapID', '=', $mapId)
                ->where('wD_TerrStatusArchive.gameID', '=', $gameId)
                ->where('wD_TerrStatusArchive.turn', '=', $turn)
                ->groupBy('wD_TerrStatusArchive.countryID');

            $data = $query->get();
            $turnCounts = [];
            foreach ($data as $turnData) {
                $turnCounts[$turnData['countryID']] = $turnData['total'];
            }
            $scCountsByTurn[$turn] = $turnCounts;
        }

        foreach($scCountsByTurn as $turn => $scCountsByCountryID) {
            $turnSCTotal=0;
            foreach($scCountsByCountryID as $countryID=>$scCount)
            {
                $turnSCTotal += $scCount;
            }

            if ($turnSCTotal==0)
            {
                unset($scCountsByTurn[$turn]);
                break;
            }

            $percentLeft=100;
            foreach($scCountsByCountryID as $countryID=>$scCount)
            {
                $percent = floor(100.0*($scCount/$turnSCTotal));

                if ($percent == 0) {
                    if ($percentLeft > 0) {
                        $percentLeft--;
                        $percent = 1;
                        continue;
                    }
                    break;
                }

                $percentLeft -= $percent;

                $scCountsByTurn[$turn][$countryID] = $percent;
            }
        }

        return $scCountsByTurn;
    }
}