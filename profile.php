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
 * @package Base
 */

use Diplomacy\Services\Games\MessagesService as GameMessagesService;

require_once('header.php');

require_once(l_r('objects/game.php'));
require_once(l_r('gamepanel/game.php'));

if ( isset($_REQUEST['userID']) && intval($_REQUEST['userID'])>0 )
{
	$userID = (int)$_REQUEST['userID'];
}
elseif( isset($_REQUEST['searchUser']) )
{
	libAuth::resourceLimiter('user search',1);

	if( !is_array($_REQUEST['searchUser']) )
		throw new Exception(l_t("Invalid search data submitted."));

	$searchUser = $_REQUEST['searchUser'];

	$searchUserValid=array();
	if ( isset($searchUser['id']) && $searchUser['id'] && strlen($searchUser['id']) )
		$searchUserValid['id'] = (int)$searchUser['id'];

	if ( isset($searchUser['username']) && $searchUser['username'] && strlen($searchUser['username']) )
		$searchUserValid['username'] = $DB->escape($searchUser['username']);

	if ( isset($searchUser['email']) && $searchUser['email'] && strlen($searchUser['email']) )
		$searchUserValid['email'] = $DB->escape($searchUser['email']);

	unset($searchUser);

	$whereSQL=array();
	foreach($searchUserValid as $searchFieldName=>$searchFieldValue)
	{
		if( $searchFieldName == 'id' )
			$whereSQL[] = $searchFieldName." = ".$searchFieldValue;
		else
			$whereSQL[] = $searchFieldName." LIKE '".$searchFieldValue."'";
	}

	$userID=false;

	if( count($whereSQL) )
	{
		list($foundUserID) = $DB->sql_row("SELECT id FROM wD_Users WHERE ".implode(' OR ', $whereSQL)." LIMIT 1");
		if( !isset($foundUserID) || !$foundUserID )
		{
			$searchReturn = l_t('No users found matching the given search parameters.');
		}
		else
		{
			$searchReturn = l_t('Matching user found!');
			$userID=$foundUserID;
		}
	}
}
else
{
	$userID = false;
}

if ( !$userID )
{
	libHTML::starthtml(l_t('Search for user'));

	print libHTML::pageTitle(l_t('Search for user'),l_t('Search for a user using either their ID, username, or e-mail address.'));
	?>

	<?php if( isset($searchReturn) ) print '<p class="notice">'.$searchReturn.'</p>'; ?>
	<div class = "userSearch_show">
		<form action="profile.php" method="post">
		<ul class="formlist">
			<p>
				<strong><?php print l_t('User ID:'); ?></strong> </br>
				<input class="gameCreate" type="text" name="searchUser[id]" value="" size="10">
			</p>

			<p>
				<strong><?php print l_t('Username:'); ?></strong> </br>
				<input class="gameCreate" type="text" name="searchUser[username]" value="" size="40">
				</br>
				<?php print l_t('(Not case sensitive, but otherwise must match exactly.)'); ?>
			</p>

			<p>
				<strong><?php print l_t('Email address:'); ?></strong> </br>
				<input class="gameCreate" type="text" name="searchUser[email]" value="" size="40">
				</br>
				<?php print l_t('(Not case sensitive, but otherwise must match exactly.)'); ?>
			</p>
			<p>
				<input type="submit" class="green-Submit" value="<?php print l_t('Search'); ?>">
			</p>
		</ul>

		</form>
	</div>
	</div>
	<?php
	libHTML::footer();
}
else
{
	try
	{
		$UserProfile = new User($userID);
	}
	catch (Exception $e)
	{
		libHTML::error(l_t("Invalid user ID given."));
	}
}

if ( ! $UserProfile->type['User'] && !$UserProfile->isBanned() )
{
	$message = l_t('Cannot display profile: The specified account #%s is not an active user;',$userID).' ';
	if( $UserProfile->type['Guest'] )
		$message .= l_t('it\'s a guest account, used by unregistered people to '.
			'view the server without interacting.');
	elseif( $UserProfile->type['System'] )
		$message .= l_t('it\'s a system account, without a real human using it.');
	else
		$message .= l_t('in fact I\'m not sure what this account is...');

	foreach($UserProfile->type as $name=>$on)
	{
		if ( $on )
			$message .= l_t($name).', ';
	}
	libHTML::error($message);
}

libHTML::starthtml();

print '<div class="content">';

if( isset($searchReturn) )
	print '<p class="notice">'.$searchReturn.'</p>';

