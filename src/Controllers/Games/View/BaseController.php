<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\BaseController as Base;

abstract class BaseController extends Base
{
    protected $template = 'pages/games/view.twig';

    /** @var \WDVariant */
    protected $variant;
    /** @var \panelGameBoard */
    protected $game;

    public function beforeRender(): void
    {
        $this->loadGame();
    }

    public function call()
    {
        return [];
    }

    /**
     * @return \panelGameBoard
     */
    protected function loadGame() : \panelGameBoard
    {
        $gameId = $this->request->get('id');
        require_once 'objects/game.php';
        require_once 'board/chatbox.php';
        require_once 'gamepanel/gameboard.php';
        $this->variant = \libVariant::loadFromGameID($gameId);
        \libVariant::setGlobals($this->variant);
        $this->game = $this->variant->panelGameBoard($gameId);
        $this->setPlaceholder('game', $this->game);
        return $this->game;
    }
}