<?php

namespace Diplomacy\Views\Components\Games\Board;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Renders the "header bar", which is a bar specific to a given member in a game. It essentially renders information
 * about only the current authenticated member, SC status bar, and such.
 *
 * @package Diplomacy\Views\Components\Games\Board
 */
class HeaderBarComponent extends BaseComponent
{
    /** @var string $template */
    protected $template = 'games/board/header_bar.twig';
    /** @var Game $game */
    protected $game;
    /** @var Member $member */
    protected $member;

    /**
     * @param Game $game
     * @param Member $member
     */
    public function __construct(Game $game, Member $member)
    {
        $this->game = $game;
        $this->member = $member;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'member' => $this->member,
        ];
    }
}