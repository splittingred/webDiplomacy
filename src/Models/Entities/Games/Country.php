<?php

namespace Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Game;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Country
{
    const ALL = -1;
    const GLOBAL = 0;

    public $id = self::GLOBAL;
    public $name = 'Global';

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isGlobal(): bool
    {
        return $this->id == static::GLOBAL;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}