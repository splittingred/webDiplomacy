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
        foreach ($this as $member) {
            if ($member->user->id == $userId) return true;
        }
        return false;
    }

    /**
     * Get a Member for a given User ID
     *
     * @param int $userId
     * @return Member|null
     */
    public function byUserId(int $userId)
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if ($member->user->id == $userId) return $member;
        }
        return null;
    }

    /**
     * Get a Member for a given status
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
     * Get a member for a given country ID
     *
     * @param int $countryId
     * @return Member|UnassignedMember
     */
    public function byCountryId(int $countryId)
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if ($member->country->id == $countryId) return $member;
        }
        return new UnassignedMember();
    }

    /**
     * Determine if a given user is participating, but banned for this game
     *
     * @param int $userId
     * @return bool
     */
    public function isUserBanned(int $userId): bool
    {
        $member = $this->byUserId($userId);
        if (empty($member)) return false;

        return $member->isBanned();
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
            if (!empty($status)) {
                if ((string)$member->status == $status) $count += $member->supplyCenterCount;
            } else {
                $count += $member->supplyCenterCount;
            }
        }
        return $count;
    }
}