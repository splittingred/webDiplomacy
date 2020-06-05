<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Models\Game;
use Diplomacy\Services\Games\Factory;
use Diplomacy\Services\Games\GamesService;
use Diplomacy\Services\Request;
use \Diplomacy\Models\Entities\Games\GameBoard;

class IndexController extends Base
{
    protected $template = 'pages/games/view/index.twig';
    protected $renderPageTitle = false;

    /** @var GamesService $gamesService */
    protected $gamesService;
    /** @var Game $game */
    protected $game;

    public function setUp()
    {
        parent::setUp();
        $this->gamesService = new GamesService();
    }

    public function beforeRender(): void
    {
        $this->loadGame();
        $this->pageTitle = $this->game->name;
    }

    public function call()
    {
        $gameFactory = new Factory();
        $gameEntity = $gameFactory->build($this->game->id);
        $gameBoard = new GameBoard($this->game, $gameEntity, $this->currentUser);
        return [
            'game' => $gameEntity,
            'board' => $gameBoard,
        ];
    }

    protected function loadGame()
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        $this->game = $this->gamesService->find($gameId);
    }
}