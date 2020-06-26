<?php

namespace Diplomacy\Models\Entities\Users;

/**
 * Value class of various commonly-referenced counts for a user
 *
 * @package Diplomacy\Models\Entities\Users
 */
class Counts
{
    public int $civilDisorders;
    public int $civilDisordersTakenOver;
    public int $civilDisordersDeleted;
    public int $nmrs;
    public int $phases;
    public int $yearlyPhases;
    public int $games;

    /**
     * @param int $civilDisorders
     * @param int $civilDisordersTakenOver
     * @param int $civilDisordersDeleted
     * @param int $nmrs
     * @param int $phases
     * @param int $yearlyPhases
     * @param int $games
     */
    public function __construct(
        int $civilDisorders = 0,
        int $civilDisordersTakenOver = 0,
        int $civilDisordersDeleted = 0,
        int $nmrs = 0,
        int $phases = 0,
        int $yearlyPhases = 0,
        int $games = 0
    ) {
        $this->civilDisorders = $civilDisorders;
        $this->civilDisordersTakenOver = $civilDisordersTakenOver;
        $this->civilDisordersDeleted = $civilDisordersDeleted;
        $this->nmrs = $nmrs;
        $this->phases = $phases;
        $this->yearlyPhases = $yearlyPhases;
        $this->games = $games;
    }
}

