<?php

namespace Diplomacy\Models\Entities\Users;

class MutedCountry
{
    public $countryId;
    public $gameId;
    public $time;

    public function __construct(int $countryId, int $gameId, int $time)
    {
        $this->countryId = $countryId;
        $this->gameId = $gameId;
        $this->time = $time;
    }
}

