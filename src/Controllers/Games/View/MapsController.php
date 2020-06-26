<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Services\Games\OrdersService;

class MapsController extends BaseController
{
    protected string $template = 'pages/games/view/maps.twig';

    public function call(): array
    {
        return [
            'maps' => $this->getMaps(),
        ];
    }

    /**
     * @return array
     */
    protected function getMaps(): array
    {
        $maps = [];
        for ($i = $this->game->turn; $i >= 0; $i--)
        {
            $hideMoves = $i == $this->game->turn ? '&hideMoves=1' : '';
            $maps[] = [
                'name' => $this->game->datetxt($i),
                'map' => "/map.php?gameID={$this->game->id}$hideMoves&turn=$i",
                'large_map' => "/map.php?gameID={$this->game->id}&largemap=on$hideMoves&turn=$i",
            ];
        }
        return $maps;
    }
}