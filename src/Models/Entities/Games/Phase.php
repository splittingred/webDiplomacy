<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * Value object for a phase in a game
 *
 * @package Diplomacy\Models\Entities\Games
 */
class Phase
{
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
        return $this->minutes < 60;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->type == 'finished';
    }

    /**
     * @return bool
     */
    public function isPreGame(): bool
    {
        return $this->type == 'pre-game';
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
        return $this->type == 'diplomacy';
    }

    /**
     * @return bool
     */
    public function isRetreats(): bool
    {
        return $this->type == 'retreats';
    }

    /**
     * @return bool
     */
    public function isBuilds(): bool
    {
        return $this->type == 'builds';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}