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
use Diplomacy\Views\Components\Games\OrdersComponent;

class IndexController extends Base
{
    protected string $template = 'pages/games/view/index.twig';
    protected bool $renderPageTitle = false;
    protected GamesService $gamesService;
    protected Factory $gameFactory;
    protected Game $game;
    protected GameEntity $gameEntity;
    protected ?Member $currentMember;

    public function setUp(): void
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

    public function call(): array
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

    protected function loadGame(): IndexController
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        if (empty($gameId)) $this->redirectRelative('/', true);

        $this->game = $this->gamesService->find($gameId);
        if (empty($this->game)) $this->redirectRelative('/', true);

        $this->gameEntity = $this->gameFactory->build($this->game->id);
        $this->currentMember = $this->gameEntity->members->byUser($this->currentUserEntity);
        return $this;
    }

    /**
     * @return string
     */
    protected function getForum(): string
    {
        $targetCountryId = $this->request->get('countryId', -1, Request::TYPE_REQUEST);
        return (string)(new ChatBoxComponent($this->gameEntity, $this->currentMember, $targetCountryId));
    }

    /**
     * @return string
     */
    protected function getOrders(): string
    {
        if (!$this->gameEntity->phase->isActive()) return '';

        return (string)(new OrdersComponent($this->gameEntity, $this->currentMember));
    }
}