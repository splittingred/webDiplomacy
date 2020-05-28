<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use panelGame;
use panelGameHome;
use WDVariant;

class Game extends EloquentBase
{
    protected $table = 'wD_Games';
    protected $hidden = [
        'password',
    ];
    /** @var WDVariant */
    protected $variant;
    /** @var panelGameHome */
    protected $homeGamePanel;
    /** @var panelGame */
    protected $gamePanel;

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /**
     * @return HasMany
     */
    public function members() : HasMany
    {
        return $this->hasMany(Member::class, 'gameID', 'id');
    }

    /**
     * @return HasMany
     */
    public function units() : HasMany
    {
        return $this->hasMany(Unit::class, 'gameID');
    }

    /**
     * @return HasMany
     */
    public function turnDates() : HasMany
    {
        return $this->hasMany(TurnDate::class, 'gameID');
    }

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinMembers(Builder $query) : Builder
    {
        $membersTable = Member::getTableName();
        return $query->join($membersTable, $membersTable . '.gameID', '=', static::getTableName() . '.id');
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWithUser(Builder $query, int $userId) : Builder
    {
        $membersTable = Member::getTableName();
        return $this->scopeJoinMembers($query)->where($membersTable . '.userID', '=', $userId);
    }

    /**
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeWithoutUser(Builder $query, int $userId) : Builder
    {
        $membersTable = Member::getTableName();
        $gamesTable = Game::getTableName();
        return $query
            ->leftJoin($membersTable , function($join) use ($gamesTable, $membersTable, $userId) {
                $join->where($membersTable . '.gameID', '=', Game::raw($gamesTable . '.id'))
                    ->where($membersTable . '.userID', '=', Game::raw($userId));
            })
            ->whereNull($membersTable . '.id');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinished(Builder $query) : Builder
    {
        return $query->where('phase', '=', 'Finished');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotFinished(Builder $query) : Builder
    {
        return $query->where('phase', '!=', 'Finished');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGameOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'Yes');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGameNotOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'No');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePreGame(Builder $query) : Builder
    {
        return $query->where('phase', '=' , 'Pre-Game');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotPreGame(Builder $query) : Builder
    {
        return $query->where('phase', '!=' , 'Pre-Game');
    }

    /**
     * Get all joinable games for a user, based on passed available points and RR
     *
     * @param Builder $query
     * @param int $userId
     * @param int $points
     * @param int $reliabilityRating
     * @return Builder
     */
    public function scopeJoinableForUser(Builder $query, int $userId, int $points, int $reliabilityRating) : Builder
    {
        return $this->scopeWithoutUser($query, $userId)
            ->whereNotNull('minimumBet')
            ->whereNull('password')
            ->gameNotOver()
            ->active()
            ->where('minimumBet', '<=', $points)
            ->where('minimumReliabilityRating', '<=', $reliabilityRating);
    }

    /**
     * Return all active games
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->notPreGame()->notFinished();
    }

    /**
     * Get all active games for a given user
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeActiveForUser(Builder $query, int $userId) : Builder
    {
        return $query->withUser($userId)->notFinished();
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

    /**
     * @param int $gameId
     * @param int $userId
     * @return bool
     */
    public static function isMember(int $gameId, int $userId) : bool
    {
        return Member::forGame($gameId)->forUser($userId)->exists();
    }

    /**
     * @return WDVariant
     */
    public function getVariant() : WDVariant
    {
        if (!$this->variant) $this->variant = \libVariant::loadFromVariantID($this->variantID);
        return $this->variant;
    }

    /**
     * @return panelGameHome
     */
    public function getHomeGamePanel() : panelGameHome
    {
        if (!$this->homeGamePanel) $this->homeGamePanel = $this->getVariant()->panelGameHome($this->toArray());
        return $this->homeGamePanel;
    }

    /**
     * Get the home panel summary
     *
     * @return string
     */
    public function getHomeSummary() : string
    {
        return $this->getHomeGamePanel()->summary();
    }

    /**
     * Get the main game panel
     *
     * @return panelGame
     */
    public function getGamePanel() : panelGame
    {
        if (!$this->gamePanel) $this->gamePanel = $this->getVariant()->panelGame($this->toArray());
        return $this->gamePanel;
    }

    /**
     * Get the main summary
     *
     * @return string
     */
    public function getSummary() : string
    {
        return $this->getGamePanel()->summary();
    }

    /**
     * @return bool
     */
    public function isPaused() : bool
    {
        return $this->processStatus == 'Paused';
    }
    /**
     * @return bool
     */
    public function isPreGame() : bool
    {
        return $this->phase == 'Pre-game';
    }

    /**
     * Is the game crashed?
     *
     * @return bool
     */
    public function isCrashed() : bool
    {
        return $this->processStatus == 'Crashed';
    }


    /**
     * @return bool
     */
    public function isProcessing() : bool
    {
        return $this->processStatus == 'Processing';
    }

    /**
     * @return bool
     */
    public function isInNotProcessing() : bool
    {
        return $this->processStatus == 'Not-processing';
    }

    /**
     * @return bool
     */
    public function isStarted() : bool
    {
        return !$this->isPreGame();
    }

    /**
     * @return bool
     */
    public function isInProgress() : bool
    {
        return $this->isStarted() && !$this->isFinished();
    }

    /**
     * @return bool
     */
    public function isFinished() : bool
    {
        return $this->phase == 'Finished';
    }
}