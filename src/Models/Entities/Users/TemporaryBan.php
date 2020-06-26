<?php

namespace Diplomacy\Models\Entities\Users;

/**
 * Represents a temporary ban of a user until a specific time, with a given reason
 *
 * @package Diplomacy\Models\Entities\Users
 */
class TemporaryBan
{
    public int $until;
    public string $reason;

    /**
     * @param int $until
     * @param string $reason
     */
    public function __construct(int $until, string $reason)
    {
        $this->until = $until;
        $this->reason = $reason;
    }
}

