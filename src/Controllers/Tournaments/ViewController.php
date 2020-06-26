<?php

namespace Diplomacy\Controllers\Tournaments;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Tournament;
use Diplomacy\Services\Request;
use Diplomacy\Services\Tournaments\Service as TournamentService;

class ViewController extends BaseController
{
    protected string $template = 'pages/tournaments/view.twig';
    protected string $pageTitle = 'webDiplomacy Tournaments';
    protected string $pageDescription = 'Information on Tournaments and Feature Game rules and setup.';

    /** @var TournamentService */
    protected $tournamentService;
    /** @var Tournament */
    protected $tournament;

    public function setUp(): void
    {
        $this->tournamentService = new TournamentService();
    }

    public function beforeRender(): void
    {
        $this->loadTournament();
    }

    public function call(): array
    {
        $this->handleSubmit();
        return [
            'tournament' => $this->tournament,
            'participants' => $this->tournament->scoresTabulated(),
            'editor' => $this->tournament->isEditor($this->currentUser),
        ];
    }

    /**
     * Handle form update
     */
    public function handleSubmit()
    {
        if ($this->request->isEmpty('submit', Request::TYPE_POST)) return;
        if ($this->tournament->isEditor($this->currentUser)) return; // dont allow non-editors to edit

        $scores = $this->request->get('user', [], Request::TYPE_POST);
        if (empty($scores)) return;

        foreach ($scores as $userId => $rounds)
        {
            $userId = intval(ltrim($userId, 'u'));
            if (empty($userId)) continue;

            foreach ($rounds as $round => $score) {
                $round = intval(ltrim($round, 'r'));
                if (empty($round)) continue;

                $this->tournamentService->updateScore($this->tournament->id, $userId, $round, (int)$score);
            }
        }
        $this->redirectRelative('tournaments/' . $this->tournament->id);
    }

    /**
     * @return Tournament
     */
    protected function loadTournament() : Tournament
    {
        $id = (int)$this->request->get('id');
        $this->tournament = Tournament::find($id);
        if (empty($this->tournament)) {
            $this->redirectRelative('/');
        }
        return $this->tournament;
    }
}