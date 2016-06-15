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

/**
 * @package Board
 */

require_once('header.php');
libHTML::startHTML();

$required = array('name','England','France','Italy','Germany','Austria','Turkey','Russia','videoLink','mapLink');

if ($User->type['FtfTD'] && isset($_POST['name'])) {
		$input = array();
        
        foreach($required as $requiredName)
        {
            if ( isset($_POST[$requiredName]) )
            {
                $input[$requiredName] = $DB->escape($_POST[$requiredName]);
            }
            else
            {
              throw new Exception(l_t('The variable "%s" is needed to create a game, but was not entered.',$requiredName));
            }
		}
		$names = '';
		$values = '';
		$set = '';
		$comma = '';
		foreach($input as $key=>$value) {
				$names .= $comma . "$key";
				$values .= $comma . "'$value'";
				$set .= $comma . $key. "='$value'";
				$comma = ', ';
		}
		if(isset($_REQUEST['ftfID'])) {
				$ftfID = (int)$_REQUEST['ftfID'];
			   $DB->sql_put("UPDATE wD_FtfLinks SET $set WHERE id=$ftfID;");
		} else {
				// Create
				$DB->sql_put("INSERT INTO wD_FtfLinks SET $set;");
		}
}

if ( ! isset($_REQUEST['ftfID']) )
{
		print "<div class='content-bare content-board-header'>
				<div class='boardHeader'>
					<div class='titleBar'>
					<span class='gameName'>Board Listings</span>
					</div>
					</div>
					</div>";
		print "<div class='content content-follow-on'><ul>";

		$tabl = $DB->sql_tabl('SELECT id, name FROM wD_FtfLinks');
		if ($DB->last_affected() == 0) 
		{
				print 'No current face-to-face games.';
		}
    while(list($id, $name)=$DB->tabl_row($tabl))
    {
		print "<li><a href='?ftfID=$id'>$name</a></li>";
	}

} else {

		$ftfID = (int)$_REQUEST['ftfID'];
    	$ftfBoard = $DB->sql_hash("SELECT * FROM wD_FtfLinks where id=$ftfID");

		print "<div class='content-bare content-board-header'>
				<div class='boardHeader'>
					<div class='titleBar'>
					<span class='gameName'>".$ftfBoard['name']."</span>
					</div>
					</div>
					</div>";
		print "<div class='content content-follow-on'>";

		foreach (array('England','France','Italy','Germany','Austria','Turkey','Russia') as $country) {
         	print '<b>'. $country . ':</b> ' . $ftfBoard[$country] . '<br/>';
		}
		print '<br/><br/>';
		print '<b>Twitter hashtag:</b> #'.str_replace(' ','',$ftfBoard['name']).'<br/>';
		if ($ftfBoard['mapLink'] != '') {
				if (substr($ftfBoard['mapLink'],0,4) != 'http') {
						$ftfBoard['mapLink'] = 'http://' . $ftfBoard['mapLink'];
				}
				print '<a href="'.$ftfBoard['mapLink'].'">View map</a><br/>';
		}
		if ($ftfBoard['videoLink'] != '') {
				if (substr($ftfBoard['videoLink'],0,4) != 'http') {
						$ftfBoard['videoLink'] = 'http://' . $ftfBoard['videoLink'];
				}
				print '<a href="'.$ftfBoard['videoLink'].'">View video</a><br/>';
		}
				print '<br><br/><a href="ftf.php">Back to board listings</a><br/>';
}

if ($User->type['FtfTD']) {

		if ( isset($_REQUEST['ftfID']) )
		{
				print "<div class='hr'></div><h3>Edit info</h3><form method='post'>";
		} else {
				print "<div class='hr'></div><h3>Create new board</h3><form method='post'>";
				$ftfBoard = array();
				foreach ($required as $key) {
					$ftfBoard[$key] = '';
				}
		}
				print "<b>Board name:</b><input type='text' name='name' value='".$ftfBoard['name']."'><br/><br/>";
				
				foreach (array('England','France','Italy','Germany','Austria','Turkey','Russia') as $country) {
					print '<b>'. $country . ':</b> <input type="text" name="'.$country.'" value="' . $ftfBoard[$country] . '"><br/>';
				}


				print "<br/>";
				print "<b>Map link:</b><input type='text' name='mapLink' value='".$ftfBoard['mapLink']."'><br/>";
				print "<b>Video link:</b><input type='text' name='videoLink' value='".$ftfBoard['videoLink']."'><br/><br/>";
				print '<input type="submit" class="form-submit" value="Update">';
				print "</form>";
}
print "</div>";


		libHTML::footer();

		?>
