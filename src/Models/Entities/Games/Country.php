<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Country
{
    public $id;
    public $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}