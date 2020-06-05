<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status;
use Diplomacy\Models\Entities\User;

class Member
{
    /** @var int $id */
    public $id;
    /** @var User $user */
    public $user;
    /** @var int $gameId */
    public $gameId;
    /** @var Country $country */
    public $country;
    /** @var Status $status */
    public $status;
    /** @var int $timeLoggedIn */
    public $timeLoggedIn;
    /** @var int $bet */
    public $bet;
    /** @var string $missedPhases */
    public $missedPhases;
    /** @var string $newMessagesFrom */
    public $newMessagesFrom;
    /** @var int $supplyCenterCount */
    public $supplyCenterCount;
    /** @var int $unitCount */
    public $unitCount;
    /** @var array<string> $votes */
    public $votes;
    /** @var int $pointsWon */
    public $pointsWon;
    /** @var int $gameMessagesSent */
    public $gameMessagesSent;
    /** @var OrdersState $ordersState */
    public $ordersState;
    /** @var boolean $hideNotifications */
    public $hideNotifications;
    /** @var int $excusedMissedTurns */
    public $excusedMissedTurns;

    public function __construct()
    {

    }
}