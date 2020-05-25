<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @package Diplomacy\Models
 */
class MissedTurn extends EloquentBase
{
    const RECENT_THRESHOLD = 2419200;
    const YEARLY_THRESHOLD = 31536000;

    protected $table = 'wD_MissedTurns';
    protected $hidden = [];

    /**
     * @return string
     */
    public function getTimeText() : string
    {
        return \libTime::detailedText($this->turnDateTime);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\Diplomacy\Models\User', 'userID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo('\Diplomacy\Models\Game', 'gameID');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLive(Builder $query) : Builder
    {
        return $query->where('liveGame', 1);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNonLive(Builder $query) : Builder
    {
        return $query->where('liveGame', 0);
    }

    /**
     * Recent, or in last month.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRecent(Builder $query) : Builder
    {
        return $query->where('turnDateTime', '>', time() - self::RECENT_THRESHOLD);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLastYear(Builder $query) : Builder
    {
        return $query->where('turnDateTime', '>', time() - self::YEARLY_THRESHOLD);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnexcused(Builder $query) : Builder
    {
        return $query->where(function($query) {
            $query->where('modExcused', 0)
                  ->where('samePeriodExcused', 0)
                  ->where('systemExcused', 0);
        });
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeExcused(Builder $query) : Builder
    {
        return $query->where(function($query) {
            $query->where('modExcused', 1)
                ->orWhere('samePeriodExcused', 1)
                ->orWhere('systemExcused', 1);
        });
    }
}