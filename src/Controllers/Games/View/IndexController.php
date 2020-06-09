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
use Diplomacy\Views\Components\Games\MapComponent;

class IndexController extends Base
{
    protected $template = 'pages/games/view/index.twig';
    protected $renderPageTitle = false;

    /** @var GamesService $gamesService */
    protected $gamesService;
    /** @var Game $game */
    protected $game;

    protected $footerScripts = ['makeFormsSafe();'];


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

        $currentMember = $gameEntity->members->byUserId($this->currentUser->id);
        return [
            'current_member' => $currentMember,
            'game' => $gameEntity,
            'board' => $gameBoard,
            'forum' => $this->getForum($gameEntity, $currentMember, $this->currentUser),
            'orders' => $this->getOrders($gameEntity, $currentMember, $this->currentUser),
            'map' => (string)(new MapComponent($gameEntity, $this->currentUser)),
        ];
    }

    protected function loadGame()
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        $this->game = $this->gamesService->find($gameId);
    }

    protected function getForum(GameEntity $game, Member $member = null, \User $legacyUser = null)
    {
        $targetCountryId = $this->request->get('msgCountryID', -1, Request::TYPE_REQUEST);
        return (string)(new \Diplomacy\Views\Components\Games\ChatBox\ChatBoxComponent($game, $member, $targetCountryId));
    }

    protected function getOrders(GameEntity $game, Member $member = null, \User $legacyUser = null)
    {
        if (!$game->phase->isActive()) return '';

        // TODO: Will require redoing _all_ of the orderinterface class
        return '';
    }
}