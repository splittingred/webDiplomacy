<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status;
use Diplomacy\Models\Entities\User;
use Diplomacy\Models\Entities\Users\MutedCountry;

/**
 * Represents a member placement that is unassigned (not yet filled in a game)
 *
 * @package Diplomacy\Models\Entities\Games
 */
class UnassignedMember extends Member
{
    public function __construct()
    {
        $this->country = new Country(0, 'Unassigned');
        $this->user = new User();
        $this->user->id = User::GUEST_ID;
        $this->status = new Status(Status::STATUS_UNASSIGNED);
        $this->ordersState = new OrdersState([]);
        $this->isInGame = false;
    }

    /**
     * @param User $user
     * @return UnassignedMember
     */
    public static function buildFromUser(User $user): UnassignedMember
    {
        $m = new static();
        $m->user = $user;
        return $m;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isAssigned(): bool
    {
        return false;
    }


    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return false;
    }
}