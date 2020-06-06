<?php

namespace Diplomacy\Models\Entities;

class Tournament
{
    /** @var int $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var int $totalRounds */
    public $totalRounds;
    /** @var User $director */
    public $director;
    /** @var User $coDirector */
    public $coDirector;
}

