<?php
namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\User;

class Members extends \ArrayObject
{
    /**
     * @return bool
     */
    public function isReadyForProcessing(): bool
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if (!$member->ordersState->readyForProcessing()) return false;
        }
        return true;
    }

    /**
     * @param int|User|\User $user
     * @return bool
     */
    public function isUserInGame($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;
        /** @var Member $member */
        foreach ($this as $member) {
            if ($member->user->id == $userId && $member->isInGame) return true;
        }
        return false;
    }

    /**
     * Get a Member for a given User
     *
     * @param User $user
     * @return Member|UnassignedMember
     */
    public function byUser(User $user)
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if ($member->isUser($user)) return $member;
        }
        return UnassignedMember::buildFromUser($user);
    }

    /**
     * Get Members for a given status
     *
     * @param string $status
     * @return array<Member>
     */
    public function allWithStatus(string $status): array
    {
        $members = [];
        /** @var Member $member */
        foreach ($this as $member) {
            if ((string)$member->status == $status) $members[] = $member;
        }
        return $members;
    }

    /**
     * @return int
     */
    public function totalMembersLeftInGame(): int
    {
        return count($this->allWithStatus(Status::STATUS_ACTIVE));
    }

    /**
     * @return int
     */
    public function totalInGame(): int
    {
        if (empty($this->totalInGame)) {
            $this->totalInGame = 0;

            /** @var Member $member */
            foreach ($this as $member) {
                if ($member->isInGame) $this->totalInGame += 1;
            }
        }
        return $this->totalInGame;
    }

    /**
     * Get a member for a given country ID
     *
     * @param int $countryId
     * @return Member|UnassignedMember
     */
    public function byCountryId(int $countryId)
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if (!$member->isInGame) continue;

            if ($member->country->id == $countryId) return $member;
        }
        return new UnassignedMember();
    }

    /**
     * Determine if a given user is participating, but banned for this game
     *
     * @param User $user
     * @return bool
     */
    public function isUserBanned($user): bool
    {
        return $this->byUser($user)->isBanned();
    }

    /**
     * @return array<Member>
     */
    public function getWithUnsubmittedOrders(): array
    {
        $result = [];
        /** @var Member $member */
        foreach ($this as $member) {
            if ($member->hasUnsubmittedOrders()) $result[] = $member;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function allOrdersAreEntered(): bool
    {
        return count($this->getWithUnsubmittedOrders()) <= 0;
    }

    /**
     * @param string $status
     * @return int
     */
    public function supplyCenterCount(string $status = '') : int
    {
        $count = 0;
        $status = strtolower($status);

        /** @var Member $member */
        foreach ($this as $member) {
            if (!$member->isInGame) continue; // ignore "members" not in-game

            if (!empty($status)) {
                if ((string)$member->status == $status) $count += $member->supplyCenterCount;
            } else {
                $count += $member->supplyCenterCount;
            }
        }
        return $count;
    }
}