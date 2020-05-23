<?php

namespace Diplomacy\Models;

/**
 * @package Diplomacy\Models
 */
class Tournament extends EloquentBase
{
    protected $table = 'wD_Tournaments';
    protected $hidden = [];

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->status != 'PreStart';
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status == 'Active';
    }

    /**
     * @return bool
     */
    public function isRegistrationComplete()
    {
        return $this->status != 'Registration';
    }
}