<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;

class NewController extends BaseController
{
    public $template = 'pages/games/list/new.twig';

    public function call()
    {
        if (!$this->currentUser->isAuthenticated()) { $this->redirectRelative('/'); exit(); }

        $query = Game::query()->preGame();
        $total = $query->count();

        return [
            'games' => $query->paginate(),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
        ];
    }
}
