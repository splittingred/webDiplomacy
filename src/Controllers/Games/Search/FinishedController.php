<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;

class FinishedController extends BaseController
{
    use HasGamesTab;
    public $template = 'pages/games/list/finished.twig';

    public function call()
    {
        $query = Game::finished();
        $total = $query->count();

        return [
            'games' => $query->paginate(),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
            'tabs' => $this->getGamesTabs('finished'),
        ];
    }
}
