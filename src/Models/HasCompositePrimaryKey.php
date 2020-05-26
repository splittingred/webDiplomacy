<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * This trait is needed with tables with composite primary keys, as Eloquent does not support that functionality out
 * of the box.
 *
 * @package Diplomacy\Models
 */
trait HasCompositePrimaryKey
{
    /**
     * Set the keys for a save update query.
     * This is a fix for tables with composite keys
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function setKeysForSaveQuery(Builder $query) {
        if (is_array($this->primaryKey)) {
            foreach ($this->primaryKey as $pk) {
                $query->where($pk, '=', $this->original[$pk]);
            }
            return $query;
        } else{
            return parent::setKeysForSaveQuery($query);
        }
    }
}