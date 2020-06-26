<?php

namespace Diplomacy\Models\Entities;

use Diplomacy\Models\Entities\Users\Counts;
use Diplomacy\Models\Entities\Users\TemporaryBan;

class User
{
    const GUEST_ID = 1;
    const ROLE_USER = 'User';
    const ROLE_GUEST = 'Guest';
    const ROLE_DONATOR_BRONZE = 'DonatorBronze';
    const ROLE_DONATOR_SILVER = 'DonatorSilver';
    const ROLE_DONATOR_GOLD = 'DonatorGold';
    const ROLE_DONATOR_PLATINUM = 'DonatorPlatinum';
    const ROLE_MODERATOR = 'Moderator';
    const ROLE_FORUM_MODERATOR = 'ForumModerator';
    const ROLE_SENIOR_MODERATOR = 'SeniorMod';
    const ROLE_ADMIN = 'Admin';
    const ROLE_SYSTEM = 'System';
    const ROLE_BANNED = 'Banned';
    const ROLE_BOT = 'Bot';

    public int $id;
    public string $username;
    public string $email;
    public int $points;
    public string $comment;
    public string $homepage;
    public string $locale;
    public ?TemporaryBan $temporaryBan;
    public float $reliabilityRating;
    public array $roles = [];
    public int $timeLastSessionEnded;
    public Counts $counts;
    /* some more fields and i'll get to them */

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return !empty($this->id) && $this->id != static::GUEST_ID;
    }

    /**
     * @param string|array $desiredRoles
     * @return bool
     */
    public function hasRole($desiredRoles): bool
    {
        if (!is_array($desiredRoles)) $desiredRoles = [$desiredRoles];
        return count(array_intersect($desiredRoles, $this->roles)) > 0;
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->hasRole([static::ROLE_MODERATOR, static::ROLE_SENIOR_MODERATOR, static::ROLE_ADMIN]);
    }

    /**
     * @return bool
     */
    public function isSeniorModerator(): bool
    {
        return $this->hasRole([static::ROLE_SENIOR_MODERATOR, static::ROLE_ADMIN]);
    }

    /**
     * @return bool
     */
    public function isForumModerator(): bool
    {
        return $this->hasRole([static::ROLE_FORUM_MODERATOR, static::ROLE_ADMIN]);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(static::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isBot(): bool
    {
        return $this->hasRole(static::ROLE_BOT);
    }

    /**
     * @return bool
     */
    public function isDonator(): bool
    {
        return $this->hasRole([static::ROLE_DONATOR_BRONZE, static::ROLE_DONATOR_SILVER, static::ROLE_DONATOR_GOLD, static::ROLE_DONATOR_PLATINUM]);
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->hasRole(static::ROLE_BANNED);
    }

    /**
     * @return string
     */
    public function roleIcons(): string
    {
        $buf = '';
        if ($this->isModerator()) {
            $buf .= '<img src="/images/icons/mod.png" alt="Mod" title="Moderator/Admin" />';
        } elseif ($this->isBanned()) {
            $buf .= '<img src="/images/icons/cross.png" alt="X" title="Banned" />';
        }

        if ($this->hasRole(static::ROLE_DONATOR_PLATINUM))
            $buf .= \libHTML::platinum();
        elseif ($this->hasRole(static::ROLE_DONATOR_GOLD))
            $buf .= \libHTML::gold();
        elseif ($this->hasRole(static::ROLE_DONATOR_SILVER))
            $buf .= \libHTML::silver();
        elseif ($this->hasRole(static::ROLE_DONATOR_BRONZE))
            $buf .= \libHTML::bronze();

        return $buf;
    }
}

