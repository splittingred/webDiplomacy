<?php

namespace Diplomacy\Models;

use Iterator;

/**
 * @package Diplomacy\Models
 */
class Collection implements Iterator
{
    /** @var array */
    protected $entities;
    /** @var int */
    protected int $total;
    /** @var int */
    private int $position = 0;

    /**
     * @param $entities
     * @param int $total
     */
    public function __construct($entities = [], int $total = 0)
    {
        $this->entities = $entities;
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal() : int
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return bool
     */
    public function any() : bool
    {
        return count($this->entities) > 0;
    }

    public function current()
    {
        return $this->entities[$this->position];
    }

    public function next() : void
    {
        ++$this->position;
    }

    public function key() : int
    {
        return $this->position;
    }

    public function valid() : bool
    {
        return isset($this->entities[$this->position]);
    }

    public function rewind() : void
    {
        $this->position = 0;
    }
}