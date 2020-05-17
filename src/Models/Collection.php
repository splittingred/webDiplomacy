<?php

namespace Diplomacy\Models;

/**
 * @package Diplomacy\Models
 */
class Collection
{
    /** @var array */
    protected $entities;
    /** @var int */
    protected $total;

    /**
     * @param array $entities
     * @param int $total
     */
    public function __construct(array $entities = [], int $total = 0)
    {
        $this->entities = $entities;
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
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
    public function any()
    {
        return count($this->entities) > 0;
    }
}