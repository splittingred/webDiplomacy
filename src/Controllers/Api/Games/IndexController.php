<?php

namespace Diplomacy\Controllers\Api\Games;

use Diplomacy\Controllers\Api\BaseApiController;
use Diplomacy\Models\Game;

class IndexController extends BaseApiController
{
    public function call(): array
    {
        $games = Game::query();
        if (!$this->request->isEmpty('search')) {
            $name = strip_tags(filter_var($this->request->get('search'), FILTER_SANITIZE_STRING));
            $games->where('name', 'LIKE', '%'.$name.'%');
        }
        $games->limit(10);

        $o = [];
        /** @var Game $game */
        foreach ($games->get() as $game) {
            $o[] = [
                'id' => $game->id,
                'name' => $game->name,
            ];
        }
        return [
            'items' => $o,
            'pagination' => [
                'more' => false,
            ],
        ];
    }
}