<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Game;

class GameBoard
{
    public $game;
    public $currentUser;

    public function __construct(Game $game, \User $currentUser)
    {
        $this->game = $game;
        $this->currentUser = $currentUser;
    }

    /**
     * @param mixed $turn
     * @return string
     */
    public function getPhaseAsText($turn = null) : string
    {
        if (is_null($this->game->turn)) $turn = $this->game->turn;

        return $this->game->getVariant()->turnAsDate($turn);
    }

    /**
     * @return string
     */
    public function getPausedTimeRemainingAsText() : string
    {
        return \libTime::timeLengthText($this->game->getPauseTimeRemaining());
    }

    /**
     * @return string
     */
    public function getPointsIcon() : string
    {
        return \libHTML::points();
    }

    /**
     * Return the next process time in textual format, in terms of time remaining
     *
     * @return string
     */
    public function getNextProcessTime() : string
    {
        if ($this->game->processTimePassed())
            return 'Now';
        else
            return \libTime::remainingText($this->game->processTime);
    }


    public function getProcessedTimeAsText()
    {
        return \libTime::detailedText($this->game->processTime);
    }

    /**
     * @return string
     */
    public function getHoursPerPhase() : string
    {
        return \libTime::timeLengthText($this->game->getPhaseHours());
    }

    /**
     * @return string
     */
    public function getNoticeBar() : string
    {
        $countries = $this->game->getVariant()->countries;
        $totalPlayers = count($countries);
        $memberTotal = count($this->game->getMembers()->ByID);

        if ($this->game->isFinished()) {
            return $this->getGameOverDetails();
        }
        elseif ($this->game->isPreGame() && $memberTotal == $totalPlayers)
        {
            if ($this->game->isLiveGame()) {
                return $totalPlayers . ' players joined; game will start at the scheduled time';
            } else {
                return $totalPlayers . ' players joined; game will start on next process cycle';
            }
        }
        elseif ($this->game->missingPlayerPolicy == 'Wait' && !$this->game->getMembers()->isCompleted() && time() >= $this->game->processTime) {
            return 'One or more players need to complete their orders before this wait-mode game can go on';
        }
        return '';
    }

    /**
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
        return $this->game->getMembers()->ByUserID[$this->currentUser->id]->memberHeaderBar();
    }

    /**
     * @return string
     */
    public function getGameOverDetails() : string
    {
        if ($this->game->gameOver == 'Won')
        {
            // TODO: replace with just getting last element
            $Winner = end($this->game->getMembers()->ByStatus['Won']);
            return 'Game won by ' . $Winner->memberName();
        }
        elseif( $this->game->gameOver == 'Drawn' )
        {
            return 'Game drawn';
        }
        return '';
    }
}

