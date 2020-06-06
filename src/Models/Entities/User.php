<?php

namespace Diplomacy\Models\Entities;

use Diplomacy\Models\Entities\Users\TemporaryBan;

class User
{
    const ROLE_USER = 'User';
    const ROLE_GUEST = 'Guest';
    const ROLE_DONATOR_BRONZE = 'DonatorBronze';
    const ROLE_DONATOR_SILVER = 'DonatorSilver';
    const ROLE_DONATOR_GOLD = 'DonatorGold';
    const ROLE_DONATOR_PLATINUM = 'DonatorPlatinum';
    const ROLE_MODERATOR = 'Moderator';
    const ROLE_SENIOR_MODERATOR = 'SeniorMod';
    const ROLE_ADMIN = 'Admin';
    const ROLE_SYSTEM = 'System';
    const ROLE_BANNED = 'Banned';

    public $id;
    public $username;
    public $email;
    public $points;
    public $comment;
    public $homepage;
    public $locale;
    /** @var TemporaryBan $temporaryBan */
    public $temporaryBan;
    /** @var double $reliabilityRating */
    public $reliabilityRating;
    public $roles = [];
    /* some more fields and i'll get to them */

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return in_array(static::ROLE_MODERATOR, $this->roles) ||
               in_array(static::ROLE_SENIOR_MODERATOR, $this->roles) ||
               in_array(static::ROLE_ADMIN, $this->roles);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array(static::ROLE_ADMIN, $this->roles);
    }
}

