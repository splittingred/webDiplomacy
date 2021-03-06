<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status;
use Diplomacy\Models\Entities\User;
use Diplomacy\Models\Entities\Users\MutedCountry;
use Diplomacy\Views\Components\Games\Members\CountryNameComponent;
use Diplomacy\Views\Components\Games\Members\MemberNameComponent;
use Diplomacy\Views\Components\Games\Members\NameComponent;

class Member
{
    public int $id = 0;
    public User $user;
    public int $gameId = 0;
    public Country $country;
    public Status $status;
    public int $timeLoggedIn = 0;
    public int $bet = 0;
    public int $missedPhases = 0;
    public array $newMessagesFrom = [];
    public int $supplyCenterCount = 0;
    public int $unitCount = 0;
    public array $votes = [];
    public int $pointsWon = 0;
    public int $gameMessagesSent = 0;
    public OrdersState $ordersState;
    public bool $hideNotifications = false;
    public int $excusedMissedTurns = 0;
    public int $supplyCenterTarget = 0;
    public array $mutedCountries = [];
    public bool $isDirector = false;
    public bool $isTournamentDirector = false;
    public bool $isTournamentCoDirector = false;
    public bool $isInGame = true;

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->user->isAuthenticated();
    }

    /**
     * @return bool
     */
    public function isAssigned(): bool
    {
        return $this->country->id > 0;
    }

    /**
     * @return bool
     */
    public function hasUnsubmittedOrders(): bool
    {
        return $this->status->isPlaying() && !$this->ordersState->submitted();
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->status->hasLeft() && $this->user->temporaryBan;
    }

    /**
     * @param int|Country $country
     * @return bool
     */
    public function isCountry($country): bool
    {
        $countryId = is_int($country) ? $country : $country->id;
        return $this->country->id == $countryId;
    }

    /**
     * @param int|Country $country
     * @return bool
     */
    public function hasMutedCountry($country): bool
    {
        $countryId = is_a($country, Country::class) ? $country->id : $country;

        /** @var MutedCountry $mutedCountry */
        foreach ($this->mutedCountries as $mutedCountry) {
            if ($mutedCountry->countryId == $countryId) return true;
        }
        return false;
    }

    /**
     * Does this member have equal Units to SCs?
     *
     * @return bool
     */
    public function unitsEqualToSupplyCenters(): bool
    {
        return $this->unitCount == $this->supplyCenterCount;
    }

    /**
     * Does this member have more SCs than units?
     *
     * @return bool
     */
    public function hasUnitDeficit(): bool
    {
        return $this->unitCount < $this->supplyCenterCount;
    }

    /**
     * Does this member have more units than SCs?
     *
     * @return bool
     */
    public function hasUnitSurplus(): bool
    {
        return $this->unitCount > $this->supplyCenterCount;
    }

    /**
     * Does the member have no SCs or Units left?
     *
     * @return bool
     */
    public function hasNoPieces(): bool
    {
        return $this->piecesCount() == 0;
    }

    /**
     * @return int
     */
    public function piecesCount(): int
    {
        return $this->unitCount + $this->supplyCenterCount;
    }

    /**
     * @return bool
     */
    public function hasNewMessages(): bool
    {
        return count($this->newMessagesFrom) > 0;
    }

    /**
     * @return string
     */
    public function unreadMessagesLink(): string
    {
        if (count($this->newMessagesFrom) == 1 && in_array('0', $this->newMessagesFrom)) {
            return \libHTML::maybeReadMessages('board.php?gameID=' . $this->gameId . '#chatbox') . ' - Unread global messages';
        } else {
            return \libHTML::unreadMessages('board.php?gameID=' . $this->gameId . '#chatbox') . ' - Unread messages';
        }
    }

    /**
     * Can this member be seen on game lists by the specified User, even if it's an anonymous game?
     *
     * @param Member $currentMember
     * @return bool
     */
    public function canBeSeenBy($currentMember): bool
    {
        $isSelf = $currentMember->isUser($this->user);
        return $isSelf || $currentMember->isDirector || $currentMember->isTournamentDirector || $currentMember->isTournamentCoDirector;
    }

    /**
     * Get the fully rendered country name for this member, properly hiding it if the member is anonymous and
     * the viewing member cannot see anonymous members.
     *
     * @param Game $game
     * @param Member $currentMember
     * @return string
     */
    public function getRenderedCountryName(Game $game, Member $currentMember): string
    {
        return (string)(new CountryNameComponent($game, $this, $currentMember));
    }

    /**
     * @param Game $game
     * @param Member $currentMember
     * @return string
     */
    public function getRenderedName(Game $game, Member $currentMember)
    {
        return (string)(new NameComponent($this, $game, $currentMember));
    }


    /**
     * @param Game $game
     * @param Member $currentMember
     * @return string
     */
    public function getMemberNameForGame(Game $game, Member $currentMember): string
    {
        return (string)(new MemberNameComponent($game, $this, $currentMember));
    }

    /**
     * @param int|User|\User $user
     * @return string
     */
    public function messagesFromLink($user): string
    {
        if ($this->isBanned()) return '';
        $userId = is_int($user) ? $user : $user->id;

        if (!$this->hasNewMessageFrom($userId)) return '';

        return \libHTML::unreadMessages('/games/'.$this->gameId.'/view?countryId='.$this->country->id.'#chatbox');
    }

    /**
     * @param int|User $user
     * @return boolean
     */
    public function hasNewMessageFrom($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;
        return in_array($userId, $this->newMessagesFrom);
    }

    /**
     * @param int|User|\User $user
     * @return bool
     */
    public function isUser($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;
        return $this->user->id == $userId;
    }

    /**
     * A textual display of this user's last log-in time
     * @return string Last log-in time
     */
    public function lastLoggedInAsText(): string
    {
        return \libTime::timeLengthText(time() - $this->timeLoggedIn).' ('.\libTime::text($this->timeLoggedIn).')';
    }

    /**********
     * DEPRECATED METHODS
     **********/

    public function getCountryNameColored(): string
    {
        $buf = '';
        if($this->country->id != 'Unassigned' )
            $buf .= '<span class="memberStatus'.$this->status.'">';

        if ( $this->isNameHidden() )
            $buf .= '<span class="country'.$this->country->id.'">'.l_t($this->country).'</span>';
        else
            $buf .= '<a class="country'.$this->country->id.'" href="profile.php?userID='.$this->user->id.'">'.$this->user->username.'</a>';

        $buf .= '</span>';

        return $buf;
    }
}