<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Game;
use \Diplomacy\Models\Entities\Game as GameEntity;

class GameBoard
{
    public $game;
    public $gameEntity;
    public $currentUser;

    public function __construct(Game $game, GameEntity $gameEntity, \User $currentUser)
    {
        $this->game = $game;
        $this->gameEntity = $gameEntity;
        $this->currentUser = $currentUser;
    }

    /**
     * @param mixed $turn
     * @return string
     */
    public function getPhaseAsText($turn = null) : string
    {
        if (is_null($this->game->turn)) $turn = $this->game->turn;

        return $this->gameEntity->variant->turnAsDate($turn);
    }

    /**
     * @return string
     */
    public function getPausedTimeRemainingAsText() : string
    {
        return $this->gameEntity->processing->getPauseTimeRemainingAsText();
    }

    /**
     * Return the next process time in textual format, in terms of time remaining
     *
     * @return string
     */
    public function getNextProcessTime() : string
    {
        return $this->gameEntity->processing->timeRemainingAsText();
    }

    /**
     * @return string
     */
    public function getNoticeBar() : string
    {
        if ($this->gameEntity->phase->isFinished())
        {
            return $this->getGameOverDetails();
        }
        elseif ($this->gameEntity->phase->isPreGame())
        {
            $totalPlayers = $this->gameEntity->getMemberCount();
            if ($this->gameEntity->allSlotsFilled()) {
                if ($this->gameEntity->phase->isLive()) {
                    return $totalPlayers . ' players joined; game will start at the scheduled time';
                } else {
                    return $totalPlayers . ' players joined; game will start on next process cycle';
                }
            } else {
                $neededPlayers = $this->gameEntity->getCountryCount();
                return "Game is currently at $totalPlayers members out of $neededPlayers - waiting for remaining to join.";
            }
        }
        elseif ($this->gameEntity->missingPlayerPolicy->isWait() && !$this->gameEntity->members->isReadyForProcessing() && $this->gameEntity->processing->overdue()) {
            return 'One or more players need to complete their orders before this wait-mode game can go on';
        }
        return '';
    }

    /**
     * TODO: not sure this is correct. Fix it.
     *
     * @return bool
     */
    public function allMembersAreJoined() : bool
    {
        return $this->game->getMembers()->isJoined();
    }

    /**
     * @return mixed
     */
    public function getMemberHeaderBar()
    {
        $member = $this->game->getMembers()->ByUserID[$this->currentUser->id];
        return '';
        //return $member->memberHeaderBar();
    }

    /**
     * @return string
     */
    public function getGameOverDetails() : string
    {
        if ($this->gameEntity->status->wasWon())
        {
            $winner = $this->gameEntity->getWinner();
            if ($winner) {
                return 'Game won by ' . $winner->user->username;
            }
        }
        elseif ($this->gameEntity->status->wasDrawn())
        {
            return 'Game drawn';
        }
        return '';
    }
}

