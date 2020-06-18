<?php

namespace Diplomacy\Models;

use Diplomacy\Models\Entities\Users\Counts;
use Diplomacy\Models\Entities\Users\TemporaryBan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @package Diplomacy\Models
 * @property int id
 * @property string username
 * @property string email
 * @property int points
 * @property string comment
 * @property string homepage
 * @property string hideEmail
 * @property int timeJoined
 * @property string locale
 * @property int timeLastSessionEnded
 * @property int lastMessageIDViewed
 * @property string password
 * @property string type
 * @property string notifications
 * @property float ChanceEngland
 * @property float ChanceFrance
 * @property float ChanceItaly
 * @property float ChanceGermany
 * @property float ChanceAustria
 * @property float ChanceRussia
 * @property float ChanceTurkey
 * @property string muteReports
 * @property int silenceID
 * @property int cdCount
 * @property int nmrCount
 * @property int cdTakenCount
 * @property int phaseCount
 * @property int gameCount
 * @property double reliabilityRating
 * @property int deletedCDs
 * @property int tempBan
 * @property int emergencyPauseDate
 * @property int yearlyPhaseCount
 * @property string tempBanReason
 */
class User extends EloquentBase
{
    protected $table = 'wD_Users';
    protected $hidden = ['password'];

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return HasMany
     */
    public function missedTurns(): HasMany
    {
        return $this->hasMany(MissedTurn::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function watchedGames(): HasMany
    {
        return $this->hasMany(WatchedGame::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(UserOption::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function connections(): HasMany
    {
        return $this->hasMany(UserConnection::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function turnDates(): HasMany
    {
        return $this->hasMany(TurnDate::class, 'userID');
    }

    /**
     * @return HasMany
     */
    public function mutedCountries(): HasMany
    {
        return $this->hasMany(MutedCountry::class, 'userID');
    }

    /**
     * @param int $gameId
     * @return HasMany
     */
    public function mutedCountriesForGame(int $gameId): HasMany
    {
        return $this->mutedCountries()->where(MutedCountry::getTableName().'.gameID', '=', $gameId);
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param string $username
     * @return Builder
     */
    public function scopeWithUsername(Builder $query, string $username): Builder
    {
        return $query->where('username', '=', $username);
    }

    /**
     * @param Builder $query
     * @param string $email
     * @return Builder
     */
    public function scopeWithEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', '=', $email);
    }

    /*****************************************************************************************************************
     * QUERY METHODS
     ****************************************************************************************************************/

    /**
     * Get all status counts for the user
     * @return Builder
     */
    public function statusCountsForUser(): Builder
    {
        $membersTable = Member::getTableName();
        $query = Member::query();
        return $query->select([
            $query->raw('COUNT('.$membersTable.'.id) AS total'),
            'status',
            $query->raw('SUM('.$membersTable.'.bet) AS bet'),
        ])->forUser($this->id)->groupBy('status');
    }

    /**
     * @return int
     */
    public function queryWorth(): int
    {
        return $this->points + Member::forUser($this->id)->sum('bet');
    }

    /**
     * @return int
     */
    public function queryRanking(): int
    {
        return static::where('points', '>', $this->points)->count();
    }

    /**
     * @return int
     */
    public function queryActiveRanking(): int
    {
        $sixMonthsAgo = time() - 15552000;
        return static::where('points', '>', $this->points)->where('timeLastSessionEnded', '>', $sixMonthsAgo)->count() + 1;
    }

    /**
     * @return Builder
     */
    public function queryAnonymousGameCounts(): Builder
    {
        $membersTable = Member::getTableName();
        $gamesTable = Game::getTableName();
        $query = Member::query();
        return $query->select([
            $query->raw('COUNT('.$membersTable.'.id) AS total'),
            $membersTable.'.status',
            $query->raw('SUM('.$membersTable.'.bet) AS bet'),
        ])
            ->join($gamesTable, $membersTable.'.gameID','=',$gamesTable.'.id')
            ->forUser($this->id)
            ->where($gamesTable.'.phase', '!=', 'Finished')
            ->where($gamesTable.'.anon', '=', 'Yes')
            ->groupBy($membersTable.'status');
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

    /**
     * @return string
     */
    public function generateSessionKey()
    {
        return $this->id . '_' . md5(md5(\Config::$secret) . $this->id . $this->getPasswordHash() . sha1(\Config::$secret));
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return bin2hex($this->password);
    }

    /**
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return md5(\Config::$salt . md5($password));
    }

    /**
     * @param string $password
     * @return bool
     */
    public function passwordMatches(string $password): bool
    {
        $actualPasswordHash = $this->getPasswordHash();
        $attemptedPasswordHash = static::hashPassword($password);
        return 0 == strcasecmp($actualPasswordHash, $attemptedPasswordHash);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function setPassword(string $password) : bool
    {
        $this->password = hex2bin(static::hashPassword($password));
        return true;
    }

    /**
     * @return bool
     */
    public function canDoEmergencyPauses(): bool
    {
        return $this->emergencyPauseDate != 1;
    }

    /**
     * @return string
     */
    public function forgotPasswordToken(): string
    {
        return base64_encode(substr(md5(\Config::$secret . $this->email), 0, 8) . '%7C' . time() . '%7C' . urlencode($this->email));
    }

    /**
     * @return Entities\User
     */
    public function toEntity() : \Diplomacy\Models\Entities\User
    {
        $entity = new \Diplomacy\Models\Entities\User();
        $entity->id = (int)$this->id;
        $entity->username = (string)$this->username;
        $entity->email = (string)$this->email;
        if ($this->tempBan > 0) {
            $entity->temporaryBan = new TemporaryBan((int)$this->tempBan, (string)$this->tempBanReason);
        }
        $entity->reliabilityRating = doubleval($this->reliabilityRating);
        $entity->roles = explode(',', $this->type);
        $entity->points = (int)$this->points;
        $entity->timeLastSessionEnded = (int)$this->timeLastSessionEnded;
        $entity->counts = new Counts(
            $this->cdCount,
            $this->cdTakenCount,
            $this->deletedCDs,
            $this->nmrCount,
            $this->phaseCount,
            $this->yearlyPhaseCount,
            $this->gameCount
        );
        return $entity;
    }
}