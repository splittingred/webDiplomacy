<?php

namespace Diplomacy\Services\Users;

use Diplomacy\Models\CivilDisorder;
use Diplomacy\Models\Entities\User as UserEntity;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;
use Diplomacy\Models\User;
use Misc;

class RankingsService
{
    const STATUSES = [
        'won',
        'drawn',
        'survived',
        'defeated',
        'resigned'
    ];
    const RATINGS = [
        'Diplomat'          => 5,
        'Mastermind'        => 10,
        'Pro'               => 20,
        'Experienced'       => 50,
        'Member'            => 90,
        'Casual player'     => 100,
        'Political puppet'  => 99999999999999,
    ];

    /** @var Misc */
    protected $misc;

    public function __construct()
    {
        global $app;
        $this->misc = $app->make('Misc');
    }

    /**
     * @param UserEntity $user
     * @return array[]
     */
    public function getForUser(UserEntity $user)
    {
        return array_merge($this->getGeneralRankings($user), [
            'classic' => $this->getClassicRankings($user),
            'gunboat' => $this->getClassicGunboatRankings($user),
            'press' => $this->getClassicPressRankings($user),
            'ranked' => $this->getClassicRankedRankings($user),
            'variants' => $this->getVariantRankings($user),
        ]);
    }

    /**
     * Get general rankings for a User. This is an expensive function, so never call this within a loop.
     *
     * @param UserEntity $user
     * @return array
     */
    private function getGeneralRankings(UserEntity $user): array
    {
        /** @var User $userModel */
        $userModel = User::find($user->id);
        $canSeeAnon = $user->isModerator();

        $rankingDetails = [
            'position' => $userModel->queryRanking(),
            'activePosition' => $userModel->queryActiveRanking(),
            'worth' => $userModel->queryWorth(),
            'civilDisorders' => $user->counts->civilDisorders,
            'civilDisordersTakenOver' => $user->counts->civilDisordersTakenOver,
            'byStatus' => $this->getGeneralStats($userModel),
            'anon' => $this->getAnonymousStats($userModel),
        ];

        $rankingDetails['takenOver'] = CivilDisorder::takenOverByUser($user->id)->count();

        $rankingDetails['rankingPlayers'] = $this->misc->RankingPlayers;
        $rankingPlayers = ($rankingDetails['rankingPlayers'] == 0 ? 1 : $rankingDetails['rankingPlayers']);
        $rankingDetails['percentile'] = ceil(100.0 * $rankingDetails['position'] / $rankingPlayers);

        // Calculate the percentile of the player. Smaller is better.
        $rankingDetails['isTop100'] = $rankingDetails['position'] <= 100;

        foreach (static::RATINGS as $name => $limit)
        {
            if ($rankingDetails['percentile'] <= $limit) {
                $rankingDetails['rank'] = $name;
                break;
            }
        }

        $rankingDetails['pointsInPlay'] = $rankingDetails['worth'] - $user->points - ($canSeeAnon ? 0 : $rankingDetails['anon']['points']);

        return $rankingDetails;
    }


    /**
     * Get rankings for games
     *
     * @param User $userModel
     * @return array
     */
    public function getGeneralStats(User $userModel): array
    {
        $q = $userModel->statusCountsForUser();

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status)
        {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }

    /**
     * @param User $userModel
     * @return array
     */
    public function getAnonymousStats(User $userModel): array
    {
        $anonStatuses = $userModel->queryAnonymousGameCounts();
        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'points' => 0,
            'total' => 0,
        ];
        foreach ($anonStatuses as $anonStatus) {
            $details['statuses'][strtolower($anonStatus['status'])] = $anonStatus['total'];
            $details['points'] += $anonStatus['bet'];
            $details['total'] += $anonStatus['total'];
        }
        return $details;
    }


    /**
     * Get rankings for active classic human games
     *
     * @param UserEntity $user
     * @return array
     */
    public function getClassicRankings(UserEntity $user): array
    {
        $memberTable = Member::getTableName();
        $q = Member::query();
        $q = $q->finishedClassicHumanGames()
            ->forUser($user->id)
            ->playing()
            ->select([
                $q->raw('COUNT('.$memberTable.'.id) AS total'),
                $memberTable.'.status',
            ])
            ->groupBy(Member::getTableName().'.status');

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status)
        {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }

    /**
     * A lighter version of rankingDetails with just the game % stats for classic gunboat games.
     *
     * @param UserEntity $user
     * @return array
     */
    public function getClassicGunboatRankings(UserEntity $user): array
    {
        $memberTable = Member::getTableName();
        $q = Member::query();
        $q = $q->finishedClassicHumanGunboatGames()
            ->forUser($user->id)
            ->playing()
            ->select([
                $q->raw('COUNT('.$memberTable.'.id) AS total'),
                $memberTable.'.status',
            ])
            ->groupBy(Member::getTableName().'.status');

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status)
        {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }

    /**
     * A lighter version of rankingDetails with just the game % stats for classic press games.
     *
     * @param UserEntity $user
     * @return array
     */
    public function getClassicPressRankings(UserEntity $user): array
    {
        $memberTable = Member::getTableName();
        $q = Member::query();
        $q = $q->finishedClassicHumanPressGames()
            ->forUser($user->id)
            ->playing()
            ->select([
                $q->raw('COUNT(' . $memberTable . '.id) AS total'),
                $memberTable . '.status',
            ])
            ->groupBy(Member::getTableName() . '.status');

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status) {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }

    /**
     * A lighter version of rankingDetails with just the game % stats for classic ranked games.
     *
     * @param UserEntity $user
     * @return array
     */
    public function getClassicRankedRankings(UserEntity $user): array
    {
        $memberTable = Member::getTableName();
        $q = Member::query();
        $q = $q->finishedClassicRankedGames()
            ->forUser($user->id)
            ->playing()
            ->select([
                $q->raw('COUNT(' . $memberTable . '.id) AS total'),
                $memberTable . '.status',
            ])
            ->groupBy(Member::getTableName() . '.status');

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status) {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }


    /**
     * A lighter version of rankingDetails with just the game % stats for variant games.
     *
     * @param UserEntity $user
     * @return array
     */
    public function getVariantRankings(UserEntity $user)
    {
        $memberTable = Member::getTableName();
        $q = Member::query();
        $q = $q->finishedHumanVariantGames()
            ->forUser($user->id)
            ->playing()
            ->select([
                $q->raw('COUNT(' . $memberTable . '.id) AS total'),
                $memberTable . '.status',
            ])
            ->groupBy(Member::getTableName() . '.status');

        $details = [
            'statuses' => array_fill_keys(static::STATUSES, 0),
            'total' => 0,
        ];
        foreach ($q->get() as $status) {
            $details['statuses'][strtolower($status['status'])] = $status['total'];
            $details['total'] += $status['total'];
        }
        return $details;
    }
}