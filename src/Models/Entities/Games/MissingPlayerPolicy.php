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
}