<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Status
{
    /** @var string $type */
    protected $type;

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
        return $this->type == 'no';
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
        return $this->type == 'won';
    }

    /**
     * @return bool
     */
    public function wasDrawn(): bool
    {
        return $this->type == 'drawn';
    }
}