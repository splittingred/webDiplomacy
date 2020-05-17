<?php

namespace Diplomacy\Controllers\Tournaments\Scoring;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Tournaments\Service as TournamentService;

class IndexController extends BaseController
{
    /** @var string */
    protected $template = 'pages/tournaments/scoring/index.twig';
    /** @var TournamentService */
    protected $tournamentService;

    public function setUp()
    {
        $this->tournamentService = new TournamentService($this->database);
    }

    public function call() : array
    {
        $result = $this->tournamentService->getActive();
        return [
            'tournaments' => $result->getEntities(),
            'total' => $result->getTotal(),
        ];
    }
}