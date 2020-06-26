<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\BaseComponent;

class MapComponent extends BaseComponent
{
    protected string $template = 'games/board/map.twig';
    protected Game $game;
    protected \User $currentUser;

    public function __construct(Game $game, \User $currentUser)
    {
        $this->game = $game;
        $this->currentUser = $currentUser;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $showMoves = true; // TODO: $this->currentUser->options->value['showMoves'] == 'Yes'

        $turn = $this->game->currentTurn->id;
        $mapTurn = $this->game->phase->isPreGame() || $this->game->phase->isMoves() ? $turn - 1 : $turn;

        $smallMapUrl = '/map.php?gameID='.$this->game->id.'&turn='.$mapTurn .(!$showMoves ? '&hideMoves' : '');
        $largeMapUrl = $smallMapUrl.'&mapType=large'.($showMoves ? '&hideMoves' : '');

        $staticFilename = \Game::mapFilename($this->game->id, $mapTurn, 'small');

        if (file_exists($staticFilename) && $showMoves) {
            $smallMapUrl = STATICSRV . $staticFilename . '?nocache=' . rand(0, 99999);
        }

        return [
            'game' => $this->game,
            'currentUser' => $this->currentUser,
            'smallMapUrl' => $smallMapUrl,
            'largeMapUrl' => $largeMapUrl,
            'mapTurn' => $mapTurn,
        ];
    }
}