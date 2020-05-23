<?php

namespace Diplomacy\Models;

/**
 * @package Diplomacy\Models
 */
class User extends EloquentBase
{
    protected $table = 'wD_Users';
    protected $hidden = ['password'];
}