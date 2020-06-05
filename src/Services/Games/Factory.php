<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Members\Status as MemberStatus;
use Diplomacy\Models\Entities\Games\MissingPlayerPolicy;
use Diplomacy\Models\Entities\Games\Status as GameStatus;
use Diplomacy\Models\Entities\Games\Turn;
use Diplomacy\Models\Game;
use Diplomacy\Models\Entities\Games\DrawType;
use Diplomacy\Models\Entities\Games\PlayersType;
use Diplomacy\Models\Entities\Game as GameEntity;
use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PotType;
use Diplomacy\Models\Entities\Games\PressType;
use Diplomacy\Models\Entities\Games\PotTypes\InvalidTypeException as InvalidPotTypeException;
use Diplomacy\Models\Entities\Games\PressTypes\InvalidTypeException as InvalidPressTypeException;
use \Diplomacy\Models\Entities\Games\DrawTypes\InvalidTypeException as InvalidDrawTypeException;
use Diplomacy\Models\Entities\Games\PlayersTypes\InvalidTypeException as InvalidPlayersTypeException;

class Factory
{
    /**
     * @param int $gameId
     * @return mixed
     * @throws InvalidPotTypeException
     * @throws InvalidPressTypeException
     * @throws InvalidDrawTypeException
     * @throws InvalidPlayersTypeException
     */
    public function build(int $gameId)
    {
        /** @var Game $game */
        $game = Game::find($gameId);
        $director = $game->directorUserID ? $game->director()->first() : null;

        $entity = new GameEntity();
        $entity->id = (int)$game->id;
        $entity->name = (string)$game->name;
        $entity->password = (string)$game->password;
        $entity->pauseTimeRemaining = (int)$game->pauseTimeRemaining;
        $entity->minimumBet = (int)$game->minimumBet;
        $entity->anonymous = $game->anon == 'Yes';
        $entity->attempts = (int)$game->attempts;
        $entity->minimumReliabilityRating = (int)$game->minimumReliabilityRating;
        $entity->excusedMissedTurns = (int)$game->excusedMissedTurns;

        // potential value objects
        $entity->startTime = $game->startTime;
        $entity->finishTime = $game->finishTime;

        $entity->processTime = $game->processTime;
        $entity->processStatus = $game->processStatus;

        // value objects
        $entity->status = new GameStatus($game->gameOver);
        $entity->currentTurn = new Turn($game->turn, $game->getVariant()->turnAsDate($game->turn));
        $entity->missingPlayerPolicy = new MissingPlayerPolicy($game->missingPlayerPolicy);
        $entity->drawType = DrawType::build($game->drawType);
        $entity->pressType = PressType::build($game->pressType);
        $entity->potType = PotType::build($game->potType, $game->pot);
        $entity->playersType = PlayersType::build($game->playerTypes);
        $entity->variant = $game->getVariant(); // TODO: Convert to normal entity
        $entity->phase = new Phase($game->phase, (int)$game->phaseMinutes, (int)$game->phaseSwitchPeriod);
        $entity->nextPhase = new Phase($game->phase, (int)$game->phaseMinutes, (int)$game->phaseSwitchPeriod);
        $entity->director = $director ? $director->toEntity() : null;

        $countries = $entity->variant->countries;
        /** @var \Diplomacy\Models\Member $model */
        foreach ($game->members()->get() as $model) {
            $member = new Member();
            $member->id = (int)$model->id;
            $member->gameId = (int)$model->gameID;
            $member->country = new Country($model->countryID, $countries[$model->countryID - 1]);
            $member->status = new MemberStatus((string)$model->status);
            $member->timeLoggedIn = (int)$model->timeLoggedIn;
            $member->bet = (int)$model->bet;
            $member->missedPhases = (int)$model->missedPhases;
            $member->newMessagesFrom = $model->newMessagesFrom;
            $member->supplyCenterCount = (int)$model->supplyCenterNo;
            $member->unitCount = (int)$model->unitNo;
            $member->votes = array_filter(explode(',', $model->votes));
            $member->pointsWon = (int)$model->pointsWon;
            $member->gameMessagesSent = (int)$model->gameMessagesSent;
            $member->orderStatus = array_filter(explode(',', $model->orderStatus));
            $member->hideNotifications = !empty($model->hideNotifications);
            $member->excusedMissedTurns = (int)$model->excusedMissedTurns;
            $entity->members[] = $member;
        }

        echo '<pre>';
        var_dump($entity);
        die();
        return $entity;
    }
}