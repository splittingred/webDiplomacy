<?php

namespace Diplomacy\Models\Entities;

use Diplomacy\Models\Entities\Games\DrawType;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PlayersType;
use Diplomacy\Models\Entities\Games\PotType;
use Diplomacy\Models\Entities\Games\PressType;
use Diplomacy\Models\Entities\Games\Status;
use Diplomacy\Models\Entities\Games\Turn;
use Diplomacy\Models\User;

class Game
{
    /** @var int $id */
    public $id;
    /** @var string $name */
    public $name;
    public $processTime;
    public $processStatus;
    public $minimumBet;
    public $password;
    public $pauseTimeRemaining;
    /** @var bool $anonymous */
    public $anonymous;
    /** @var int $attempts */
    public $attempts;
    public $missingPlayerPolicy;
    public $minimumReliabilityRating;
    public $excusedMissedTurns;
    public $startTime;
    public $finishTime;

    /* groupings for value objects TODO! */
    /** @var Status */
    public $status;
    /** @var Turn $currentTurn */
    public $currentTurn;
    /** @var array<Member> $members */
    public $members = [];
    /** @var PlayersType $playersType */
    public $playersType;
    /** @var DrawType $drawType */
    public $drawType;
    /** @var PotType $potType */
    public $potType;
    /** @var PressType $pressType */
    public $pressType;
    /** @var Variant $variant */
    public $variant;
    /** @var Phase $phase */
    public $phase;
    /** @var Phase $nextPhase */
    public $nextPhase;
    /** @var User $director */
    public $director;

    public function __construct()
    {

    }
}
