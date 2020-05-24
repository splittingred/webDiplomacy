<?php

namespace Diplomacy\Services\Users;

use Diplomacy\Models\User;

class ReliabilityService
{
    public function forUser(User $user)
    {
        return [
            'all_live_unexcused_missed_turns' => $user->liveUnexcusedMissedTurns()->count(),
            'all_unexcused_missed_turns' => '',
            'recent_unexcused_missed_turns' => '',
            'recent_live_unexcused_missed_turns' => '',
            'reliability_rating' => '',
            'yearly_phase_count' => '',
            'missed_turns' => '',
            'live_missed_turns' => '',
            'all_missed_turns' => '',
            'base_percentage' => '',
        ];
    }
}