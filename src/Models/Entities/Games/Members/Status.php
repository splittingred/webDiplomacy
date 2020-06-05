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
}