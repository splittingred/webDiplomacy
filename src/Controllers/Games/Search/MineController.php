<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;

class MineController extends BaseController
{
    use HasGamesTab;
    public $template = 'pages/games/list/mine.twig';

    public function call()
    {
        if (!$this->currentUser->isAuthenticated()) { $this->redirectRelative('/'); exit(); }

        $query = Game::activeForUser($this->currentUser->id);
        $total = $query->count();

        return [
            'games' => $query->paginate(),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
            'tabs' => $this->getGamesTabs('mine'),
        ];
    }
}
