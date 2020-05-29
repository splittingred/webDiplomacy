<?php

namespace Diplomacy\Utilities;

trait HasPlaceholders
{
    /** @var array */
    protected $placeholders = [];

    /**
     * Set a placeholder value for the view
     *
     * @param string $k
     * @param mixed $v
     */
    public function setPlaceholder($k,$v = null)
    {
        $this->placeholders[$k] = $v;
    }

    /**
     * @param array $array
     */
    public function setPlaceholders(array $array)
    {
        $this->placeholders = array_merge($this->placeholders,$array);
    }

    /**
     * Unset a placeholder value
     *
     * @param string $k
     */
    public function unsetPlaceholder($k)
    {
        unset($this->placeholders[$k]);
    }

    /**
     * Get a placeholder value
     *
     * @param string $k
     * @param mixed $default
     * @return mixed
     */
    public function getPlaceholder($k,$default = null)
    {
        return array_key_exists($k,$this->placeholders) ? $this->placeholders[$k] : $default;
    }

    /**
     * Return all set placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }
}