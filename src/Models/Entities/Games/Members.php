<?php
namespace Diplomacy\Models\Entities\Games;

class Members extends \ArrayObject
{
    public function isReadyForProcessing(): bool
    {
        /** @var Member $member */
        foreach ($this as $member) {
            if (!$member->ordersState->readyForProcessing()) return false;
        }
        return true;
    }
}