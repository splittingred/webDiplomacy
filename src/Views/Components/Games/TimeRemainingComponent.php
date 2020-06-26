<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\BaseComponent;

class TimeRemainingComponent extends BaseComponent
{
    protected string $template = 'games/time_remaining.twig';
    protected bool $displayTimeAsText;
    protected Game $game;

    public function __construct(Game $game, bool $displayTimeAsText = true)
    {
        $this->game = $game;
        $this->displayTimeAsText = $displayTimeAsText;
    }

    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'displayTimeAsText' => $this->displayTimeAsText,
        ];
    }
}