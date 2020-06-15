<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * @package Diplomacy\Views\Components\Games\Members
 */
class OrdersComponent extends BaseComponent
{
    protected $template = 'games/board/orders.twig';

    /** @var Game $game */
    protected $game;
    /** @var Member $currentMember */
    protected $currentMember;

    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
    }

    public function attributes(): array
    {
        // WDVariant is a mess and relies on Globals (ugh)
        \libVariant::setGlobals($this->game->variant);
        global $Member, $Game;
        $Game = $this->game->variant->panelGameBoard($this->game->id);
        $Member = $Game->Members->ByUserID[$this->currentMember->user->id];

        $interface = $this->game->variant->OrderInterface(
            $this->renderer,
            $this->game,
            $this->currentMember->user,
            $this->currentMember,
            $this->game->currentTurn,
            $this->game->phase,
            $this->currentMember->country,
            $this->currentMember->ordersState,
            $this->game->processing->getTime()+6*60*60
        );
        $interface->load();

        return [
            'orders' => $interface->html(),
        ];
    }
}