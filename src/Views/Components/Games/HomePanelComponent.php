<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * The component that is used to render a small view of a game, suitable for the home page
 *
 * @package Diplomacy\Views\Components\Games
 */
class HomePanelComponent extends BaseComponent
{
    protected $template = 'games/home_panel.twig';
    protected $game;
    protected $currentMember;

    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'currentMember' => $this->currentMember,
            'gameAboveReliabilityThreshold' => (float)$this->game->minimumReliabilityRating > (float)$this->currentMember->user->reliabilityRating,
        ];
    }
}