if ( isset($_REQUEST['detail']) )
{
	print '<p>(<a href="profile.php?userID='.$UserProfile->id.'">'.l_t('Back').'</a>)</p>';

	switch($_REQUEST['detail'])
	{
		case 'reports':
			if ( $User->isModerator() )
			{
				require_once(l_r('lib/modnotes.php'));
				libModNotes::checkDeleteNote();
				libModNotes::checkInsertNote();
				print libModNotes::reportBoxHTML('User', $UserProfile->id);
				print libModNotes::reportsDisplay('User', $UserProfile->id);
			}
		break;
	}

	print '</div>';
?>
<script type="text/javascript">
   var coll = document.getElementsByClassName("profile_title");
   var searchCounter;

   for (searchCounter = 0; searchCounter < coll.length; searchCounter++) {
     coll[searchCounter].addEventListener("click", function() {
       this.classList.toggle("active");
       var content = this.nextElementSibling;
   		if (content.style.display === "block") { content.style.display = "none"; }
   		else { content.style.display = "block"; }
     });
   }
</script>
<?php

	libHTML::footer();
}

print '<div>';
print '<h2 class = "profileUsername">'.$UserProfile->username;

if (!isset(Config::$customForumURL))
{
	if ( $User->type['User'] && $UserProfile->type['User'] && ! ( $User->id == $UserProfile->id || $UserProfile->type['Moderator'] || $UserProfile->type['Guest'] || $UserProfile->type['Admin'] ) )
	{
		$userMuted = $User->isUserMuted($UserProfile->id);

		print '<a name="mute"></a>';
		if( isset($_REQUEST['toggleMute'])) 
		{
			$User->toggleUserMute($UserProfile->id);
			$userMuted = !$userMuted;
		}
		$muteURL = 'profile.php?userID='.$UserProfile->id.'&toggleMute=on&rand='.rand(0,99999).'#mute';
		print ' '.($userMuted ? libHTML::muted($muteURL) : libHTML::unmuted($muteURL));
	}
}
print '</h2>';
print '<div class = "profile-show">';
print '<div class="rightHalf">';

$rankingDetails = $UserProfile->rankingDetails();
$rankingDetailsClassic = $UserProfile->rankingDetailsClassic();
$rankingDetailsClassicPress = $UserProfile->rankingDetailsClassicPress();
$rankingDetailsClassicGunboat = $UserProfile->rankingDetailsClassicGunboat();
$rankingDetailsClassicRanked = $UserProfile->rankingDetailsClassicRanked();
$rankingDetailsVariants = $UserProfile->rankingDetailsVariants();

$showAnon = ($UserProfile->id == $User->id || $User->type['Moderator']);

print '<ul class="formlist">';

print '<li title="Diplomat/Mastermind/Pro/Experienced/Member/Casual/Puppet (top 5/10/20/50/90/100%/not ranked)"><strong>'.l_t('Rank:').'</strong> '.$rankingDetails['rank'].'</li>';

if ( $rankingDetails['position'] < $rankingDetails['rankingPlayers'] )
	print '<li><strong>'.l_t('Position:').'</strong> '.$rankingDetails['position'].'/'.
		$rankingDetails['rankingPlayers'].' '.l_t('(top %s%%)',$rankingDetails['percentile']).'</li>';

print '<li><strong>'.l_t('Available points:').'</strong> '.number_format($UserProfile->points).' '.libHTML::points().'</li>';

print '<li><strong>'.l_t('Points in play:').'</strong> '.number_format(($rankingDetails['worth']-$UserProfile->points-($showAnon ? 0 : $rankingDetails['anon']['points']))).' '.libHTML::points().'</li>';

print '<li><strong>'.l_t('Total points:').'</strong> '.number_format($rankingDetails['worth']).' '.libHTML::points().'</li>';

if( $UserProfile->type['DonatorPlatinum'] )
	$donatorMarker = libHTML::platinum().' - <strong>'.l_t('Platinum').'</strong>';
elseif( $UserProfile->type['DonatorGold'] )
	$donatorMarker = libHTML::gold().' - <strong>'.l_t('Gold').'</strong>';
elseif( $UserProfile->type['DonatorSilver'] )
	$donatorMarker = libHTML::silver().' - '.l_t('Silver');
elseif( $UserProfile->type['DonatorBronze'] )
	$donatorMarker = libHTML::bronze().' - '.l_t('Bronze');
else
	$donatorMarker = false;

if( $donatorMarker )
	print '<li>&nbsp;</li><li><strong>'.l_t('Donator:').'</strong> '.$donatorMarker.'</li>';

print '<li>&nbsp;</li>';

