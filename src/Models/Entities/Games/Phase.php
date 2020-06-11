<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * Value object for a phase in a game
 *
 * @package Diplomacy\Models\Entities\Games
 */
class Phase
{
    const TYPE_FINISHED = 'finished';
    const TYPE_PRE_GAME = 'pre-game';
    const TYPE_MOVES = 'diplomacy';
    const TYPE_RETREATS = 'retreats';
    const TYPE_BUILDS = 'builds';

    const LIVE_THRESHOLD_MINUTES = 60;

    public $name;
    public $minutes;
    public $nextSwitchPeriod;
    /** @var string $type */
    protected $type;

    /**
     * @param string $name 'Finished','Pre-game','Diplomacy','Retreats','Builds'
     * @param int $minutes
     * @param int $nextSwitchPeriod
     */
    public function __construct(string $name, int $minutes, int $nextSwitchPeriod)
    {
        $this->name = $name;
        $this->type = strtolower($name);
        $this->minutes = $minutes;
        $this->nextSwitchPeriod = $nextSwitchPeriod;
    }

    /**
     * @return int
     */
    public function getHours(): int
    {
        return $this->minutes * 60;
    }

    /**
     * @return string
     */
    public function getLengthAsText(): string
    {
        return \libTime::timeLengthText($this->getHours());
    }

    /**
     * Is press allowed during this phase?
     * In standard games, only allowed during normal phases or if game is finished
     *
     * @return bool
     */
    public function isPressAllowed(): bool
    {
        return $this->isActive() || $this->isFinished();
    }

    /**
     * Is the game active? Meaning in normal moves/builds/retreat phases - so not pre-game or finished.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isMoves() || $this->isBuilds() || $this->isRetreats();
    }

    /**
     * Check whether this game will be considered a "live" game.
     * @return true if phase minutes are less than 60.
     */
    public function isLive(): bool
    {
        return $this->minutes < static::LIVE_THRESHOLD_MINUTES;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->type == static::TYPE_FINISHED;
    }

    /**
     * @return bool
     */
    public function isPreGame(): bool
    {
        return $this->type == static::TYPE_PRE_GAME;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return !$this->isPreGame();
    }

    /**
     * @return bool
     */
    public function isMoves(): bool
    {
        return $this->type == static::TYPE_MOVES;
    }

    /**
     * @return bool
     */
    public function isRetreats(): bool
    {
        return $this->type == static::TYPE_RETREATS;
    }

    /**
     * @return bool
     */
    public function isBuilds(): bool
    {
        return $this->type == static::TYPE_BUILDS;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}