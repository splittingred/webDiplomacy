<?php

namespace Diplomacy\Services\Monads;

class Success extends Result
{
    /**
     * @return bool
     */
    public function successful() : bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function failure() : bool
    {
        return false;
    }
}