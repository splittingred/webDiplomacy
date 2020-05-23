<?php

namespace Diplomacy\Models;

use WDVariant;

/**
 * @package Diplomacy\Models
 */
class GameMessage extends EloquentBase
{
    protected $table = 'wD_GameMessages';

    protected $variant;

    /**
     * @param WDVariant $variant
     */
    public function setVariant(WDVariant $variant) : void
    {
        $this->variant = $variant;
    }

    /**
     * @return string
     */
    public function getFromCountryName() : string
    {
        return $this->variant->getCountryName($this->fromCountryID);
    }

    /**
     * @return string
     */
    public function getToCountryName() : string
    {
        return $this->variant->getCountryName($this->toCountryID);
    }

    /**
     * @return string
     */
    public function getTimeSentText() : string
    {
        return \libTime::text($this->timeSent);
    }

    /**
     * @return bool
     */
    public function fromGameMaster() : bool
    {
        return intval($this->fromCountryID) == 0;
    }

    /**
     * @return bool
     */
    public function toGlobal() : bool
    {
        return intval($this->toCountryID) == 0;
    }

    /**
     * @return bool
     */
    public function isPersonalNote() : bool
    {
        return !$this->toGlobal() && $this->toCountryID == $this->fromCountryID;
    }

    /**
     * @param int $memberCountryId
     * @return string
     */
    public function getCountryToText($memberCountryId = 0) : string
    {
        if ($this->toGlobal()) return 'Global';
        if ($memberCountryId == $this->toCountryID) return 'you';
        return $this->getToCountryName();
    }

    /**
     * @param int $memberCountryId
     * @return string
     */
    public function getCountryFromText($memberCountryId = 0) : string
    {
        if ($this->fromGameMaster()) return 'Gamemaster';
        if ($memberCountryId == $this->fromCountryID) return 'you';
        return $this->getFromCountryName();
    }
}