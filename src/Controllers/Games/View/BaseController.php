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
    /** @var \userMember */
    protected $member;

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
        $this->loadCountries($this->variant);

        if ($this->user && $this->user->isAuthenticated()) {
            $this->member = $this->game->Members->ByUserID[$this->user->id];
            $this->setPlaceholder('member', $this->member);
        }
        return $this->game;
    }

    protected function loadCountries(\WDVariant $variant) : void
    {
        $idx = 1;
        $countries = [];
        foreach ($variant->countries as $countryName) {
            $countries[] = ['id' => $idx, 'name' => $countryName];
            $idx++;
        }
        $this->setPlaceholder('countries', $countries);
    }
}