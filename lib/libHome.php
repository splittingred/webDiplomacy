<?php

class libHome
{
    static public function getType($type=false, $limit=35)
    {
        global $app;
        $User = $app->make('user');
        $DB = $app->make('DB');

        $notices=array();

        $tabl=$DB->sql_tabl("SELECT n.*
			FROM wD_Notices n
			LEFT JOIN wD_Games g on g.name = n.linkName and n.type = 'Game'
			LEFT JOIN wD_Members m on m.gameID = g.id and n.type = 'Game' and m.userID = ".$User->id."
			WHERE (m.hideNotifications is null or m.hideNotifications = 0) and n.toUserID=".$User->id.($type ? " AND n.type='".$type."'" : '')."
			ORDER BY n.timeSent DESC ".($limit?'LIMIT '.$limit:''));
        while($hash=$DB->tabl_hash($tabl))
        {
            $notices[] = new notice($hash);
        }

        return $notices;
    }
    public static function Game()
    {
        $pms = self::getType('Game');

        if(!count($pms))
        {
            print '<div class="hr"></div>';
            print '<p class="notice">'.l_t('No game notices found.').'</p>';
            return;
        }

        print '<div class="hr"></div>';

        foreach($pms as $pm)
        {
            print $pm->viewedSplitter();

            print $pm->html();
        }
    }
    public static function NoticePMs()
    {
        $output = '';
        try
        {
            $message=notice::sendPMs();
        }
        catch(Exception $e)
        {
            $message=$e->getMessage();
        }

        if ( $message )
            $output .= '<p class="notice">'.$message.'</p>';

        $pms = self::getType('PM',50);

        if(!count($pms))
        {
            $output .= '<div class="hr"></div>';
            $output .= '<p class="notice">'.l_t('No private messages found; you can send them to other people on their profile page.').'</p>';
            return $output;
        }

        $output .= '<div class="hr"></div>';

        foreach($pms as $pm)
        {
            $output .= $pm->viewedSplitter();

            $output .= $pm->html();
        }
        return $output;
    }

    public static function NoticeGame()
    {
        $pms = self::getType('Game');
        $output = '';

        if(!count($pms))
        {
            $output .= '<div class="hr"></div>';
            $output .= '<p class="notice">'.l_t('No game notices found; try browsing the <a href="/games/search">game listings</a>, '.
                    'or <a href="gamecreate.php">create your own</a> game.').'</p>';
            return $output;
        }

        $output .= '<div class="hr"></div>';

        foreach($pms as $pm)
        {
            $output .= $pm->viewedSplitter();

            $output .= $pm->html();
        }
        return $output;
    }

    public static function Notice()
    {
        $output = '';

        $pms = self::getType();

        if(!count($pms))
        {
            $output .= '<div class="hr"></div>';
            $output .= '<p class="notice">'.l_t('No notices found.').'</p>';
            return $output;
        }

        $output .= '<div class="hr"></div>';

        /** @var \notice $pm */
        foreach($pms as $pm)
        {
            $output .= $pm->html();
        }
        return $output;
    }

    public static function topUsers()
    {
        global $app;
        $DB = $app->make('DB');
        $rows = [];
        $tabl = $DB->sql_tabl("SELECT id, username, points FROM wD_Users
						order BY points DESC LIMIT 10");
        $i=1;
        while(list($userID,$username,$points)=$DB->tabl_row($tabl))
        {
            $rows[] = '#'.$i.': <a href="profile.php?userID='.$userID.'">'.$username.'</a> ('.$points.libHTML::points().')';
            $i++;
        }
        return $rows;
    }
    static function statsGlobalGame()
    {
        global $app;
        $Misc = $app->make('Misc');
        $stats=array(
            'Starting'=>$Misc->GamesNew,
            'Joinable'=>$Misc->GamesOpen,
            'Active'=>$Misc->GamesActive,
            'Finished'=>$Misc->GamesFinished
        );

        return $stats;
    }
    static function statsGlobalUser()
    {
        global $app;
        $Misc = $app->make('Misc');
        $stats=array(
            'Logged on'=>$Misc->OnlinePlayers,
            'Playing'=>$Misc->ActivePlayers,
            'Registered'=>$Misc->TotalPlayers
        );

        if( $stats['Logged on'] <= 1 ) unset($stats['Logged on']);
        if( $stats['Playing'] < 25 ) unset($stats['Playing']);

        return $stats;
    }
    static function globalInfo()
    {
        $userStats = self::statsGlobalUser();
        $gameStats = self::statsGlobalGame();

        $buf='<strong>'.l_t('Users:').'</strong> ';
        $first=true;
        foreach($userStats as $name => $val)
        {
            if( $first ) $first=false; else $buf .= ' - ';
            $buf .= l_t($name).':<strong>'.$val.'</strong>';
        }

        $buf .= '<br /><strong>'.l_t('Games:').'</strong> ';
        $first=true;
        foreach($gameStats as $name => $val)
        {
            if( $first ) $first=false; else $buf .= ' - ';
            $buf .= l_t($name).':<strong>'.$val.'</strong>';
        }

        return $buf;
    }

    static public function upcomingLiveGames ()
    {
        global $app;
        $User = $app->make('user');
        $DB = $app->make('DB');

        if ($User->options->value['displayUpcomingLive'] == 'No') return '';

        $tabl=$DB->sql_tabl("SELECT g.* FROM wD_Games g
			WHERE (
			        ( g.phase = 'Pre-game' ) 
                OR (
			        g.phase in ('Diplomacy','Retreats','Builds') 
			        AND g.minimumBet IS NOT NULL 
			        AND g.gameOver = 'No')
                ) 
			AND g.phaseMinutes < 60 
			AND g.password IS NULL
			ORDER BY g.processStatus ASC, g.processTime ASC LIMIT 3");
        $buf = '';
        $count=0;
        while($game=$DB->tabl_hash($tabl))
        {
            $count++;
            $Variant=libVariant::loadFromVariantID($game['variantID']);
            $Game=$Variant->panelGameHome($game);
            $buf .= '<div class="hr"></div>';
            $buf .= $Game->summary();
        }
        return $buf;
    }

    static function forumNew()
    {
        // Select by id, prints replies and new threads
        global $app;
        $DB = $app->make('DB');

        $tabl = $DB->sql_tabl("
			SELECT m.id as postID, t.id as threadID, m.type, m.timeSent, IF(t.replies IS NULL,m.replies,t.replies) as replies,
				IF(t.subject IS NULL,m.subject,t.subject) as subject,
				u.id as userID, u.username, u.points, IF(s.userID IS NULL,0,0) as online, u.type as userType,
				SUBSTRING(m.message,1,100) as message, m.latestReplySent, t.fromUserID as threadStarterUserID
			FROM wD_ForumMessages m
			INNER JOIN wD_Users u ON ( m.fromUserID = u.id )
			LEFT JOIN wD_Sessions s ON ( m.fromUserID = s.userID )
			LEFT JOIN wD_ForumMessages t ON ( m.toID = t.id AND t.type = 'ThreadStart' AND m.type = 'ThreadReply' )
			ORDER BY m.timeSent DESC
			LIMIT 50");
        $oldThreads=0;
        $threadCount=0;

        $threadIDs = array();
        $threads = array();

        while(list($postID, $threadID, $type, $timeSent, $replies, $subject, $userID, $username, $points, $online, $userType, $message, $latestReplySent,$threadStarterUserID
            ) = $DB->tabl_row($tabl))
        {
            $threadCount++;

            if( $threadID )
                $iconMessage=libHTML::forumMessage($threadID, $postID);
            else
                $iconMessage=libHTML::forumMessage($postID, $postID);

            if ( $type == 'ThreadStart' ) $threadID = $postID;

            if( !isset($threads[$threadID]) )
            {
                if(strlen($subject)>30) $subject = substr($subject,0,40).'...';
                $threadIDs[] = $threadID;
                $threads[$threadID] = array('subject'=>$subject, 'replies'=>$replies, 'posts'=>array(),'threadStarterUserID'=>$threadStarterUserID);
            }

            $message=Message::refilterHTML($message);

            if( strlen($message) >= 50 ) $message = substr($message,0,50).'...';

            $message = '<div class="message-contents threadID'.$threadID.'" fromUserID="'.$userID.'">'.$message.'</div>';

            $threads[$threadID]['posts'][] = array(
                'iconMessage'=>$iconMessage,'userID'=>$userID, 'username'=>$username,
                'message'=>$message,'points'=>$points, 'online'=>$online, 'userType'=>$userType, 'timeSent'=>$timeSent
            );
        }

        $buf = '';
        $threadCount=0;
        foreach($threadIDs as $threadID)
        {
            $data = $threads[$threadID];

            $buf .= '<div class="hr userID'.$threads[$threadID]['threadStarterUserID'].' threadID'.$threadID.'"></div>';

            $buf .= '<div class="homeForumGroupNew homeForumAlt'.($threadCount%2 + 1).
                ' userID'.$threads[$threadID]['threadStarterUserID'].' threadID'.$threadID.'">
				<div class="homeForumSubject homeForumTopBorder">'.libHTML::forumParticipated($threadID).' '.$data['subject'].'</div> ';

            if( count($data['posts']) < $data['replies'])
            {
                $buf .= '<div class="homeForumPost homeForumMessage homeForumPostAlt'.libHTML::alternate().' ">

				...</div>';
            }

            $data['posts'] = array_reverse($data['posts']);
            foreach($data['posts'] as $post)
            {
                $buf .= '<div class="homeForumPost homeForumPostAlt'.libHTML::alternate().' userID'.$post['userID'].'">

					<div class="homeForumPostTime">'.libTime::text($post['timeSent']).' '.$post['iconMessage'].'</div>
					<a href="profile.php?userID='.$post['userID'].'" class="light">'.$post['username'].'</a>
						'.' ('.$post['points'].libHTML::points().
                    User::typeIcon($post['userType']).')

					<div style="clear:both"></div>
					<div class="homeForumMessage">'.$post['message'].'</div>
					</div>';
            }

            $buf .= '<div class="homeForumLink">
					<div class="homeForumReplies">'.l_t('%s replies','<strong>'.$data['replies'].'</strong>').'</div>
					<a href="forum.php?threadID='.$threadID.'#'.$threadID.'">'.l_t('Open').'</a>
					</div>
					</div>';
        }

        if( $buf )
        {
            return $buf;
        }
        else
        {
            return '<div class="homeNoActivity">'.l_t('No forum posts found, why not '.
                    '<a href="forum.php?postboxopen=1#postbox" class="light">start one</a>?');
        }
    }

    static function forumBlock()
    {
        $buf = '<div class="homeHeader">'.l_t('Forum').'</div>';

        $forumNew=libHome::forumNew();
        $buf .=  '<table><tr><td>'.implode('</td></tr><tr><td>',$forumNew).'</td></tr></table>';
        return $buf;
    }

    static function forumBlockExtern()
    {
        $buf = '<div class="homeHeader">'.l_t('Forum').'</div>';

        $forumNew=libHome::forumNewExtern();
        $buf .=  '<table><tr><td>'.implode('</td></tr><tr><td>',$forumNew).'</td></tr></table>';
        return $buf;
    }

    static function forumNewExtern()
    {
        // Select by id, prints replies and new threads
        global $app;
        $DB = $app->make('DB');

        $tabl = $DB->sql_tabl("SELECT t.forum_id, f.forum_name, t.topic_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved,
				u1.webdip_user_id as topic_poster_webdip, t.topic_poster, t.topic_first_poster_name, t.topic_first_poster_colour,
				t.topic_last_post_id, t.topic_last_post_time,
				u2.webdip_user_id as topic_last_poster_webdip, t.topic_last_poster_id, t.topic_last_poster_name, t.topic_last_poster_colour,
				p.post_id, p.post_text
				FROM phpbb_topics t
				INNER JOIN phpbb_posts p ON p.post_id = t.topic_last_post_id
				INNER JOIN phpbb_forums f ON f.forum_id = t.forum_id
				INNER JOIN phpbb_users u1 ON u1.user_id = t.topic_poster
				INNER JOIN phpbb_users u2 ON u2.user_id = t.topic_last_poster_id
				WHERE t.topic_visibility = 1 and f.forum_name <> 'Politics' and t.topic_title not like '%HIDDEN%'
				ORDER BY t.topic_last_post_time DESC
				LIMIT 20");

        $buf = '';
        while($t = $DB->tabl_hash($tabl))
        {
            $buf .= '<div class="hr"></div>';

            $urlForum = '/contrib/phpBB3/viewforum.php?f='.$t['forum_id'];
            $urlThread = '/contrib/phpBB3/viewtopic.php?f='.$t['forum_id'].'&t='.$t['topic_id'];
            $urlPost = '/contrib/phpBB3/viewtopic.php?f='.$t['forum_id'].'&p='.$t['post_id'].'#p'.$t['post_id'];

            //topic_poster_webdip // ID
            //topic_last_poster_webdip // ID

            $alt = libHTML::alternate();
            $buf .= '<div class="homeForumGroupNew homeForumAlt'.$alt.'">';

            $buf .= '
				<div class="homeForumSubject" >';
            //$buf .= '<div style="float:right"><img src="http://127.0.0.1/images/historyicons/external.png" width="10" height="10"></div>';

            $buf .= '<span style=\'font-size:90%\'>'.($t['topic_posts_approved']>1?'Re: ':'New: ').'</span>'
                .'<a href="'.$urlThread.'" style=\'font-size:110%\'>'.$t['topic_title'].'</a>';
            $buf .= '<div style="clear:both"></div>';
            $buf .= '<div class="homeForumPostTime" style="float:right"><em>'.libTime::text($t['topic_time']).'</em></div>';
            $buf .= '<span style=\'font-size:90%\'>';
            $buf .= 'Thread:</span> <a href="profile.php?userID='.$t['topic_poster_webdip'].'" class="light">'.$t['topic_first_poster_name'].'</a> ';
            $buf .= '<div style="clear:both"></div></div>';
            $buf .= '<div class="homeForumPost homeForumPostAlt'.$alt.'">';


            if( $t['topic_posts_approved']>1 )
            {

                $buf .= '<div class="" style="margin-bottom:5px;margin-left:3px; margin-right:3px;">';
                $buf .= '<div class="homeForumPostTime" style="float:right;font-weight:bold"><em>'.libTime::text($t['topic_last_post_time']).'</em></div>';
                $buf .= '<span class="home-forum-latest">';
                $buf .= 'Latest:</span> <a href="profile.php?userID='.$t['topic_last_poster_webdip'].'" class="light">'.$t['topic_last_poster_name'].'</a> '
                    .'</div>';
            }

            $post = $DB->msg_escape(preg_replace("/\[[^\]]+\]/","",preg_replace("/<[^>]+>/","",$t['post_text'])));
            $post = str_replace("\\'","'", $post);
            $post = substr($post, 0, 75);
            if(strlen($post) > 45) $post .= '...';

            $buf .= '<div><span style="font-style:italic">&quot;'.$post.'&quot;</span>';

            $buf .= '<div style="float:right"><a href="'.$urlPost.'">Open</a></div>';
            $buf .= '<div style="clear:both"></div>';

            $buf .= '</div>';
            $buf .= '</div>';

            $buf .= '<div class="" style="margin-bottom:5px;margin-left:3px; margin-right:3px;">';

            $buf .= '<div style="margin-left:3px; margin-right:3px; font-size:90%">';
            $buf .= '<div style="float:right">';
            $buf .= l_t('<span class="forum-preview-span">%s replies, </span>','<strong>'.($t['topic_posts_approved']-1).'</strong>');
            $buf .= l_t('<span class="forum-preview-span">%s views</span>','<strong style=\'content: "\f14c"\'>'.($t['topic_views']-1).'</strong>');
            $buf .= '</div>';
            $buf .= '<span class="forum-preview-span">&raquo;
					<a href="'.$urlForum.'">'.$t['forum_name'].'</a></span>

					</div>';
            $buf .= '</div>';
            $buf .= '</div>';
        }

        if( $buf )
        {
            return $buf;
        }
        else
        {
            return '<div class="homeNoActivity">'.l_t('No forum posts found, why not start one?');
        }
    }
}
