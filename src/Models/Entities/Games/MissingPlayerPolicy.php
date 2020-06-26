<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class MissingPlayerPolicy
{
    const TYPE_STRICT = 'strict';
    const TYPE_NORMAL = 'normal';
    const TYPE_WAIT = 'wait';

    protected string $type;

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
        return $this->type == static::TYPE_NORMAL;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->type == static::TYPE_STRICT;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->type == static::TYPE_WAIT;
    }
}