$messagesService = new GameMessagesService();
$posts = $messagesService->totalForUser($UserProfile->id);
print '<li><strong>'.l_t('Game messages:').'</strong> '.number_format($posts).'</li>';

print '<li>&nbsp;</li>';
$total = 0;
$includeStatus=array('Won','Drawn','Survived','Defeated','Resigned');
foreach($rankingDetails['stats'] as $name => $status)
{
	if ( !in_array($name, $includeStatus) ) continue;

	$total += $status;
	if (!$showAnon && isset($rankingDetails['anon'][$name]))
		$total -= $rankingDetails['anon'][$name];
}

if( $total )
{
	print '<div class = "profile_title">';
	print '<li><strong>'.l_t('All Game stats:').'</strong> </div><div class = "profile_content_show">';

	// Shows each of the game details
	foreach($includeStatus as $name)
	{
		if ( !array_key_exists($name, $rankingDetails['stats']) ) continue;

		$status = $rankingDetails['stats'][$name];

		if (!$showAnon && isset($rankingDetails['anon'][$name]))
			$status -= $rankingDetails['anon'][$name];

		print '<li>'.l_t($name.': <strong>%s</strong>',$status);
		print ' ( '.round(($status/$total)*100).'% )';
		print '</li>';
	}
	print '<li>'.l_t('Total (finished): <strong>%s</strong>',$total).'</li>';
	print '<br>';

	// This shows the Playing/Civil Disorder and CD takeover stats.
	foreach($rankingDetails['stats'] as $name => $status)
	{
		if ( in_array($name, $includeStatus) ) continue;

		if (!$showAnon && isset($rankingDetails['anon'][$name]))
			$status -= $rankingDetails['anon'][$name];
		print '<li>'.l_t($name.': <strong>%s</strong>',$status).'</li>';
	}
	print '</li>';
	print '</div>';

	// Get a count of the number of classic games that have been played.
	$totalClassic = 0;
	foreach($rankingDetailsClassic['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		$totalClassic += $status;
	}

	// Print out Classic stats if any classic games have been finished.
	if( $totalClassic )
	{
		print '<div class = "profile_title">';
		print '<li><strong>'.l_t('Classic:').'</strong></div><div class = "profile_content">';
		foreach($includeStatus as $name)
		{
			if ( !array_key_exists($name, $rankingDetailsClassic['stats']) ) continue;

			$status = $rankingDetailsClassic['stats'][$name];

			print '<li>'.l_t($name.': <strong>%s</strong>',$status);
			print ' ( '.round(($status/$totalClassic)*100).'% )';
			print '</li>';
		}
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$totalClassic).'</li>';
		print '</li>';
		print '</div>';
	}

	// Get a count of the number of classic press games that have been played.
	$totalClassicPress = 0;
	foreach($rankingDetailsClassicPress['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		$totalClassicPress += $status;
	}

	// Print out Classic Press stats if any classic press games have been finished.
	if( $totalClassicPress )
	{
		print '<div class = "profile_title">';
		print '<li><strong>'.l_t('Classic Press:').'</strong> </div><div class = "profile_content">';

		foreach($includeStatus as $name)
		{
			if ( !array_key_exists($name, $rankingDetailsClassicPress['stats']) ) continue;

			$status = $rankingDetailsClassicPress['stats'][$name];

			print '<li>'.l_t($name.': <strong>%s</strong>',$status);
			print ' ( '.round(($status/$totalClassicPress)*100).'% )';
			print '</li>';
		}
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$totalClassicPress).'</li>';
		print '</li>';
		print '</div>';
	}

	// Get a count of the number of classic gunboat games that have been played.
	$totalClassicGunboat = 0;
	foreach($rankingDetailsClassicGunboat['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		$totalClassicGunboat += $status;
	}

	// Print out Classic Gunboat stats if any classic gunboat games have been finished.
	if( $totalClassicGunboat )
	{
		print '<div class = "profile_title">';
		print '<li><strong>'.l_t('Classic Gunboat:').'</strong> </div><div class = "profile_content">';

		foreach($includeStatus as $name)
		{
			if ( !array_key_exists($name, $rankingDetailsClassicGunboat['stats']) ) continue;

			$status = $rankingDetailsClassicGunboat['stats'][$name];

			print '<li>'.l_t($name.': <strong>%s</strong>',$status);
			print ' ( '.round(($status/$totalClassicGunboat)*100).'% )';
			print '</li>';
		}
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$totalClassicGunboat).'</li>';
		print '</li>';
		print '</div>';
	}

	// Get a count of the number of classic ranked games that have been played.
	$totalClassicRanked = 0;
	foreach($rankingDetailsClassicRanked['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		$totalClassicRanked += $status;
	}

	// Print out Classic Ranked stats if any classic ranked games have been finished.
	if( $totalClassicRanked )
	{
		print '<div class = "profile_title">';
		print '<li><strong>'.l_t('Classic Ranked:').'</strong> </div><div class = "profile_content">';

		foreach($includeStatus as $name)
		{
			if ( !array_key_exists($name, $rankingDetailsClassicRanked['stats']) ) continue;

			$status = $rankingDetailsClassicRanked['stats'][$name];

			print '<li>'.l_t($name.': <strong>%s</strong>',$status);
			print ' ( '.round(($status/$totalClassicRanked)*100).'% )';
			print '</li>';
		}
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$totalClassicRanked).'</li>';
		print '</li>';
		print '</div>';
	}

	// Get a count of the number of classic games that have been played.
	$totalVariants = 0;
	foreach($rankingDetailsVariants['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		$totalVariants += $status;
	}

	// Print out Variant stats if any variant games have been finished.
	if( $totalVariants )
	{
		print '<div class = "profile_title">';
		print '<li><strong>'.l_t('Variant stats:').'</strong> </div> <div class = "profile_content">';

		foreach($includeStatus as $name)
		{
			if ( !array_key_exists($name, $rankingDetailsVariants['stats']) ) continue;

			$status = $rankingDetailsVariants['stats'][$name];

			print '<li>'.l_t($name.': <strong>%s</strong>',$status);
			print ' ( '.round(($status/$totalVariants)*100).'% )';
			print '</li>';
		}
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$totalVariants).'</li>';
		print '</li>';
		print '</div>';
	}

	print '</br>';
	if ($User->isModerator() || $User->id == $UserProfile->id )
	{
		print '<li><strong>'.l_t('Reliability:').' (<a href="/users/'.$UserProfile->id.'/civil-disorders">'.l_t('Reliability Explained').'</a>) </strong>';
	}
	else
	{
		print '<li><strong>'.l_t('Reliability:').'</strong>';
	}

	if ( $User->isModerator() || $User->id == $UserProfile->id )
	{
		$recentMissedTurns = $UserProfile->getRecentUnExcusedMissedTurns();
		$allMissedTurns = $UserProfile->getYearlyUnExcusedMissedTurns();
		If ($recentMissedTurns > 0)
		{
			print '<li class="rr-profile-info"> Recent un-excused delays: ' . $recentMissedTurns.'</font></li>';
			print '<li class="rr-profile-info"> Recent delay RR penalty: ' . ($recentMissedTurns*6).'%</font></li>';
			print '<li class="rr-profile-info"> Yearly delay RR penalty: ' . ($allMissedTurns*5).'%</font></li>';
		}
		print '<li style="font-size:13px">'.l_t('Un-excused delays/phases:').' <strong>'.$allMissedTurns.'/'.$UserProfile->yearlyPhaseCount.'</strong></li>';
	}
	print '<li style="font-size:13px">'.l_t('Reliability rating:').' <strong>'.($UserProfile->reliabilityRating).'%</strong>';

	print '</li>';

	print '</li>';
}

