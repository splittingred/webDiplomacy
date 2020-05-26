<?php

namespace Diplomacy\Services\Contact;

use Diplomacy\Models\AdminLog;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;
use Mailer;
use User;

class SubmissionService
{
    /** @var Mailer */
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new Mailer();
    }

    /**
     * @param User $user
     * @param Issue $issue
     */
    public function handle(User $user, Issue $issue)
    {
        if ($issue instanceof EmergencyIssue) {
            return $this->handleEmergency($user, $issue);
        } else {
            return $this->handleRegular($user, $issue);
        }
    }

    /**
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    protected function handleRegular(User $user, Issue $issue) : bool
    {
        $email = $user->email;
        $body = "<p>This request is from <a href=\"".\Config::$url."/profile.php?userID=".$user->id."\">".$user->username."</a>, 
            and their registered email is: ".$user->email."</p>";

        $games = $issue->getGames();
        if (!empty($games)) $body .= $this->getGameHtml($issue, $games);

        $body .= "<p>The ".$issue->getActualProblem().".</p>";

        $additionalInfo = $issue->additionalInfo;
        $body .="<p><strong>Additional Information:</strong>:<br />$additionalInfo</p>";
        return $this->send($email, $issue->subject.' - '.$user->username, $body);
    }

    /**
     * @param User $user
     * @param EmergencyIssue $issue
     * @return bool
     */
    protected function handleEmergency(User $user, EmergencyIssue $issue) : bool
    {
        $email = $user->email;
        $allGameIds = [];

        foreach ($issue->getGames() as $game) {
            $allGameIds[] = $game->id;

            // This is a reduced version of the toggle Pause function, altered so that it can only impact non-paused games.
            if ($game->isPaused()) continue;

            if (!$game->isPreGame() && !$game->isFinished() && $game->isNotProcessing()) {
                $game->processStatus = 'Paused';
                $game->pauseTimeRemaining = $game->processTime - time();
                $game->processTime = null;
                $game->save();

                // Any votes to toggle the pause are now void
                Member::forGame($game->id)->where('votes', 'LIKE', '%Pause%')->update(['votes' => '']);
            }
        }

        // Update the users emergency pause time to now.
        $user->updateEmergencyPauseDate(time());

        $adminLog = new AdminLog();
        $adminLog->name = 'Emergency Pause';
        $adminLog->userID = $user->id;
        $adminLog->time = time();
        $adminLog->details = 'The following games were paused due to a player emergency.';
        $adminLog->params = $allGameIds;
        $adminLog->save();

        $body = '<p>This request is from <a href="'. \Config::$url . '/profile.php?userID='. $user->id .'>' . $user->username . '</a>, and their registered email is: ' . $user->email . '</p>';
        $body .= '<p><strong>An emergency pause was used because of ' . $issue->getActualProblem(). '</strong></p>';

        $body .= '<p>All the games impacted will need a moderator to post in the global chat explaining why the game was paused. Simply say \'A user in this game needed an emergency pause\', do <strong>not</strong> give the reason.</p>';

        $games = $issue->getGames();
        if (!empty($games)) {
            $body .= '<p>The games that were paused as a result of this are:</p>';
            $body .= $this->getGameHtml($issue, $games);
        }

        $body .= '<p>Please note that any games they are in that were already paused will NOT show up in the above list. Please check all this users games to make sure none are 
about to be unpaused and then follow up with the user to see how long they need this pause for and determine if the reason was acceptable.</p>';
        $body .= '<p><strong>Additional Information:</strong>:<br />$additionalInfo</p>';

        return $this->send($email, $issue->subject.' - '.$user->username, $body);
    }

    /**
     * @param Issue $issue
     * @param array $games
     * @return string
     */
    protected function getGameHtml(Issue $issue, $games = []) : string
    {
        $body = '';
        if ($issue->isForNoSpecificGame()) {
            $body .= "<p><strong>The user called out no specific game.</strong></p>";
        } elseif ($issue->isForAllGames()) {
            $body .= "<p><strong>The user called out all their games.</strong></p>";
        } else {
            $body .= '<p><strong>The user called out:</strong></p><ul>';
            foreach ($games as $game) {
                $body .= '<li><a href="' . \Config::$url . '/board.php?gameID=' . $game->id . '">' . $game->name . '</a></li>';
            }
            $body .= '</ul>';
        }
        return $body;
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $body
     * @return bool
     */
    protected function send(string $email, string $subject, string $body) : bool
    {
        try {
            $this->mailer->Send([$email => $email], $subject, $body);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}