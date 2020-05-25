<?php

namespace Diplomacy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use panelGameHome;
use WDVariant;

class Game extends EloquentBase
{
    protected $table = 'wD_Games';
    protected $hidden = [
        'password',
    ];
    /** @var WDVariant */
    protected $variant;
    /** @var panelGameHome */
    protected $homeGamePanel;

    public function scopeJoinMembers(Builder $query) : Builder
    {
        return $query->join('wD_Members', 'wD_Members.gameID', '=', 'wD_Games.id');
    }

    /**
     * @return HasMany
     */
    public function members()
    {
        return $this->hasMany(\Diplomacy\Models\Member::class, 'gameID', 'id');
    }

    /**
     * @return WDVariant
     */
    public function getVariant() : WDVariant
    {
        if (!$this->variant) $this->variant = \libVariant::loadFromVariantID($this->variantID);
        return $this->variant;
    }

    /**
     * @return panelGameHome
     */
    public function getHomeGamePanel() : panelGameHome
    {
        if (!$this->homeGamePanel) $this->homeGamePanel = $this->getVariant()->panelGameHome($this->toArray());
        return $this->homeGamePanel;
    }

    public function scopeGameOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'Yes');
    }

    public function scopeGameNotOver(Builder $query) : Builder
    {
        return $query->where('gameOver', 'No');
    }

    public function scopeNotPreGame(Builder $query) : Builder
    {
        return $query->where('phase', '!=' , 'Pre-Game');
    }

    /**
     * @return string
     */
    public function getSummary() : string
    {
        return $this->getHomeGamePanel()->summary();
    }
}