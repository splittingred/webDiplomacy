<?php

namespace Diplomacy\Controllers;

use Diplomacy\Tournaments\Service as TournamentsService;
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
    }

    public function call()
    {
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
}
