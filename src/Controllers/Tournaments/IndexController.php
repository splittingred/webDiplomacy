<?php

namespace Diplomacy\Controllers\Tournaments;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Request;
use Diplomacy\Services\Tournaments\Service as TournamentService;

class IndexController extends BaseController
{
    protected string $template = 'pages/tournaments/index.twig';
    protected TournamentService $tournamentService;

    public function setUp(): void
    {
        $this->tournamentService = new TournamentService();
    }

    public function call() : array
    {
        $id = $this->request->get('id');
        if ($id) {
            $this->redirectRelative('tournaments/' . $id);
            exit();
        }
        $result = $this->tournamentService->getActive();
        return [
            'tournaments' => $result->getEntities(),
            'total' => $result->getTotal(),
        ];
    }
}