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
    public int $id;
    public string $name;
    public int $minimumBet;
    public string $password;
    public bool $anonymous;
    public int $attempts;
    public int $minimumReliabilityRating;
    public int $excusedMissedTurns;

    public int $startTime;
    public int $finishTime;
    public int $pauseTimeRemaining;

    public Processing $processing;
    public MissingPlayerPolicy $missingPlayerPolicy;
    public Status $status;
    public ?Turn $currentTurn;
    /** @var Members<Member> $members */
    public Members $members;
    /** @var array<Country> */
    public array $countries = [];
    public PlayersType $playersType;
    public DrawType $drawType;
    public PotType $potType;
    public PressType $pressType;
    public \WDVariant $variant;
    public Phase $phase;
    public Phase $nextPhase;
    public ?User $director;
    public bool $featured = false;
    public ?Tournament $tournament;

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
        return $this->members->totalInGame();
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
            if ($member->status->hasWon()) return $member;
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
     * Does the given user have director-level privileges to this game?
     *
     * This is different than isDirector, as this also checks tournament status
     *
     * @param User $user
     * @return bool
     */
    public function hasDirectorAccess(User $user): bool
    {
        return $user->isAdmin() || $this->isDirector($user) || $this->isTournamentDirector($user) || $this->isTournamentCoDirector($user);
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

    /**
     * Process the game through the variant mechanism
     */
    public function process()
    {
        return $this->variant->processGame($this->id);
    }
}
