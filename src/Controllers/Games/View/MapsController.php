<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Services\Games\OrdersService;

class MapsController extends BaseController
{
    protected $template = 'pages/games/view/maps.twig';

    public function call()
    {
        return [
            'maps' => $this->getMaps(),
        ];
    }

    protected function getMaps()
    {
        $maps = [];
        for ($i = $this->game->turn; $i>=0; $i--)
        {
            $maps[] = [
                'name' => $this->game->datetxt($i),
                'map' => "/map.php?gameID={$this->game->id}&turn=$i",
                'large_map' => "/map.php?gameID={$this->game->id}&largemap=on&turn=$i",
            ];
        }
        return $maps;
    }
}