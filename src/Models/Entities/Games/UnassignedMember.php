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
    /** @var int $id */
    public $id = 0;

    public function __construct()
    {
        $this->country = new Country(0, 'Global');
    }
}