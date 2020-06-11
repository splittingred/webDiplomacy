<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\BaseComponent;

class NoticeBarComponent extends BaseComponent
{
    protected $template = 'games/notice_bar.twig';
    protected $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function attributes(): array
    {
        $attributes = [];
        if ($this->game->phase->isFinished())
        {
            $attributes['message'] = $this->getGameOverDetails();
        }
        elseif ($this->game->phase->isPreGame())
        {
            $totalPlayers = $this->game->getMemberCount();
            if ($this->game->allSlotsFilled()) {
                if ($this->game->phase->isLive()) {
                    $attributes['message'] = $totalPlayers . ' players joined; game will start at the scheduled time';
                } else {
                    $attributes['message'] = $totalPlayers . ' players joined; game will start on next process cycle';
                }
            } else {
                $neededPlayers = $this->game->getCountryCount();
                $attributes['message'] = "Game is currently at $totalPlayers members out of $neededPlayers - waiting for remaining to join.";
            }
        }
        elseif ($this->game->missingPlayerPolicy->isWait() && !$this->game->members->isReadyForProcessing() && $this->game->processing->overdue()) {
            $attributes['message'] = 'One or more players need to complete their orders before this wait-mode game can go on';
        }
        return $attributes;
    }

    /**
     * @return string
     */
    public function getGameOverDetails() : string
    {
        if ($this->game->status->wasWon())
        {
            $winner = $this->game->getWinner();
            if ($winner) {
                $countryName = $winner->country->shortName();
                $countryId = $winner->country->id;
                $userId = $winner->user->id;
                $username = $winner->user->username;
                return "Game won by <a href=\"/profile.php?userID={$userId}\">{$username}</a> (<span class=\"country{$countryId}\">{$countryName}</span>)";
            }
        }
        elseif ($this->game->status->wasDrawn())
        {
            return 'Game drawn';
        }
        return '';
    }
}