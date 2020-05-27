<?php
namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Model as Model;

abstract class EloquentBase extends Model
{
    /** @var bool Unfortunately we do not have this in webDip models at present */
    public $timestamps = false;

    /**
     * @return string
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}