print '</ul></div>';

// Regular user info starts here:
print '<div class="leftHalf" style="width:50%">';

if($UserProfile->isBanned())
{
    print '<p><strong>' . l_t('Banned') . '</strong></p>';
}

if( $UserProfile->isBot() )
{
	print '<p class="profileCommentURL">Bot User</p>';
}

if ($User->isModerator())
{
	$lastCheckedBy = $UserProfile->modLastCheckedBy();
	$modLastCheckedOn = $UserProfile->modLastCheckedOn();
	list($previousUsernames) = $DB->sql_row(
		"SELECT GROUP_CONCAT(DISTINCT oldUsername SEPARATOR ', ') FROM wD_UsernameHistory WHERE userID = ".$UserProfile->id
	);
	list($previousEmails) = $DB->sql_row(
		"SELECT GROUP_CONCAT(DISTINCT oldEmail SEPARATOR ', ') FROM wD_EmailHistory WHERE userID = ".$UserProfile->id
	);

	if($UserProfile->modLastCheckedOn() > 0 && $lastCheckedBy > 0)
	{
		$modUsername = \Diplomacy\Models\User::where('id', '=', $lastCheckedBy)->pluck('username')[0];
		print '<p class="profileCommentURL">Investigated: '.libTime::text($modLastCheckedOn).', by: <a href="/profile.php?userID='.$lastCheckedBy.'">'.$modUsername.'</a></p>';
	}
	else
	{
		print '<p>Investigated: Never</p>';
	}

	if ($UserProfile->userIsTempBanned())
	{
		print '<p>Temp Ban Time: '.libTime::remainingText($UserProfile->tempBan).' Reason: '.$UserProfile->tempBanReason.'</p>';
	}

	if (!empty($previousUsernames))
	{
		print '<p class="profileCommentURL">Previous Usernames: '.$previousUsernames.'</p>';
	}

	if (!empty($previousEmails))
	{
		print '<p class="profileCommentURL">Previous Emails: '.$previousEmails.'</p>';
	}

	if($UserProfile->qualifiesForEmergency() )
	{
		print '<p class="profileCommentURL">User qualifies for emergency pause</p>';
	}
	else if ($UserProfile->emergencyPauseDate == 1)
	{
		print '<p class="profileCommentURL">User is mod banned from emergency pause</p>';
	}
	else
	{
		print '<p class="profileCommentURL">User does not qualify for emergency pause</p>';
	}
}

