<?php

namespace Diplomacy\Models;

use panelGameBoard;
use WDVariant;

/**
 * @package Diplomacy\Models
 */
class ArchivedMove extends EloquentBase
{
    protected $table = 'wD_MovesArchive';

    /** @var WDVariant $variant */
    protected $variant;
    /** @var panelGameBoard $gameBoard */
    protected $gameBoard;
    /** @var string */
    protected $territoryName = '';
    /** @var string */
    protected $toTerritoryName = '';

    /**
     * @param WDVariant $variant
     */
    public function setVariant(WDVariant $variant) : void
    {
        $this->variant = $variant;
    }

    /**
     * @param panelGameBoard $gameBoard
     */
    public function setGameBoard(panelGameBoard $gameBoard) : void
    {
        $this->gameBoard = $gameBoard;
    }

    /**
     * @return string
     */
    public function getCountryName() : string
    {
        return $this->variant->getCountryName($this->countryID);
    }

    /**
     * @return string
     */
    public function getPhaseName() : string
    {
        switch (strtolower($this->type)) {
            case 'retreat':
            case 'disband':
                return 'Retreat';
            case 'build army':
            case 'build fleet':
            case 'wait':
            case 'destroy':
                return 'Build';
        }
        return 'Diplomacy';
    }

    /**
     * Get name of territory unit is on at the time of the order.
     *
     * @return string
     */
    public function getTerritoryName() : string
    {
        if (empty($this->territoryName) && $this->variant) {
            $this->territoryName = $this->variant->getTerritoryName($this->terrID);
        }
        return $this->territoryName;
    }

    /**
     * Get name of territory unit desired to move to at the time of the order.
     *
     * @return string
     */
    public function getToTerritoryName() : string
    {
        if (empty($this->toTerritoryName) && $this->variant) {
            $this->toTerritoryName = $this->variant->getTerritoryName($this->toTerrID);
        }
        return $this->toTerritoryName;
    }

    /**
     * Is this a build phase order?
     *
     * @return bool
     */
    public function isInBuildPhase() : bool
    {
        return in_array(strtolower($this->type), ['wait', 'build army', 'build fleet']);
    }

    /**
     * @return bool
     */
    public function isInRetreatPhase() : bool
    {
        return in_array(strtolower($this->type), ['retreat', 'disband']);
    }

    /**
     * Get the order in a long-form text format
     *
     * @return string
     */
    public function getText() : string
    {
        switch(strtolower($this->type)) {
            case 'retreat':
                $str = l_t('The %s at %s retreat to %s',$this->unitType, $this->getTerritoryName(), $this->getToTerritoryName());
                break;
            case 'disband':
                $str = l_t('The %s at %s disband', $this->unitType, $this->getTerritoryName());
                break;
            case 'build army':
                $str = l_t('Build army at %s', $this->getTerritoryName());
                break;
            case 'build fleet':
                $str = l_t('Build fleet at %s', $this->getTerritoryName());
                break;
            case 'wait':
                $str = l_t('Do not use build order');
                break;
            case 'destroy':
                $str = l_t('Destroy the unit at %s', $this->getTerritoryName());
                break;
            default:
                $str = l_t("The %s at %s %s", $this->unitType, $this->getTerritoryName(), $this->type);
                if (!empty($this->toTerrID)) $str .= ' to ' . $this->getToTerritoryName();
                if (!empty($this->terrID)) $str .= ' from ' . $this->getTerritoryName();
                if ($this->isConvoy()) $str .= ' via convoy';
        }

        if ($this->wasDislodged() || (!$this->isSuccessful() && !$this->isHold())) {
            $str = "<u>$str";
            if (!$this->isSuccessful() && !$this->isHold()) {
                $str .= '</u> (fail)';
            }
            if ($this->wasDislodged()) {
                $str .= '</u> (dislodged)';
            }
        }
        return $str;
    }

    /**
     * Was this a hold order?
     *
     * @return bool
     */
    public function isHold() : bool
    {
        return strtolower($this->type) == 'hold';
    }

    /**
     * Was the unit dislodged after the order?
     *
     * @return bool
     */
    public function wasDislodged() : bool
    {
        return $this->dislodged == 'Yes';
    }

    /**
     * Was this a convoy order?
     *
     * @return bool
     */
    public function isConvoy() : bool
    {
        return $this->viaConvoy == 'Yes';
    }

    /**
     * Get the turn in a friendly date format (e.g. Spring 1902)
     * @return string
     */
    public function getTurnAsDate() : string
    {
        return $this->gameBoard ? $this->gameBoard->datetxt($this->turn) : '';
    }

    /**
     * Was this order successful?
     *
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return $this->success == 'Yes';
    }

    /**
     * Did this order fail?
     *
     * @return bool
     */
    public function isFailed() : bool
    {
        return !$this->isSuccessful();
    }
}