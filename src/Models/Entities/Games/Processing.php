<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Processing
{
    /** @var string $type */
    protected $type;

    /**
     *
     * @param string $type Values of 'Not-processing','Processing','Crashed','Paused'
     */
    public function __construct(string $type = '')
    {
        $this->type = strtolower($type);
    }

    /**
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->type == 'paused';
    }

    /**
     * @return bool
     */
    public function isCrashed(): bool
    {
        return $this->type == 'crashed';
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->type == 'processing';
    }

    /**
     * @return bool
     */
    public function isNotProcessing(): bool
    {
        return $this->type == 'not-processing';
    }
}