<?php

namespace Diplomacy\Models;

use panelGameBoard;
use WDVariant;

/**
 * @package Diplomacy\Models
 */
class Order extends Base
{
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
    public function countryName() : string
    {
        return $this->variant->getCountryName($this->countryID);
    }

    /**
     * @return string
     */
    public function phaseName() : string
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
    public function territoryName() : string
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
    public function toTerritoryName() : string
    {
        if (empty($this->toTerritoryName) && $this->variant) {
            $this->toTerritoryName = $this->variant->getTerritoryName($this->toTerrID);
        }
        return $this->toTerritoryName;
    }

    /**
     * Get the order in a long-form text format
     *
     * @return string
     */
    public function text() : string
    {
        switch(strtolower($this->type)) {
            case 'retreat':
                $str = l_t('The %s at %s retreat to %s',$this->unitType, $this->territoryName(), $this->toTerritoryName());
                break;
            case 'disband':
                $str = l_t('The %s at %s disband', $this->unitType, $this->territoryName());
                break;
            case 'build army':
                $str = l_t('Build army at %s', $this->territoryName());
                break;
            case 'build fleet':
                $str = l_t('Build fleet at %s', $this->territoryName());
                break;
            case 'wait':
                $str = l_t('Do not use build order');
                break;
            case 'destroy':
                $str = l_t('Destroy the unit at %s', $this->territoryName());
                break;
            default:
                $str = l_t("The %s at %s %s", $this->unitType, $this->territoryName(), $this->type);
                if (!empty($this->toTerrID)) $str .= ' to ' . $this->toTerritoryName();
                if (!empty($this->terrID)) $str .= ' from ' . $this->territoryName();
                if ($this->isConvoy()) $str .= ' via convoy';
        }

        if ($this->wasDislodged() || (!$this->successful() && !$this->isHold())) {
            $str = "<u>$str";
            if (!$this->successful() && !$this->isHold()) {
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
    public function turnAsDate() : string
    {
        return $this->gameBoard ? $this->gameBoard->datetxt($this->turn) : '';
    }

    /**
     * Was this order successful?
     *
     * @return bool
     */
    public function successful() : bool
    {
        return $this->success == 'Yes';
    }

    /**
     * Did this order fail?
     *
     * @return bool
     */
    public function failed() : bool
    {
        return !$this->successful();
    }
}