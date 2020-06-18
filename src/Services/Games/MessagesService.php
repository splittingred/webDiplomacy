<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Entities\Game as GameEntity;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member as MemberEntity;
use Diplomacy\Models\Entities\Games\Message;
use Diplomacy\Models\Entities\Games\Turn;
use Diplomacy\Models\GameMessage;
use Diplomacy\Models\Member;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;
use Illuminate\Database\Query\Builder;

/**
 * Handles operations around in-game messages
 *
 * @package Diplomacy\Services\Games
 */
class MessagesService
{
    /** @var int Show all messages accessible to member */
    const FILTER_ALL = -1;
    /** @var int Show only messages target for Global consumption */
    const FILTER_GLOBAL = -2;

    /**
     * @param int $userId
     * @return int
     */
    public function totalForUser(int $userId) : int
    {
        return (int)Member::select(Member::raw('SUM(gameMessagesSent)'))->forUser($userId)->count();
    }

    /**
     * Search in-game messages
     *
     * @param int $gameId
     * @param int $filter
     * @param int $memberCountryId
     * @param int $perPage
     * @return Collection
     */
    public function search(int $gameId, int $filter = self::FILTER_GLOBAL, int $memberCountryId = 0, int $perPage = 10)
    {
        /** @var Builder $query */
        $query = GameMessage::where('gameID', $gameId);

        if ($filter == self::FILTER_ALL)
        {
            $query->where(function($query) use ($memberCountryId) {
                $query->where('toCountryID', 0);
                if (!empty($memberCountryId)) {
                    $query->orWhere('fromCountryID', $memberCountryId)
                          ->orWhere('toCountryID', $memberCountryId);
                }
            });
        }
        elseif ($filter == self::FILTER_GLOBAL)
        {
            $query->where('toCountryID', 0);
        }
        elseif (!empty($memberCountryId))
        {
            $query->where(function ($query) use ($filter, $memberCountryId) {
                $query->where('toCountryID', $memberCountryId)
                      ->where('fromCountryID', $filter);
            });
            $query->whereOr(function ($query) use ($filter, $memberCountryId) {
                $query->where('fromCountryID', $memberCountryId)
                      ->where('toCountryID', $filter);
            });
        }
        else // fallback scenario, just show global only
        {
            $query->where('toCountryID', 0);
        }

        $count = $query->count();
        $query->paginate($perPage);
        $query->orderBy('timeSent', 'desc');
        $messages = $query->get();
        return new Collection($messages, $count);
    }

    /**
     * @param GameEntity $game
     * @param int $countryId
     * @param int $currentMemberCountryId
     * @param int $limit
     * @return Collection<Message>
     */
    public function forChatBox(GameEntity $game, int $countryId, $currentMemberCountryId = -1, int $limit = 50): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = GameMessage::query();

        if ($countryId == Country::ALL)
        {
            $q->where('toCountryID', '=', Country::GLOBAL);
            if ($currentMemberCountryId > 0) {
                $q->orWhere('fromCountryID', $currentMemberCountryId);
                $q->orWhere('toCountryID', $currentMemberCountryId);
            }
        }
        elseif ($countryId == Country::GLOBAL)
        {
            // Get all messages addressed to everyone
            $q->where('toCountryID', '=', Country::GLOBAL);
        }
        else
        {
            // To current user, from another country
            $q->where(function ($query) use ($countryId, $currentMemberCountryId) {
                $query->where('toCountryID', '=', $currentMemberCountryId)
                      ->where('fromCountryID', '=', $countryId);
            });
            // From current user, to another country
            $q->orWhere(function ($query) use ($countryId, $currentMemberCountryId) {
                $query->where('fromCountryID', '=', $currentMemberCountryId)
                      ->where('toCountryID', '=', $countryId);
            });
        }

        $q->forGame($game->id);

        $total = $q->count();

        $q->orderBy('timeSent', 'asc')
            ->limit($limit);

        $entities = [];
        /** @var GameMessage $message */
        foreach ($q->get() as $message) {
            $entity = new Message();
            $entity->gameId = $message->gameID;
            $entity->message = $message->message;
            $entity->turn = new Turn($message->turn, $game->variant->turnAsDate($message->turn));
            $entity->toCountry = new Country($message->toCountryID, $game->variant->getCountryName($message->toCountryID));
            $entity->fromCountry = new Country($message->fromCountryID, $game->variant->getCountryName($message->fromCountryID));
            $entities[] = $entity;
        }

        return new Collection($entities, $total);
    }

    /**
     * Can the member send a message in this phase?
     *
     * @param GameEntity $game
     * @param MemberEntity $member
     * @param int $countryId
     * @return bool
     */
    public function canSend(GameEntity $game, MemberEntity $member, int $countryId): bool
    {
        $pressAllowsDMs = $game->pressType->allowPrivateMessages();
        $phaseAllowsPress = $game->phase->isPressAllowed();

        return (
            (string)$game->pressType == 'Regular' // always allow messages in regular press type
            || $member->isCountry($countryId) // can always send to self
            || ($pressAllowsDMs && $phaseAllowsPress) // does this press type allow it in this phase?
            || ($countryId == Country::GLOBAL && $game->pressType->allowPublicPress($game->phase)) // can public press be sent?
        );
    }

    /**
     * @param GameEntity $game
     * @param MemberEntity $member
     * @param int $countryId
     * @param string $message
     * @throws \Exception
     */
    public function sendToCountry(GameEntity $game, MemberEntity $member, int $countryId, string $message): Result
    {
        $countryName = $game->variant->getCountryName($countryId);

        if ($this->canSend($game, $member, $countryId))
        {
            if ($countryId != Country::GLOBAL && $member->hasMutedCountry($countryId)) {
                \libGameMessage::send($member->country->id, $countryId, 'Cannot send message; this country has muted you.');
            } else {
                \libGameMessage::send($countryId, $member->country->id, $message);
            }
        }
        elseif ($member->user->isModerator())
        {
            \libGameMessage::send(Country::GLOBAL, 'Moderator', '('.$member->user->username.'): '.$countryName);
        }
        elseif ($game->isDirector($member->user))
        {
            \libGameMessage::send(Country::GLOBAL, 'Game Director', '('.$member->user->username.'): '.$countryName);
        }
        elseif ($game->isTournamentDirector($member->user) || $game->isTournamentCoDirector($member->user))
        {
            \libGameMessage::send(Country::GLOBAL, 'Tournament Director', '('.$member->user->username.'): '.$countryName);
        }

        return new Success();
    }

    /**
     * Register a message as seen by a country
     *
     * @param MemberEntity $member
     * @param int $countryId
     * @return Result
     */
    public function markCountryMessageSeen(MemberEntity $member, int $countryId): Result
    {
        $memberModel = Member::find($member->id);
        return $memberModel->markMessageSeen($countryId) ? (new Success()) : (new Failure());
    }

    /**
     * Register a message as unseen by a country
     *
     * @param MemberEntity $member
     * @param int $countryId
     * @return Result
     */
    public function markCountryMessageUnseen(MemberEntity $member, int $countryId): Result
    {
        $memberModel = Member::find($member->id);
        return $memberModel->markMessageUnseen($countryId) ? (new Success()) : (new Failure());
    }
}