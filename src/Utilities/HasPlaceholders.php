<?php

namespace Diplomacy\Utilities;

trait HasPlaceholders
{
    protected array $placeholders = [];

    /**
     * Set a placeholder value for the view
     *
     * @param string $k
     * @param mixed $v
     * @return $this
     */
    public function setPlaceholder(string $k, $v = null)
    {
        $this->placeholders[$k] = $v;
        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function setPlaceholders(array $array)
    {
        $this->placeholders = array_merge($this->placeholders,$array);
        return $this;
    }

    /**
     * Unset a placeholder value
     *
     * @param string $k
     * @return $this
     */
    public function unsetPlaceholder(string $k)
    {
        unset($this->placeholders[$k]);
        return $this;
    }

    /**
     * Get a placeholder value
     *
     * @param string $k
     * @param mixed $default
     * @return mixed
     */
    public function getPlaceholder(string $k, $default = null)
    {
        return array_key_exists($k,$this->placeholders) ? $this->placeholders[$k] : $default;
    }

    /**
     * Return all set placeholders
     *
     * @return array
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }
}