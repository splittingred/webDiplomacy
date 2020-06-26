<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Services\Request;

abstract class BaseController extends Base
{
    protected string $template = 'pages/games/view.twig';
    protected \WDVariant $variant;
    protected \panelGameBoard $game;
    protected \userMember $member;

    public function beforeRender() : void
    {
        $this->loadGame();
    }

    public function call(): array
    {
        return [];
    }

    /**
     * @return \panelGameBoard
     */
    protected function loadGame() : \panelGameBoard
    {
        $gameId = $this->request->get('id', 0, Request::TYPE_REQUEST);
        require_once 'objects/game.php';
        require_once 'board/chatbox.php';
        require_once 'gamepanel/gameboard.php';
        $this->variant = \libVariant::loadFromGameID($gameId);
        $this->setPlaceholder('variant', $this->variant);
        \libVariant::setGlobals($this->variant);
        $this->game = $this->variant->panelGameBoard($gameId);
        $this->setPlaceholder('game', $this->game);
        $this->loadCountries($this->variant);

        if ($this->currentUser && $this->currentUser->isAuthenticated() && $this->game->Members->isJoined() && !$this->game->Members->isTempBanned()) {
            $this->game->Members->makeUserMember($this->currentUser->id);
            $this->member = $this->game->Members->ByUserID[$this->currentUser->id];
            $this->setPlaceholder('member', $this->member);
        }
        return $this->game;
    }

    protected function loadCountries(\WDVariant $variant) : BaseController
    {
        $idx = 1;
        $countries = [];
        foreach ($variant->countries as $countryName) {
            $countries[] = ['id' => $idx, 'name' => $countryName];
            $idx++;
        }
        $this->setPlaceholder('countries', $countries);
        return $this;
    }
}