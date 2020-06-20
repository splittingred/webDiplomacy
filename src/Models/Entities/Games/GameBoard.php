<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Game;
use \Diplomacy\Models\Entities\Game as GameEntity;
use Diplomacy\Views\Components\Games\Members\AllMembersBarComponent;
use Diplomacy\Views\Renderer;

class GameBoard
{
    public $game;
    public $gameEntity;
    public $currentUser;

    /** @var Renderer $renderer */
    protected $renderer;

    public function __construct(Game $game, GameEntity $gameEntity, \User $currentUser)
    {
        global $app;
        $this->game = $game;
        $this->gameEntity = $gameEntity;
        $this->currentUser = $currentUser;
        $this->renderer = $app->make('renderer');
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
     * TODO: not sure this is correct. Fix it.
     *
     * @return bool
     */
    public function allMembersAreJoined() : bool
    {
        return $this->game->getMembers()->isJoined();
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

