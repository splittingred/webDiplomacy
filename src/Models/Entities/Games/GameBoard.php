<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Game;
use \Diplomacy\Models\Entities\Game as GameEntity;
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

    /**
     * The occupation bar HTML; only generate it once then store it here, as it is usually used at least twice for one game
     * @var string
     */
    private $occupationBarCache;

    /**
     * The occupation bar; a bar representing each of the countries current progress as measured by the number of SCs.
     * If called pre-game it goes from red to green as 1 to 7 players join the game.
     *
     * @return string
     */
    public function occupationBar() : string
    {
        if (isset($this->occupationBarCache)) return $this->occupationBarCache;

        \libHTML::$first = true;
        if ($this->gameEntity->phase->isStarted())
        {
            $percentages = $this->gameEntity->supplyCenterPercentages();

            $members = [];
            foreach ($percentages as $countryID => $width) {
                if ($width <= 0) continue;

                $members[] = [
                    'country_id' => $countryID,
                    'width' => $width,
                    'first' => \libHTML::first(),
                ];
            }

            $buf = $this->renderer->render('games/members/occupation_bar/active.twig',[
                'members' => $members,
            ]);
        }
        else
        {
            $countryCount = $this->gameEntity->getCountryCount();
            $playerCount = $this->gameEntity->getMemberCount();
            $joinedPercent = ceil(($playerCount * 100.0 / $countryCount));

            $buf = $this->renderer->render('games/members/occupation_bar/active.twig',[
                'joined_percent' => $joinedPercent,
                'remaining_percent' => 100 - $joinedPercent,
            ]);
        }

        $this->occupationBarCache = $buf;
        return $this->occupationBarCache;
    }
}

