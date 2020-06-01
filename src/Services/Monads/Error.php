<?php

namespace Diplomacy\Services\Monads;

/**
 * @package Diplomacy\Services\Monads
 */
class Error
{
    protected $code;
    protected $message;

    /**
     * @param string $code
     * @param string $message
     */
    public function __construct(string $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }
}