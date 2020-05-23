<?php

namespace Diplomacy\Services\Stats;

use Diplomacy\Models\User;

class HallOfFame
{
    const SIX_MONTHS_AGO = 15552000;

    /**
     * @return array
     */
    public function getUsers()
    {
        $users = User::orderBy('points', 'desc')->limit(100)->get();

        $rankedUsers = [];
        $rank = 1;
        /** @var $user User */
        foreach ($users as $user) {
            $hash = $user->toArray();
            $hash['position'] = $rank;
            $rankedUsers[] = $hash;
            $rank++;
        }
        return $rankedUsers;
    }

    /**
     * @return array
     */
    public function getActiveUsers()
    {
        $sixMonths = time() - self::SIX_MONTHS_AGO;
        $users = User::orderBy('points', 'desc')
            ->where('timeLastSessionEnded', '>', $sixMonths)
            ->limit(100)
            ->get();

        $rankedUsers = [];
        $rank = 1;
        /** @var $user User */
        foreach ($users as $user) {
            $hash = $user->toArray();
            $hash['position'] = $rank;
            $rankedUsers[] = $hash;
            $rank++;
        }
        return $rankedUsers;
    }
}