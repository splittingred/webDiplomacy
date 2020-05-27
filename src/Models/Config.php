<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @package Diplomacy\Models
 */
class Config extends EloquentBase
{
    protected $table = 'wD_Config';

    /**
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeWithName(Builder $query, string $name) : Builder
    {
        return $query->where('name', $name);
    }
}