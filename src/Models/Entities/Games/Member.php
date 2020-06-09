<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status;
use Diplomacy\Models\Entities\User;
use Diplomacy\Models\Entities\Users\MutedCountry;
use Diplomacy\Views\Components\Games\Members\BetWonComponent;
use Diplomacy\Views\Components\Games\Members\CountryNameComponent;
use Diplomacy\Views\Components\Games\Members\MemberNameComponent;
use Diplomacy\Views\Components\Games\Members\MuteIconComponent;
use Diplomacy\Views\Components\Games\Members\NameComponent;
use Diplomacy\Views\Components\Games\Members\ProgressBarComponent;
use Diplomacy\Views\Components\Games\Members\UnitCountComponent;

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
    /** @var array $newMessagesFrom */
    public $newMessagesFrom = [];
    /** @var int $supplyCenterCount */
    public $supplyCenterCount;
    /** @var int $unitCount */
    public $unitCount;
    /** @var array<string> $votes */
    public $votes = [];
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
    /** @var int $supplyCenterTarget The target number of SCs to win for this member */
    public $supplyCenterTarget;
    /** @var array $mutedCountries */
    public $mutedCountries = [];

    /** @var bool $isDirector */
    public $isDirector;
    /** @var bool $isTournamentDirector */
    public $isTournamentDirector;
    /** @var bool $isTournamentCoDirector */
    public $isTournamentCoDirector;

    /**
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->id > 0;
    }

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
    public function hasUnsubmittedOrders(): bool
    {
        return $this->status->isPlaying() && !$this->ordersState->submitted();
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->status->left() && $this->user->temporaryBan;
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
     * @return string
     */
    public function unitCountComponent(): string
    {
        return (string)(new UnitCountComponent($this));
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
     * @param int|User $user
     * @return bool
     */
    public function canBeSeenBy($user): bool
    {
        $userId = is_int($user) ? $user : $user->id;
        $isSelf = $userId != $this->user->id;
        return $isSelf || $this->isDirector || $this->isTournamentDirector || $this->isTournamentCoDirector;
    }

    /**
     * Get the fully rendered country name for this member, properly hiding it if the member is anonymous and
     * the viewing user cannot see anonymous members.
     *
     * @param Game $game
     * @param mixed $currentUser
     * @return string
     */
    public function getRenderedCountryName(Game $game, $currentUser = null): string
    {
        $currentUserId = is_int($currentUser) ? $currentUser : $currentUser->id;
        return (string)(new CountryNameComponent($game, $this, $currentUserId));
    }

    /**
     * @param Game $game
     * @param int $currentUserId
     * @return string
     */
    public function getRenderedName(Game $game, int $currentUserId = 0)
    {
        return (string)(new NameComponent($this, $game, $currentUserId));
    }


    /**
     * @param Game $game
     * @param mixed $currentUser
     * @return string
     */
    public function getMemberNameForGame(Game $game, $currentUser = null): string
    {
        $currentUserId = is_int($currentUser) ? $currentUser : $currentUser->id;
        return (string)(new MemberNameComponent($game, $this, $currentUserId));
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

        return \libHTML::unreadMessages('/games/'.$this->gameId.'/view&msgCountryID='.$this->country->id.'#chatbox');
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

    /**
     * @param Game $game
     * @return string
     */
    public function betWon(Game $game): string
    {
        return (string)(new BetWonComponent($this, $game));
    }

    /**
     * @return string
     */
    public function progressBar(): string
    {
        return (string)(new ProgressBarComponent($this));
    }

    public function muteIcon(Game $game, Member $currentMember = null): string
    {
        return (string)(new MuteIconComponent($game, $this, $currentMember));
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