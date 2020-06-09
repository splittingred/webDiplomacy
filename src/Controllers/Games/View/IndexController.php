<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Models\Entities\Games\Member;
use \Diplomacy\Models\Entities\Game as GameEntity;
use Diplomacy\Models\Game;
use Diplomacy\Services\Games\Factory;
use Diplomacy\Services\Games\GamesService;
use Diplomacy\Services\Request;
use \Diplomacy\Models\Entities\Games\GameBoard;
use Diplomacy\Views\Components\Games\ChatBox\ChatBoxComponent;
use Diplomacy\Views\Components\Games\MapComponent;

class IndexController extends Base
{
    protected $template = 'pages/games/view/index.twig';
    protected $renderPageTitle = false;

    /** @var GamesService $gamesService */
    protected $gamesService;
    /** @var Factory $gameFactory */
    protected $gameFactory;
    /** @var Game $game */
    protected $game;
    /** @var GameEntity */
    protected $gameEntity;
    /** @var Member $currentMember */
    protected $currentMember;

    protected $footerScripts = ['makeFormsSafe();'];


    public function setUp()
    {
        parent::setUp();
        $this->gameFactory = new Factory();
        $this->gamesService = new GamesService();
    }

    public function beforeRender(): void
    {
        $this->loadGame();
        $this->pageTitle = $this->game->name;
    }

    public function call()
    {
        $gameBoard = new GameBoard($this->game, $this->gameEntity, $this->currentUser);

        return [
            'current_member' => $this->currentMember,
            'game' => $this->gameEntity,
            'board' => $gameBoard,
            'forum' => $this->getForum(),
            'orders' => $this->getOrders(),
            'map' => (string)(new MapComponent($this->gameEntity, $this->currentUser)),
        ];
    }

    protected function loadGame()
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        if (empty($gameId)) $this->redirectRelative('/', true);

        $this->game = $this->gamesService->find($gameId);
        if (empty($this->game)) $this->redirectRelative('/', true);

        $this->gameEntity = $this->gameFactory->build($this->game->id);
        $this->currentMember = $this->gameEntity->members->byUserId($this->currentUser->id);
    }

    protected function getForum()
    {
        $targetCountryId = $this->request->get('countryId', -1, Request::TYPE_REQUEST);
        return (string)(new ChatBoxComponent($this->gameEntity, $this->currentMember, $targetCountryId));
    }

    protected function getOrders()
    {
        if (!$this->gameEntity->phase->isActive()) return '';

        // TODO: Will require redoing _all_ of the orderinterface class
        return '';
    }
}