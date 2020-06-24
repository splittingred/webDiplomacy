<?php

namespace Diplomacy\Views\Components\Games\Board;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Renders the "title bar", which is the top part of the board that has the game name, status, variant info, pot, etc
 *
 * @package Diplomacy\Views\Components\Games\Board
 */
class TitleBarComponent extends BaseComponent
{
    /** @var string $template */
    protected $template = 'games/board/title_bar.twig';
    /** @var Game $game */
    protected $game;

    /**
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
        ];
    }
}