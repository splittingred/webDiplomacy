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
        ];
    }

    protected function loadGame()
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        $this->game = $this->gamesService->find($gameId);
    }

    protected function getForum(GameEntity $game, Member $member = null, \User $legacyUser = null)
    {
        $chatBox = new \Chatbox($this->renderer);
        // Now that we have retrieved the latest messages we can update the time we last viewed the messages
        // Post messages we sent, and get the user we're speaking to
        $countryId = $chatBox->findTab($game, $member);
        $chatBox->postMessage($countryId, $game, $member);
        $this->database->sql_put('COMMIT');
        return $chatBox->output($countryId, $game, $member, $legacyUser);
    }
}