<?php

namespace Diplomacy\Models\Entities\Users;

class TemporaryBan
{
    public $until;
    public $reason;

    public function __construct(int $until, string $reason)
    {
        $this->until = $until;
        $this->reason = $reason;
    }
}