if ( $UserProfile->comment )
	print '<p class="profileComment">"'.$UserProfile->comment.'"</p>';

print '<p><ul class="formlist">';

if ( $UserProfile->isModerator() ||  $UserProfile->type['ForumModerator'] || $UserProfile->isAdmin() )
{
	print '<li><strong>'.l_t('Mod/Admin team').'</strong></li>';
	print '<li>'.l_t('The best way to get moderator assistance is using our built in <a href="contactUsDirect.php">help page</a>. Please do not message
	moderators directly for help.').'</li>';
	print '<li>&nbsp;</li>';
}

if ( $UserProfile->online || time() - (24*60*60) < $UserProfile->timeLastSessionEnded)
	print '<li><strong>'.l_t('Visited in last 24 hours').'</strong></li>';
else
	print '<li><strong>'.l_t('Last visited:').'</strong> '.libTime::text($UserProfile->timeLastSessionEnded).'</li>';

if (!isset(Config::$customForumURL))
{
	// Doing the query this way makes MySQL use the type, fromUserID index
	list($posts) = $DB->sql_row(
		"SELECT (
			SELECT COUNT(fromUserID) FROM `wD_ForumMessages` WHERE type='ThreadStart' AND fromUserID = ".$UserProfile->id."
			) + (
			SELECT COUNT(fromUserID) FROM `wD_ForumMessages` WHERE type='ThreadReply' AND fromUserID = ".$UserProfile->id."
			)");

	if( is_null($posts) ) $posts=0;
	list($likes) = $DB->sql_row("SELECT COUNT(*) FROM wD_LikePost WHERE userID=".$UserProfile->id);
	list($liked) = $DB->sql_row("SELECT COUNT(*) FROM wD_ForumMessages fm
		INNER JOIN wD_LikePost lp ON lp.likeMessageID = fm.id
		WHERE fm.fromUserID=".$UserProfile->id);
	$likes = ($likes ? '<strong>'.l_t('Likes:').'</strong> '.$likes : '');
	$liked = ($liked ? '<strong>'.l_t('Liked:').'</strong> '.$liked : '');

	print '<li><strong>'.l_t('Forum posts:').'</strong> '.$posts.'<br />';

	print '<br/>'.implode(' / ',array($likes,$liked)).'</li>';
	unset($likes,$liked);
}

print '<li><strong>'.l_t('Joined:').'</strong> '.$UserProfile->timeJoinedtxt().'</li>';
print '<li><strong>'.l_t('User ID#:').'</strong> '.$UserProfile->id.'</li>';
if( $User->isModerator() )
{
	print '<li><strong>'.l_t('E-mail:').'</strong>
			'.$UserProfile->email.($UserProfile->hideEmail == 'No' ? '' : ' <em>'.l_t('(hidden for non-mods)').'</em>').'</li>';
}
else if ( $UserProfile->hideEmail == 'No' )
{
	$emailCacheFilename = libCache::dirID('users',$UserProfile->id).'/email.png';
	if( !file_exists($emailCacheFilename) )
	{
		$image = imagecreate( strlen($UserProfile->email) *8, 15);
		$white = imagecolorallocate( $image, 255, 255, 255);
		$black = imagecolorallocate( $image, 0, 0, 0 );

		imagestring( $image, 2, 10, 1, $UserProfile->email, $black );

		imagepng($image, $emailCacheFilename);
	}

	print '<li><strong>'.l_t('E-mail:').'</strong>
			<img src="/'.$emailCacheFilename.'" alt="'.l_t('[E-mail address image]').'" title="'.l_t('To protect e-mails from spambots they are embedded in an image').'" >
		</li>';
}

if ( $UserProfile->hideEmail != 'No' )
{
	$emailCacheFilename = libCache::dirID('users',$UserProfile->id).'/email.png';

	if( file_exists($emailCacheFilename) )
		unlink($emailCacheFilename);
}

print '<li>&nbsp;</li>';

//print '<li><a href="profile.php?detail=reports&userID='.$UserProfile->id.'" class="light">View/post a moderator report</a></li>';

//print '<li>&nbsp;</li>';

print '</li></ul></p></div><div style="clear:both"></div></div>';

print '<div id="profile-separator"></div>';


// Start interactive area:

if ( $User->isModerator() && $User->id != $UserProfile->id )
{
	$modActions = [];

	if ($User->isAdmin())
		$modActions[] = '<a href="index.php?auid='.$UserProfile->id.'">'.l_t('Enter this user\'s account').'</a>';

	$modActions[] = libHTML::admincpType('User',$UserProfile->id);

	if( !$UserProfile->type['Admin'] && ( $User->type['Admin'] || !$UserProfile->type['Moderator'] ) )
		$modActions[] = libHTML::admincp('banUser',array('userID'=>$UserProfile->id), l_t('Ban user'));

	$modActions[] = '<a href="admincp.php?tab=Multi-accounts&aUserID='.$UserProfile->id.'" class="light">'.
		l_t('Enter multi-account finder').'</a>';

	if($modActions)
	{
		print '<p class="notice">';
		print implode(' - ', $modActions);
		print '</p>';
	}
}

if( !$UserProfile->type['Admin'] && ( $User->type['Admin'] || $User->type['ForumModerator'] ) )
{
	$silences = $UserProfile->getSilences();

	print '<p><ul class="formlist"><li><strong>'.l_t('Silences:').'</strong></li><li>';

	if( count($silences) == 0 )
		print l_t('No silences against this user.').'</p>';
	else
	{
		print '<ul class="formlist">';
		foreach($silences as $silence) {
			// There should only be one active silence displayed; other active silences could be misleading
			if( !$silence->isEnabled() || $silence->id == $UserProfile->silenceID )
				print '<li>'.$silence->toString().'</li>';
		}
		print '</ul>';
	}

	print '</li><li>';
	print libHTML::admincp('createUserSilence',array('userID'=>$UserProfile->id,'reason'=>''),l_t('Silence user'));
	print '</li></ul></p>';
}

if( !isset(Config::$customForumURL) )
{
	if ( $User->type['User'] && $User->id != $UserProfile->id)
	{
		print '<div class="hr"></div>';
		print '<a name="messagebox"></a>';
		if ( isset($_REQUEST['message']) && $_REQUEST['message'] )
		{
			if ( ! libHTML::checkTicket() )
			{
				print '<p class="notice">'.l_t('You seem to be sending the same message again, this may happen if you refresh '.
					'the page after sending a message.').'</p>';
			}
			else
			{
				if ( $UserProfile->sendPM($User, $_REQUEST['message']) )
				{
	                print '<p class="notice">'.l_t('Private message sent successfully.').'</p>';
	            }
	            else
	            {
	                print '<p class="notice">'.l_t('Private message could not be sent. You may be silenced or muted.').'</p>';
	            }
			}
		}
		print '<div style="margin-left:20px"><ul class="formlist">';
		print '<li class="formlisttitle">'.l_t('Send private-message:').'</li>
			<li class="formlistdesc">'.l_t('Send a message to this user.').'</li>';
		print '<form action="profile.php?userID='.$UserProfile->id.'#messagebox" method="post">
			<input type="hidden" name="formTicket" value="'.libHTML::formTicket().'" />
			<textarea name="message" style="width:80%" rows="4"></textarea></li>
			<li class="formlistfield"><input type="submit" class="form-submit" value="'.l_t('Send').'" /></li>
			</form>
			</ul>
			</div>';
	}
}
else
{
	if ( $User->type['User'] && $User->id != $UserProfile->id)
	{
		list($newForumId) = $DB->sql_row("SELECT user_id FROM `phpbb_users` WHERE webdip_user_id = ".$UserProfile->id);
		if ($newForumId > 0)
		{
			print '
			<div id="profile-forum-link-container">
				<div class="profile-forum-links">
					<a class="profile-link" href="/contrib/phpBB3/memberlist.php?mode=viewprofile&u='.$newForumId.'">
						<button class="form-submit" id="view-forum-profile">
							New Forum Profile
						</button>
					</a>
				</div>';
			print '
				<div class="profile-forum-links">
					<a class="profile-link" href="/contrib/phpBB3/ucp.php?i=pm&mode=compose&u='.$newForumId.'">
						<button class="form-submit" id="send-pm">
							Send a message to this user
						</button>
					</a>
				</div>
			</div>';
		}
		else
		{
			print '<p class="profileCommentURL">This user cannot currently receive messages.</p>';
		}
	}
}
print '</div>';
?>

<script type="text/javascript">
   var coll = document.getElementsByClassName("profile_title");
   var searchCounter;

   for (searchCounter = 0; searchCounter < coll.length; searchCounter++) {
     coll[searchCounter].addEventListener("click", function() {
       this.classList.toggle("active");
       var content = this.nextElementSibling;
   		if (content.style.display === "block") { content.style.display = "none"; }
   		else { content.style.display = "block"; }
     });
   }
</script>
<?php

libHTML::pagebreak();

print '<h3>'.l_t('%s\'s games',$UserProfile->username).' '.( $User->type['User'] ? '(<a href="gamelistings.php?userID='.$UserProfile->id.'&gamelistType=Search">'.l_t('Search').'</a>)' : '' ).'</h3>';

$pagenum = 1;
$resultsPerPage = 20;
$maxPage = 0;
$totalResults = 0;
$sortCol = 'id';
$sortType = 'desc';

if ( isset($_REQUEST['sortCol']))
{
	if ($_REQUEST['sortCol'] == 'name') { $sortCol='name'; }
	else if ($_REQUEST['sortCol'] == 'pot') { $sortCol='pot'; }
	else if ($_REQUEST['sortCol'] == 'phaseMinutes') { $sortCol='phaseMinutes'; }
	else if ($_REQUEST['sortCol'] == 'minimumBet') {$sortCol='minimumBet'; }
	else if ($_REQUEST['sortCol'] == 'minimumReliabilityRating') {$sortCol='minimumReliabilityRating'; }
	else if ($_REQUEST['sortCol'] == 'watchedGames') {$sortCol='watchedGames'; }
	else if ($_REQUEST['sortCol'] == 'turn') {$sortCol='turn'; }
	else if ($_REQUEST['sortCol'] == 'processTime') {$sortCol='processTime'; }
}
if ( isset($_REQUEST['sortType'])) { if ($_REQUEST['sortType'] == 'asc') { $sortType='asc'; } }
if ( isset($_REQUEST['pagenum'])) { $pagenum=(int)$_REQUEST['pagenum']; }

$SQL = "SELECT g.*, (SELECT count(1) FROM wD_WatchedGames w WHERE w.gameID = g.id) AS watchedGames FROM wD_Games g INNER JOIN wD_Members m ON m.gameID = g.id
	WHERE m.userID = ".$UserProfile->id;
$SQLCounter = "SELECT count(1) FROM wD_Games g INNER JOIN wD_Members m ON m.gameID = g.id
	WHERE m.userID = ".$UserProfile->id;
if($User->id != $UserProfile->id && !$User->type['Moderator'])
{
	$SQL .= " AND (g.anon = 'No' OR g.phase = 'Finished')";
	$SQLCounter .= " AND (g.anon = 'No' OR g.phase = 'Finished')";
}
$SQL = $SQL . " ORDER BY ";
if ($sortCol <> 'watchedGames' && $sortCol <> 'processTime' && $sortCol <> 'minimumBet') {$SQL .= "g.";}
$ordering = $sortCol;
if ($sortCol == 'processTime') {$ordering = "(CASE WHEN g.processStatus = 'Paused' THEN (g.pauseTimeRemaining + ".time().") ELSE g.processTime END)";}
elseif ($sortCol == 'minimumBet') {$ordering = "(SELECT m4.bet FROM wD_Members m4 WHERE m4.gameID = g.id AND m4.bet > 0 LIMIT 1)";}
$SQL = $SQL . $ordering." ".$sortType." ";
$SQL = $SQL . " Limit ". ($resultsPerPage * ($pagenum - 1)) . "," . $resultsPerPage .";";

$tabl = $DB->sql_tabl($SQL);
list($totalResults) = $DB->sql_row($SQLCounter);
$maxPage = ceil($totalResults / $resultsPerPage);
print "<a name='results'></a>";

if($totalResults == 0)
{
	print l_t('No games found for this profile.');
}
else
{
	print '<center><b> Showing results '.number_format(min(((($pagenum - 1) * $resultsPerPage)+1),$totalResults)).' to '.number_format(min(($pagenum * $resultsPerPage),$totalResults)).' of '.number_format($totalResults).' total results. </b></center></br>';
	printPageBar($pagenum, $maxPage, $sortCol, $sortType, $sortBar = True);

	print '<div class="gamesList">';
	while( $row = $DB->tabl_hash($tabl) )
	{
		$Variant = libVariant::loadFromVariantID($row['variantID']);
		$G = $Variant->panelGame($row);
		print $G->summary(false);
	}
	print '</div>';

	print '</br>';
	printPageBar($pagenum, $maxPage, $sortCol, $sortType);
}

print '</div>';

function printPageBar($pagenum, $maxPage, $sortCol, $sortType, $sortBar = False)
{
	if ($pagenum > 3)
	{
		printPageButton(1,False);
	}
	if ($pagenum > 4)
	{
		print "...";
	}
	if ($pagenum > 2)
	{
		printPageButton($pagenum-2, False);
	}
	if ($pagenum > 1)
	{
		printPageButton($pagenum-1, False);
	}
	if ($maxPage > 1)
	{
		printPageButton($pagenum, True);
	}
	if ($pagenum < $maxPage)
	{
		printPageButton($pagenum+1, False);
	}
	if ($pagenum < $maxPage-1)
	{
		printPageButton($pagenum+2, False);
	}
	if ($pagenum < $maxPage-3)
	{
		print "...";
	}
	if ($pagenum < $maxPage-2)
	{
		printPageButton($maxPage, False);
	}
	if ($maxPage > 1 && $sortBar)
	{
		print '<span style="float:right;">
			<FORM class="advancedSearch" method="get" action="profile.php#results">
			<b>Sort By:</b>
			<select  class = "advancedSearch" name="sortCol">
				<option'.(($sortCol=='id') ? ' selected="selected"' : '').' value="id">Game ID</option>
				<option'.(($sortCol=='name') ? ' selected="selected"' : '').' value="name">Game Name</option>
				<option'.(($sortCol=='pot') ? ' selected="selected"' : '').' value="pot">Pot Size</option>
				<option'.(($sortCol=='minimumBet') ? ' selected="selected"' : '').' value="minimumBet">Bet</option>
				<option'.(($sortCol=='phaseMinutes') ? ' selected="selected"' : '').' value="phaseMinutes">Phase Length</option>
				<option'.(($sortCol=='minimumReliabilityRating') ? ' selected="selected"' : '').' value="minimumReliabilityRating">Reliability Rating</option>
				<option'.(($sortCol=='watchedGames') ? ' selected="selected"' : '').' value="watchedGames">Spectator Count</option>
				<option'.(($sortCol=='turn') ? ' selected="selected"' : '').' value="turn">Game Turn</option>
				<option'.(($sortCol=='processTime') ? ' selected="selected"' : '').' value="processTime">Time to Next Phase</option>
			</select>
			<select class = "advancedSearch" name="sortType">
				<option'.(($sortType=='asc') ? ' selected="selected"' : '').' value="asc">Ascending</option>
				<option'.(($sortType=='desc') ? ' selected="selected"' : '').' value="desc">Descending</option>
			</select>';
			foreach($_REQUEST as $key => $value)
			{
				if ($key == 'searchUser')
				{
					foreach ($value as $curKey => $curVal)
					{
						print '<input type="hidden" name="searchUser['.$curKey.']" value="'.$curVal.'">';
					}
				}
				elseif(strpos('x'.$key,'wD') == false && strpos('x'.$key,'phpbb3') == false && strpos('x'.$key,'__utm')== false && $key!="pagenum" && $key!="sortCol" && $key!="sortType")
				{
					print '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
			}
			print ' ';
			print '<input type="submit" class="form-submit" name="Submit" value="Refresh" /></form>
			</span>';
		}
}

function printPageButton($pagenum, $currPage)
{
	if ($currPage)
	{
		print '<div class="curr-page">'.$pagenum.'</div>';
	}
	else
	{
		print '<div style="display:inline-block; margin:3px;">';
		print '<FORM method="get" action=profile.php#results>';
		foreach($_REQUEST as $key => $value)
		{
			if ($key == 'searchUser')
			{
				foreach ($value as $curKey => $curVal)
				{
					print '<input type="hidden" name="searchUser['.$curKey.']" value="'.$curVal.'">';
				}
			}
			elseif(strpos('x'.$key,'wD') == false && strpos('x'.$key,'phpbb3')== false && strpos('x'.$key,'__utm')== false && $key!="pagenum")
			{
				print '<input type="hidden" name="'.$key.'" value="'.$value.'">';
			}
		}
		print '<input type="submit" name="pagenum" class="form-submit" value='.$pagenum.' /></form></div>';
	}
}

libHTML::footer();
?>
