<?php

namespace Diplomacy\Models\Entities\Users;

class MutedCountry
{
    public int $countryId;
    public int $gameId;
    public int $time;

    public function __construct(int $countryId, int $gameId, int $time)
    {
        $this->countryId = $countryId;
        $this->gameId = $gameId;
        $this->time = $time;
    }
}

