<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Games\Country as CountryEntity;
use Diplomacy\Models\Entities\Games\Member as MemberEntity;
use Diplomacy\Models\Member;
use Diplomacy\Models\MutedCountry;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;

/**
 * Handles operations around members of games
 *
 * @package Diplomacy\Services\Games
 */
class MembersService
{
    /**
     * @param int $userId
     * @param int $gameId
     * @return Member
     */
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
    public function toggleCountryMute(MemberEntity $member, CountryEntity $country): Result
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

    public function makeBet(\Diplomacy\Models\Entities\Games\Member $member)
    {
        if ( $bet > $this->points && !$User->type['Bot'] )
        {
            throw new Exception(l_t('You do not have enough points to join this game. You need to bet %s.',$bet.' '.libHTML::points()));
        }

        if ($User->type['Bot'])
        {
            User::pointsTransfer($this->userID, 'Bet', $bet, $this->gameID, $this->id);
            $this->Game->pot += 5;
            return 5;
        }

        User::pointsTransfer($this->userID, 'Bet', $bet, $this->gameID, $this->id);

        $this->points -= $bet;
        $this->Game->pot += $bet;

        if($User instanceof User && $User->id == $this->userID) $User->points -= $bet;

    }
}