<?php

namespace Diplomacy\Views\Components\Admin\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Displays the Dropdown component for loading the admin modals.
 *
 * @package Diplomacy\Views\Components\Admin
 */
class DropdownComponent extends BaseComponent
{
    /** @var string $template */
    protected string $template = 'admin/games/dropdown.twig';
    /** @var Game $game */
    protected Game $game;
    /** @var User $user */
    protected User $user;

    /**
     * @param Game $game
     * @param User $user
     */
    public function __construct(Game $game, User $user)
    {
        $this->game = $game;
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'user' => $this->user,
        ];
    }
}