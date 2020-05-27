<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;

class MineController extends BaseController
{
    public $template = 'pages/games/list/mine.twig';

    public function call()
    {
        if (!$this->currentUser->isAuthenticated()) { $this->redirectRelative('/'); exit(); }

        $query = Game::withUser($this->currentUser->id)->notFinished();
        $total = $query->count();

        return [
            'games' => $query->paginate(10),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
        ];
    }
}
