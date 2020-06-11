<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class BetWonComponent extends BaseComponent
{
    protected $template = 'games/members/betWon.twig';
    /** @var Member $member */
    protected $member;
    /** @var Game $game */
    protected $game;

    public function __construct(Game $game, Member $member)
    {
        $this->game = $game;
        $this->member = $member;
    }

    public function attributes(): array
    {
        $playingOrLeft = $this->member->status->isPlaying() || $this->member->status->hasLeft();
        $hasWonPoints = $this->member->status->hasWon() || ($this->game->potType->grantsPointsOnSurvivals() && $this->member->status->hasSurvived()) || $this->member->status->hasDrawn();

        $betValue = $this->game->potType->amount;

        if ($playingOrLeft) {
            $betValue = $this->game->potType->pointsForDraw($this->game, $this->member);
        } elseif ($hasWonPoints) {
            $betValue = $this->member->pointsWon;
        }
        $betClass = $betValue > $this->member->bet ? 'good' : 'bad';
        return [
            'bet' => $this->member->bet,
            'betValue' => $betValue,
            'betClass' => $betClass,
            'hasWonPoints' => $hasWonPoints,
            'member' => $this->member,
            'pointsIcon' => \libHTML::points(),
        ];
    }
}