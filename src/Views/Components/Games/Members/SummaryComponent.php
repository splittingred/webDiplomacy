<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Displays a summary component, which has a BarComponent for each member
 *
 * @package Diplomacy\Views\Components\Games\Members
 */
class SummaryComponent extends BaseComponent
{
    protected string $template = 'games/members/summary.twig';
    protected Game $game;
    protected ?Member $currentMember;

    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
    }

    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'members' => $this->game->members,
            'current_member' => $this->currentMember,
        ];
    }
}
