<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status as MemberStatus;
use Diplomacy\Models\Entities\Games\MissingPlayerPolicy;
use Diplomacy\Models\Entities\Games\Processing;
use Diplomacy\Models\Entities\Games\Status as GameStatus;
use Diplomacy\Models\Entities\Games\Turn;
use Diplomacy\Models\Entities\Users\MutedCountry;
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
use Diplomacy\Models\Tournament;

class Factory
{
    protected $misc;

    public function __construct()
    {
        global $app;
        $this->misc = $app->make('Misc');
    }

    /**
     * @param int $gameId
     * @return mixed
     * @throws InvalidPotTypeException
     * @throws InvalidPressTypeException
     * @throws InvalidDrawTypeException
     * @throws InvalidPlayersTypeException
     */
    public function build(int $gameId): GameEntity
    {
        /** @var Game $game */
        $game = Game::find($gameId);
        $director = $game->directorUserID ? $game->director()->first() : null;
        $variant = $game->getVariant();

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

        /** @var Tournament $tournament */
        $tournament = $game->getTournament();
        if ($tournament) $entity->tournament = $tournament->toEntity();

        // potential value objects
        $entity->startTime = $game->startTime;
        $entity->finishTime = $game->finishTime;

        $entity->processing = new Processing(
            (string)$game->processStatus,
            (int)$game->processTime,
            (int)$game->phaseMinutes,
            is_null($game->pauseTimeRemaining) ? -1 : (int)$game->pauseTimeRemaining,
        );

        // value objects
        $entity->status = new GameStatus($game->gameOver);
        $entity->currentTurn = new Turn($game->turn, $variant->turnAsDate($game->turn));
        $entity->missingPlayerPolicy = new MissingPlayerPolicy($game->missingPlayerPolicy);
        $entity->drawType = DrawType::build($game->drawType);
        $entity->pressType = PressType::build($game->pressType);
        $entity->potType = PotType::build($game->potType, $game->pot);
        $entity->playersType = PlayersType::build($game->playerTypes);
        $entity->variant = $game->getVariant(); // TODO: Convert to normal entity
        $entity->phase = new Phase($game->phase, (int)$game->phaseMinutes, (int)$game->phaseSwitchPeriod);
        $entity->nextPhase = new Phase($game->phase, (int)$game->phaseMinutes, (int)$game->phaseSwitchPeriod);
        $entity->director = $director ? $director->toEntity() : null;
        $entity->featured = $game->pot > $this->misc->GameFeaturedThreshold;

        $countries = $entity->variant->countries;
        foreach ($countries as $idx => $name) {
            $entity->countries[] = new Country($idx + 1, $countries[$idx]);
        }

        $members = $game->members()->get(); // TODO: Improve this query so that it doesn't cause O(N) queries

        /** @var \Diplomacy\Models\Member $model */
        foreach ($members as $model) {
            $member = new Member();
            $member->id = (int)$model->id;
            $member->gameId = (int)$model->gameID;
            $member->country = new Country($model->countryID, $countries[$model->countryID - 1]);
            $member->status = new MemberStatus((string)$model->status);
            $member->timeLoggedIn = (int)$model->timeLoggedIn;
            $member->bet = (int)$model->bet;
            $member->missedPhases = (int)$model->missedPhases;
            $member->newMessagesFrom = array_filter(explode(',', $model->newMessagesFrom));
            $member->supplyCenterCount = (int)$model->supplyCenterNo;
            $member->unitCount = (int)$model->unitNo;
            $member->votes = array_filter(explode(',', $model->votes));
            $member->pointsWon = (int)$model->pointsWon;
            $member->gameMessagesSent = (int)$model->gameMessagesSent;
            $member->ordersState = new OrdersState(array_filter(explode(',', $model->orderStatus)));
            $member->hideNotifications = !empty($model->hideNotifications);
            $member->excusedMissedTurns = (int)$model->excusedMissedTurns;
            $member->isDirector = $entity->director && $entity->director->id == $model->userID;
            if ($entity->tournament) {
                $member->isTournamentDirector = $entity->tournament->director && $model->userID == $entity->tournament->director->id;
                $member->isTournamentCoDirector = $entity->tournament->coDirector && $model->userID == $entity->tournament->coDirector->id;
            }

            $member->user = $model->user->toEntity();

            foreach ($model->user->mutedCountriesForGame($game->id)->get() as $mc) {
                $member->mutedCountries[] = new MutedCountry(
                    $mc->muteCountryID,
                    $mc->gameID,
                    strtotime($mc->timestamp),
                );
            }

            $member->supplyCenterTarget = $variant->supplyCenterTarget;
            $entity->members[] = $member;
        }

//        echo '<pre>';
//        var_dump($entity);
//        die();
        return $entity;
    }
}