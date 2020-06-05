<?php

namespace Diplomacy\Models\Entities\Games\Members;

/**
 * @package Diplomacy\Models\Entities\Games\Members
 */
class Status
{
    /** @var string */
    protected $type;

    /**
     * Valid types: 'Playing','Defeated','Left','Won','Drawn','Survived','Resigned'
     *
     * @param string $type
     */
    public function __construct(string $type = 'playing')
    {
        $this->type = strtolower($type);
    }

    /**
     * @return bool
     */
    public function isPlaying(): bool
    {
        return $this->type == 'playing';
    }

    /**
     * @return bool
     */
    public function won(): bool
    {
        return $this->type == 'won';
    }

    /**
     * @return bool
     */
    public function defeated(): bool
    {
        return $this->type == 'defeated';
    }

    /**
     * @return bool
     */
    public function left(): bool
    {
        return $this->type == 'left';
    }

    /**
     * @return bool
     */
    public function drew(): bool
    {
        return $this->type == 'drawn';
    }

    /**
     * @return bool
     */
    public function survived(): bool
    {
        return $this->type == 'survived';
    }

    /**
     * @return bool
     */
    public function resigned(): bool
    {
        return $this->type == 'resigned';
    }
}