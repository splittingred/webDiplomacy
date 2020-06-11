<?php

namespace Diplomacy\Models\Entities\Games\Members;

/**
 * @package Diplomacy\Models\Entities\Games\Members
 */
class Status
{
    const STATUS_PLAYING = 'playing'; // still in the game
    const STATUS_DEFEATED = 'defeated'; // lost, out of SCs
    const STATUS_LEFT = 'left'; // quit the game, usually due to NMRs
    const STATUS_WON = 'won'; // won the game!
    const STATUS_DRAWN = 'drawn'; // drew the game with others
    const STATUS_SURVIVED = 'survived'; // had SCs at the end but did not win or draw
    const STATUS_RESIGNED = 'resigned'; // surrendered the game
    const STATUS_UNASSIGNED = 'unassigned';

    /** @var string */
    public $type;

    /**
     * @param string $type
     */
    public function __construct(string $type = self::STATUS_PLAYING)
    {
        $this->type = strtolower($type);
    }

    /**
     * Return this status as a textual representation
     *
     * @return string
     */
    public function text(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get the CSS class that highlights the name appropriately
     *
     * @return string
     */
    public function cssClass(): string
    {
        return 'memberStatus' . ucfirst($this->type);
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return !$this->isDead() && !$this->isUnassigned();
    }

    /**
     * @return bool
     */
    public function isUnassigned(): bool
    {
        return $this->type == self::STATUS_UNASSIGNED;
    }

    /**
     * @return bool
     */
    public function isDead(): bool
    {
        return in_array($this->type, [self::STATUS_LEFT, self::STATUS_RESIGNED, self::STATUS_DEFEATED]);
    }

    /**
     * @return bool
     */
    public function isPlaying(): bool
    {
        return $this->type == self::STATUS_PLAYING;
    }

    /**
     * @return bool
     */
    public function hasWon(): bool
    {
        return $this->type == self::STATUS_WON;
    }

    /**
     * Whether the member "lost", which is to be defeated or resign
     *
     * @return bool
     */
    public function hasLost(): bool
    {
        return $this->isDefeated() || $this->hasResigned();
    }

    /**
     * @return bool
     */
    public function isDefeated(): bool
    {
        return $this->type == self::STATUS_DEFEATED;
    }

    /**
     * @return bool
     */
    public function hasLeft(): bool
    {
        return $this->type == self::STATUS_LEFT;
    }

    /**
     * @return bool
     */
    public function hasDrawn(): bool
    {
        return $this->type == self::STATUS_DRAWN;
    }

    /**
     * @return bool
     */
    public function hasSurvived(): bool
    {
        return $this->type == self::STATUS_SURVIVED;
    }

    /**
     * @return bool
     */
    public function hasResigned(): bool
    {
        return $this->type == self::STATUS_RESIGNED;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}