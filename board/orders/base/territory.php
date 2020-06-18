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

defined('IN_CODE') or die('This script can not be run by itself.');

/**
 * Holds Territory data for orders, and performs other territory/terrstatus related functions such as
 * de-coasting coastal territory names.
 *
 * @package Base
 * @subpackage Game
 */
class Territory {
	/**
	 * The territory ID
	 *
	 * @var int
	 */
    public $id;

	/**
	 * The territory name
	 *
	 * @var string
	 */
    public $name;

	/**
	 * Coast type: 'No','Parent','Child'
	 *
	 * @var string
	 */
    public $coast;

	/**
	 * 'Coast','Land','Sea'
	 * @var string
	 */
    public $type;

	/**
	 * Supply center present: 'Yes'/'No'
	 *
	 * @var string
	 */
    public $supply;

	/**
	 * Large map x coordinate
	 * @var int
	 */
    public $mapX;

	/**
	 * Large map y coordinate
	 * @var int
	 */
    public $mapY;

	/**
	 * Small map x coordinate
	 * @var int
	 */
    public $smallMapX;

	/**
	 * Small map y coordinate
	 * @var int
	 */
    public $smallMapY;

	/**
	 * The countryID which initially owns this territory
	 * @var int
	 */
    public $countryID;

	/**
	 * The ID of the parent coast, or the own ID if not a coast child.
	 * @var int
	 */
    public $coastParentID;

	/**
	 * @param int/array The array of territory data or the territory ID
	 */
    public function __construct($row)
	{
        global $app;
        $DB = $app->make('DB');

		if( !is_array($row) )
			$row = $DB->sql_hash("SELECT * FROM wD_Territories WHERE id=".intval($row)." AND mapID=".MAPID);

		foreach ($row as $name=>$value)
			$this->{$name} = $value;

		$this->supply = ( $this->supply == 'Yes' );
	}
}