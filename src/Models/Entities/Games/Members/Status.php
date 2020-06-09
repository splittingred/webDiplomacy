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
    protected $type;

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
     * @return bool
     */
    public function isAlive(): bool
    {
        return !$this->isDead();
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
    public function won(): bool
    {
        return $this->type == self::STATUS_WON;
    }

    /**
     * @return bool
     */
    public function defeated(): bool
    {
        return $this->type == self::STATUS_DEFEATED;
    }

    /**
     * @return bool
     */
    public function left(): bool
    {
        return $this->type == self::STATUS_LEFT;
    }

    /**
     * @return bool
     */
    public function drew(): bool
    {
        return $this->type == self::STATUS_DRAWN;
    }

    /**
     * @return bool
     */
    public function survived(): bool
    {
        return $this->type == self::STATUS_SURVIVED;
    }

    /**
     * @return bool
     */
    public function resigned(): bool
    {
        return $this->type == self::STATUS_RESIGNED;
    }

    /**
     * @return bool
     */
    public function unassigned(): bool
    {
        return $this->type == self::STATUS_UNASSIGNED;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}