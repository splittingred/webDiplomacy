<?php

namespace Diplomacy\Services\Monads;

abstract class Result
{
    /** @var mixed */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    abstract public function successful();
    abstract public function failure();
}