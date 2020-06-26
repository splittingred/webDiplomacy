<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Status
{
    const STATUS_ACTIVE = 'no';
    const STATUS_WON = 'won';
    const STATUS_DRAWN = 'drawn';

    protected string $type = 'no';

    /**
     * @param string $type
     */
    public function __construct(string $type = 'No')
    {
        $this->type = strtolower($type);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->type == static::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isOver(): bool
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function wasWon(): bool
    {
        return $this->type == static::STATUS_WON;
    }

    /**
     * @return bool
     */
    public function wasDrawn(): bool
    {
        return $this->type == static::STATUS_DRAWN;
    }
}