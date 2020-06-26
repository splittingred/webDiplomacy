<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $variantID
 * @property int $mapID
 * @property int $supplyCenterTarget
 * @property int $supplyCenterCount
 * @property int $countryCount
 * @property string $name
 * @property string $fullName
 * @property string $description
 * @property string $author
 * @property string $adapter
 * @property string $version
 * @property string $codeVersion
 * @property string $homepage
 * @property string $countriesList
 * @package Diplomacy\Models
 */
class VariantInfo extends EloquentBase
{
    protected $table = 'wD_VariantInfo';

    /*****************************************************************************************************************
     * RELATIONSHIPS
     ****************************************************************************************************************/

    /*****************************************************************************************************************
     * SCOPES
     ****************************************************************************************************************/

    /**
     * @param Builder $query
     * @param int $mapId
     * @return Builder
     */
    public function scopeForMap(Builder $query, int $mapId) : Builder
    {
        return $query->where('mapID', '=', $mapId);
    }

    /**
     * @param Builder $query
     * @param int $variantId
     * @return Builder
     */
    public function scopeForVariant(Builder $query, int $variantId) : Builder
    {
        return $query->where('variantID', '=', $variantId);
    }

    /*****************************************************************************************************************
     * INSTANCE METHODS
     ****************************************************************************************************************/

}