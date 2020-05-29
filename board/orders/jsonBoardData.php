<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

use Diplomacy\Models\TerritoryStatus;
use Diplomacy\Models\Unit;

/**
 * Generates the JSON data used to generate orders for a certain game, used by OrderInterface.
 *
 */
class jsonBoardData
{
	public static function getBoardTurnData($gameID)
	{
		return "function loadBoardTurnData() {\n".self::getUnits($gameID)."\n\n".self::getTerrStatus($gameID)."\n}\n";
	}

	protected static function getUnits($gameID) : string
	{
		$units = Unit::query()->select(['id', 'terrID', 'countryID', 'type'])->forGame($gameID);

        $unitRows = [];
		foreach ($units as $unit)
		{
			$unitRows[$unit->id] = $unit->toArray();
		}

		return 'Units = $H('.json_encode($unitRows).');';
	}

	protected static function getTerrStatus($gameID) : string
	{
		$territoryStatuses = [];
		$statuses = TerritoryStatus::query()
            ->select(['terrID as id', 'standoff', 'occupiedFromTerrID', 'occupyingUnitID as unitID', 'countryID as ownerCountryID'])
            ->forGame($gameID);

		foreach ($statuses as $status) {
            $territoryStatuses[] = $status->toArray();
        }

		return 'TerrStatus = '.json_encode($territoryStatuses).';';
	}
}
