<?php

namespace Diplomacy\Services\Monads;

class Failure extends Result
{
    /**
     * @param string $code
     * @param string $message
     * @return Failure
     */
    public static function withError(string $code, string $message): Failure
    {
        return new static(new Error($code, $message));
    }

    /**
     * @return bool
     */
    public function successful() : bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function failure() : bool
    {
        return true;
    }
}