<?php

namespace Diplomacy\Models\Entities;

/**
 * A logged action by an admin/mod
 *
 * @package Diplomacy\Models\Entities
 */
class AdminLog
{
    public int $id = 0;
    public string $name = '';
    public ?User $user;
    public int $time = 0;
    public string $details = '';
    public array $params = [];

    /**
     * @return string
     */
    public function timeAsText(): string
    {
        return \libTime::text($this->time);
    }

    /**
     * @return string
     */
    public function getParamsAsString() : string
    {
        return !empty($this->params) ? print_r($this->params, true) : '';
    }
}

