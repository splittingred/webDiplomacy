<?php

namespace Diplomacy\Controllers\Tournaments;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Request;
use Diplomacy\Tournaments\Service as TournamentService;

class IndexController extends BaseController
{
    /** @var string */
    protected $template = 'pages/tournaments/index.twig';
    /** @var TournamentService */
    protected $tournamentService;

    public function setUp()
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