<?php

namespace Diplomacy\Services\Users;

use Diplomacy\Models\Entities\User;

class ReliabilityService
{
    /**
     * TODO: Review this to make sure it's accurate.
     *
     * @param User $userEntity
     * @return array
     */
    public function forUser(User $userEntity)
    {
        $user = \Diplomacy\Models\User::find($userEntity->id);
        $allMissedTurns = $user->missedTurns()->unexcused()->count();
        $yearlyMissedTurns = $user->missedTurns()->nonLive()->unexcused()->lastYear()->count();
        $recentUnexcusedMissedTurns = $user->missedTurns()->unexcused()->recent()->count();
        $allLiveUnexcusedMissedTurns = $user->missedTurns()->live()->unexcused()->count();
        $recentLiveUnexcusedMissedTurns = $user->missedTurns()->live()->unexcused()->recent()->count();

        $userPhaseCount = $user->yearlyPhaseCount;
        $yearlyPenalty = $yearlyMissedTurns * 5;
        $recentPenalty = $recentUnexcusedMissedTurns * 6;
        $liveLongPenalty = $allLiveUnexcusedMissedTurns * 5;
        $liveShortPenalty = $recentLiveUnexcusedMissedTurns * 6;
        $basePercentage = (100 * (1 - ($yearlyMissedTurns / max($userPhaseCount,1))));

        $reliabilityRating = max(($basePercentage - $recentPenalty - $yearlyPenalty - $liveShortPenalty - $liveLongPenalty),0);
        return [
            'all_live_unexcused_missed_turns' => $allLiveUnexcusedMissedTurns,
            'past_month_live_missed_turns' => $recentLiveUnexcusedMissedTurns,
            'yearly_unexcused_missed_turns' => $user->missedTurns()->unexcused()->lastYear()->count(),
            'recent_unexcused_missed_turns' => $recentUnexcusedMissedTurns,
            'recent_live_unexcused_missed_turns' => $recentLiveUnexcusedMissedTurns,
            'reliability_rating' => $reliabilityRating,
            'yearly_phase_count' => $userPhaseCount,
            'yearly_missed_turns' => $yearlyMissedTurns,
            'all_missed_turns' => $allMissedTurns,
            'base_percentage' => $basePercentage,
            'yearly_penalty' => $liveLongPenalty,
            'recent_penalty' => $liveShortPenalty,
        ];
    }
}