<?php

namespace Diplomacy\Models;

/**
 * Base class for database model interaction
 *
 * @package Diplomacy\Models
 */
abstract class Base
{
    protected $attributes = [];
    protected $db;

    public function __construct()
    {
        $this->attributes = [];
    }

    public static function fromRow($row)
    {
        $tournament = new static();
        $tournament->setAttributes($row);
        return $tournament;
    }

    protected function setAttributes(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function __toString() : string
    {
        return var_export($this->attributes, true);
    }
}