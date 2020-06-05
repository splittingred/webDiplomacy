<?php

namespace Diplomacy\Models\Entities;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\DrawType;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Members;
use Diplomacy\Models\Entities\Games\MissingPlayerPolicy;
use Diplomacy\Models\Entities\Games\Phase;
use Diplomacy\Models\Entities\Games\PlayersType;
use Diplomacy\Models\Entities\Games\PotType;
use Diplomacy\Models\Entities\Games\PressType;
use Diplomacy\Models\Entities\Games\Processing;
use Diplomacy\Models\Entities\Games\Status;
use Diplomacy\Models\Entities\Games\Turn;
use Diplomacy\Models\User;

class Game
{
    /** @var int $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var int $minimumBet */
    public $minimumBet;
    /** @var string $password */
    public $password;
    /** @var bool $anonymous */
    public $anonymous;
    /** @var int $attempts */
    public $attempts;
    /** @var int $minimumReliabilityRating */
    public $minimumReliabilityRating;
    /** @var int $excusedMissedTurns */
    public $excusedMissedTurns;
    /** @var int $startTime */
    public $startTime;
    /** @var int $finishTime */
    public $finishTime;

    public $pauseTimeRemaining;

    /* groupings for value objects TODO! */
    /** @var Processing $processing */
    public $processing;
    /** @var MissingPlayerPolicy $missingPlayerPolicy */
    public $missingPlayerPolicy;
    /** @var Status $status */
    public $status;
    /** @var Turn $currentTurn */
    public $currentTurn;
    /** @var Members<Member> $members */
    public $members;
    /** @var array<Country> */
    public $countries = [];
    /** @var PlayersType $playersType */
    public $playersType;
    /** @var DrawType $drawType */
    public $drawType;
    /** @var PotType $potType */
    public $potType;
    /** @var PressType $pressType */
    public $pressType;
    /** @var \WDVariant $variant */
    public $variant;
    /** @var Phase $phase */
    public $phase;
    /** @var Phase $nextPhase */
    public $nextPhase;
    /** @var User $director */
    public $director;
    /** @var bool $featured */
    public $featured = false;

    public function __construct()
    {
        $this->members = new Members();
    }

    /**
     * @return int
     */
    public function getCountryCount(): int
    {
        return count($this->countries);
    }

    /**
     * @return int
     */
    public function getMemberCount(): int
    {
        return count($this->members);
    }

    /**
     * @return bool
     */
    public function allSlotsFilled(): bool
    {
        $totalPlayers = $this->getCountryCount();
        $memberTotal = $this->getMemberCount();
        return $totalPlayers == $memberTotal;
    }

    /**
     * @return Member|null
     */
    public function getWinner()
    {
        if (!$this->status->wasWon()) return null;

        /** @var Member $member */
        foreach ($this->members as $member) {
            if ($member->status->won()) return $member;
        }
        return null;
    }

    /**
     * @param int $turnNumber
     * @return string
     */
    public function getTurnAsText(int $turnNumber = -1) : string
    {
        if ($turnNumber == -1) $turnNumber = $this->currentTurn->id;

        return $this->variant->turnAsDate($turnNumber);
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return !empty($this->password);
    }

    /**
     * @return array
     */
    public function getVariantNames() : array
    {
        $alternatives = [];
        $alternatives[] = $this->variant->link();

        $pressType = $this->pressType->getLongName();
        if (!empty($pressType)) $alternatives[] = $pressType;

        $playerType = $this->playersType->getLongName();
        if (!empty($playerType)) $alternatives[] = $playerType;

        if ($this->anonymous) $alternatives[] = 'Anonymous players';

        $alternatives[] = $this->potType->getLongName();

        $drawType = $this->drawType->getLongName();
        if (!empty($drawType)) $alternatives[] = $drawType;

        if ($this->missingPlayerPolicy->isWait()) $alternatives[] = 'Wait for orders';

        return $alternatives;
    }
}
