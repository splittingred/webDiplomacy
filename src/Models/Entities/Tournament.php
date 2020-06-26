<?php

namespace Diplomacy\Models\Entities;

class Tournament
{
    /** @var int $id */
    public int $id;
    /** @var string $name */
    public string $name;
    /** @var int $totalRounds */
    public int $totalRounds;
    /** @var User $director */
    public ?User $director;
    /** @var User $coDirector */
    public ?User $coDirector;
}

