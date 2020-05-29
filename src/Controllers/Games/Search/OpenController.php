<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;

class OpenController extends BaseController
{
    use HasGamesTab;
    public $template = 'pages/games/list/open.twig';

    public function call()
    {
        $query = Game::joinableForUser($this->currentUser->id, $this->currentUser->points, $this->currentUser->reliabilityRating)
            ->orderBy('id', 'desc');
        $total = $query->count();

        return [
            'games' => $query->paginate(),
            'total_pages' => $this->getTotalPages($total),
            'pagination' => $this->getPagination($total),
            'tabs' => $this->getGamesTabs('open'),
        ];
    }
}
