<?php

namespace Diplomacy\Models;

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

    /**
     * @return HasMany
     */
    public function members()
    {
        return $this->hasMany(Member::class, 'gameID');
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

    /**
     * @return string
     */
    public function getSummary() : string
    {
        return $this->getHomeGamePanel()->summary();
    }
}