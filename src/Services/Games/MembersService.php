<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Games\Country as CountryEntity;
use Diplomacy\Models\Entities\Games\Member as MemberEntity;
use Diplomacy\Models\Member;
use Diplomacy\Models\MutedCountry;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Success;

/**
 * Handles operations around members of games
 *
 * @package Diplomacy\Services\Games
 */
class MembersService
{
    public function findForGame(int $userId, int $gameId)
    {
        return Member::where('userID', $userId)->where('gameID', $gameId)->first();
    }

    /**
     * Toggle the mute status for a country for a specific member
     *
     * @param MemberEntity $member
     * @param CountryEntity $country
     * @return Failure|Success
     */
    public function toggleCountryMute(MemberEntity $member, CountryEntity $country)
    {
        if (!$member->user->isAuthenticated()) {
            return Failure::withError('not_authenticated', 'User not authenticated');
        }

        /** @var MutedCountry $mc */
        $mc = MutedCountry::forCountry($country->id)->forGame($member->gameId)->forUser($member->user->id)->firstOrNew();
        try {
            $result = $mc->exists ? $mc->delete() : $mc->save();
        } catch (\Exception $exception) {
            return Failure::withError('internal', $exception->getMessage());
        }
        return $result ? new Success() : Failure::withError('internal', 'Failed to persist mute to database.');
    }
}