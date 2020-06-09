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
use Diplomacy\Models\Entities\User;
use Diplomacy\Models\Entities\Tournament;

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
    /** @var Tournament $tournament */
    public $tournament;

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
     * Is the given user a Director for this game?
     *
     * @param int|User $user
     * @return bool
     */
    public function isDirector($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        return $this->director && $this->director->id == $userId;
    }

    /**
     * Is the given user a Tournament Director for this game?
     *
     * @param int|User $user
     * @return bool
     */
    public function isTournamentDirector($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        return $this->tournament && $this->tournament->director->id == $userId;
    }

    /**
     * Is the given user a Tournament Co-Director for this game?
     *
     * @param int|User $user
     * @return bool
     */
    public function isTournamentCoDirector($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;

        return $this->tournament && $this->tournament->coDirector->id == $userId;
    }

    /**
     * Should member names be hidden by default?
     *
     * @return bool
     */
    public function areMemberNamesHidden(): bool
    {
        return $this->anonymous && !$this->phase->isFinished();
    }

    /**
     * @param Member $member
     * @param Member $currentMember
     * @return bool
     */
    public function isMemberNameHidden(Member $member, Member $currentMember)
    {
        return $this->areMemberNamesHidden() && !$member->canBeSeenBy($currentMember);
    }


    /**************************************************
     * OLDER METHODS
     **************************************************/

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

    /**
     * @return array
     */
    public function supplyCenterPercentages(): array
    {
        $percentages = [];

        $totalSCs = $this->members->supplyCenterCount();
        $countryCount = $this->getCountryCount();

        for ($countryID = 1; $countryID <= $countryCount; $countryID++) {
            if (empty($totalSCs)) // We must be pre-game
            {
                $percentages[$countryID] = round((3 / (3 * 6 + 4)) * 100);
            } else {
                $member = $this->members->byCountryId($countryID);
                if ($member) $percentages[$countryID] = round(($member->supplyCenterCount / $totalSCs) * 100);
            }
        }

        $sum = 0;
        foreach ($percentages as $countryID => $percent) {
            $sum += $percentages[$countryID];
        }

        // Add the rounding error onto a countryID with a few SCs, where it won't be noticed
        foreach ($percentages as $countryID => $percent) {
            if ($percent > (1 / 8 * 100)) {
                $percentages[$countryID] += 100 - $sum;
                break;
            }
        }

        return $percentages;
    }
}
