<?php

namespace Diplomacy\Controllers;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Diplomacy\Models\WatchedGame;
use Diplomacy\Services\Games\GamesService;
use Diplomacy\Services\Games\MembersService;
use Diplomacy\Tournaments\Service as TournamentsService;
use Diplomacy\Services\Request;
use libHome;

class DashboardController extends BaseController
{
    /** @var string */
    protected $template = 'pages/home/index.twig';
    /** @var TournamentsService */
    protected $tournamentsService;
    /** @var MembersService */
    protected $membersService;
    /** @var GamesService */
    protected $gamesService;

    protected function setUp()
    {
        $this->tournamentsService = new TournamentsService();
        $this->membersService = new MembersService();
        $this->gamesService = new GamesService();
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
            'my_games' => $this->getMyGames(),
            'my_defeats' => $this->getMyDefeats(),
            'my_watched' => $this->getMyWatched(),
        ];

        $result = $this->tournamentsService->findParticipatingForUser($this->currentUser->id);
        if ($result->any()) {
            $variables['my_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'My Tournaments',
                'tournaments' => $result,
            ]);
        }

        $result = $this->tournamentsService->findSpectatingForUser($this->currentUser->id);
        if ($result->any()) {
            $variables['spectating_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'Spectated Tournaments',
                'tournaments' => $result,
            ]);
        }

        return $variables;
    }

    /**
     * @return Collection
     */
    protected function getMyGames() : Collection
    {
        return $this->gamesService->getActiveForUser($this->currentUser->id);
    }

    /**
     * @return Collection
     */
    protected function getMyDefeats() : Collection
    {
        return $this->gamesService->getDefeatsForUser($this->currentUser->id);
    }

    /**
     * @return Collection
     */
    public function getMyWatched() : Collection
    {
        return $this->gamesService->getWatchedForUser($this->currentUser->id);
    }

    /**
     * Update the user's session with the last time they were home
     */
    private function updateLastSeenHome() : void
    {
        if (!isset($_SESSION['lastSeenHome']) || $_SESSION['lastSeenHome'] < $this->currentUser->timeLastSessionEnded)
        {
            $_SESSION['lastSeenHome'] = $this->currentUser->timeLastSessionEnded;
        }
    }

    /**
     * Check to see if the "disable/enable notices" link was clicked for a game, and if so, toggle it
     */
    private function handleDisableNotices() : void
    {
        $gameToggleId = (int)$this->request->get('gameToggleName', 0, Request::TYPE_POST);
        if (!$this->currentUser->isAuthenticated() || empty($gameToggleId)) return;

        $member = $this->membersService->findForGame($this->currentUser->id, $gameToggleId);

        $member->hideNotifications = $member->hideNotifications == 1 ? 0 : 1;
        $member->save();
        $this->redirectRelative('/');
    }
}
