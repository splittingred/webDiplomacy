<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;

class NewController extends BaseController
{
    use HasGamesTab;
    public $template = 'pages/games/list/new.twig';

    public function call()
    {
        $query = Game::preGame()->orderBy('id', 'desc');
        $total = $query->count();

        return [
            'games' => $query->paginate(),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
            'tabs' => $this->getGamesTabs('new'),
        ];
    }
}
