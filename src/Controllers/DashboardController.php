<?php

namespace Diplomacy\Controllers;

use Diplomacy\Tournaments\Service as TournamentsService;
use Diplomacy\Services\Request;
use libHome;

class DashboardController extends BaseController
{
    /** @var string */
    protected $template = 'pages/home/index.twig';
    /** @var TournamentsService */
    protected $tournamentsService;

    protected function setUp()
    {
        $this->tournamentsService = new TournamentsService($this->database);
        \libHTML::$footerIncludes[] = l_j('home.js');
        \libHTML::$footerScript[] = l_jf('homeGameHighlighter').'();';
    }

    public function call()
    {
        if ($this->request->exists('submit', Request::TYPE_POST)) {
            $this->handleDisableNotices();
        }
        $this->updateLastSeenHome();

        $variables = [
            'live_games' => $this->renderPartial('pages/home/live_games.twig',[
                'live_games' => libHome::upcomingLiveGames(),
            ]),
            'notices' => $this->renderPartial('pages/home/notices.twig',[
                'notices' => libHome::Notice(),
            ]),
            'game_notify_block' => libHome::gameNotifyBlock(),
            'game_defeated_notify_block' => libHome::gameDefeatedNotifyBlock(),
            'game_watch_block' => libHome::gameWatchBlock(),
        ];

        $result = $this->tournamentsService->findParticipatingForUser($this->user->id);
        if ($result->any()) {
            $variables['my_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'My Tournaments',
                'tournaments' => $result->getEntities(),
            ]);
        }

        $result = $this->tournamentsService->findSpectatingForUser($this->user->id);
        if ($result->any()) {
            $variables['spectating_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'Spectated Tournaments',
                'tournaments' => $result->getEntities(),
            ]);
        }

        return $variables;
    }

    private function updateLastSeenHome() : void
    {
        if (!isset($_SESSION['lastSeenHome']) || $_SESSION['lastSeenHome'] < $this->user->timeLastSessionEnded)
        {
            $_SESSION['lastSeenHome'] = $this->user->timeLastSessionEnded;
        }
    }

    private function handleDisableNotices() : void
    {
        $gameToggleID = isset($_POST['gameToggleName']) && intval($_POST['gameToggleName']);

        if (!$this->user->isAuthenticated() || $gameToggleID <= 0) return;

        list($noticesStatus) = $this->database->sql_row("SELECT hideNotifications FROM wD_Members WHERE userID = ".$this->user->id." AND gameID = ".$gameToggleID);

        if ($noticesStatus == 0)
        {
            $this->database->sql_put("UPDATE wD_Members SET hideNotifications = 1 WHERE userID = ".$this->user->id." and gameID = ".$gameToggleID);
        }
        else if ($noticesStatus == 1)
        {
            $this->database->sql_put("UPDATE wD_Members SET hideNotifications = 0 WHERE userID = ".$this->user->id." and gameID = ".$gameToggleID);
        }
    }
}
