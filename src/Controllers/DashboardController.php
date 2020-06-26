<?php

namespace Diplomacy\Controllers;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Diplomacy\Models\WatchedGame;
use Diplomacy\Services\Games\GamesService;
use Diplomacy\Services\Games\MembersService;
use Diplomacy\Services\Tournaments\Service as TournamentsService;
use Diplomacy\Services\Request;
use Diplomacy\Views\Components\Games\HomePanelComponent;
use libHome;

class DashboardController extends BaseController
{
    /** @var string */
    protected string $template = 'pages/home/index.twig';
    protected TournamentsService $tournamentsService;
    protected MembersService $membersService;
    protected GamesService $gamesService;

    protected function setUp(): void
    {
        $this->tournamentsService = new TournamentsService();
        $this->membersService = new MembersService();
        $this->gamesService = new GamesService();
        \libHTML::$footerIncludes[] = l_j('home.js');
        \libHTML::$footerScript[] = l_jf('homeGameHighlighter').'();';
    }

    public function call(): array
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

        $result = $this->tournamentsService->findParticipatingForUser($this->currentUserEntity->id);
        if ($result->any()) {
            $variables['my_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'My Tournaments',
                'tournaments' => $result,
            ]);
        }

        $result = $this->tournamentsService->findSpectatingForUser($this->currentUserEntity->id);
        if ($result->any()) {
            $variables['spectating_tournaments'] = $this->renderPartial('pages/home/tournaments.twig', [
                'title' => 'Spectated Tournaments',
                'tournaments' => $result,
            ]);
        }

        return $variables;
    }

    /**
     * @return array|mixed
     */
    protected function getMyGames(): array
    {
        $collection = $this->gamesService->getActiveForUser($this->currentUserEntity->id);
        $output = [];
        /** @var \Diplomacy\Models\Entities\Game $game */
        foreach ($collection as $game) {
            $output[] = [
                'game' => $game,
                'currentMember' => $game->members->byUser($this->currentUserEntity),
            ];
        }
        return $output;
    }

    /**
     * @return Collection
     */
    protected function getMyDefeats(): array
    {
        $collection = $this->gamesService->getDefeatsForUser($this->currentUserEntity->id);
        $output = [];
        /** @var \Diplomacy\Models\Entities\Game $game */
        foreach ($collection as $game) {
            $output[] = [
                'game' => $game,
                'currentMember' => $game->members->byUser($this->currentUserEntity),
            ];
        }
        return $output;
    }

    /**
     * @return array
     */
    public function getMyWatched(): array
    {
        $collection = $this->gamesService->getWatchedForUser($this->currentUserEntity->id);
        $output = [];
        /** @var \Diplomacy\Models\Entities\Game $game */
        foreach ($collection as $game) {
            $output[] = [
                'game' => $game,
                'currentMember' => $game->members->byUser($this->currentUserEntity),
            ];
        }
        return $output;
    }

    /**
     * Update the user's session with the last time they were home
     */
    private function updateLastSeenHome() : void
    {
        if (!isset($_SESSION['lastSeenHome']) || $_SESSION['lastSeenHome'] < $this->currentUserEntity->timeLastSessionEnded)
        {
            $_SESSION['lastSeenHome'] = $this->currentUserEntity->timeLastSessionEnded;
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
