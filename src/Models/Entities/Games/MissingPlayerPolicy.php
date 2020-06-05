<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class MissingPlayerPolicy
{
    protected $type;

    /**
     * @param string $type Values of Normal, Strict, Wait
     */
    public function __construct(string $type = 'normal')
    {
        $this->type = strtolower($type);
    }

    /**
     * @return bool
     */
    public function isNormal(): bool
    {
        return $this->type == 'normal';
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->type == 'strict';
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->type == 'wait';
    }
}