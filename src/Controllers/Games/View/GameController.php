<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Models\Collection;

class GameController extends BaseController
{
    protected $template = 'pages/games/view/index.twig';

    public function getPageTitle(): string
    {
        return $this->game->name;
    }

    public function call()
    {
        $this->redirectRelative('/board.php?gameID='.$this->game->id);
    }
}