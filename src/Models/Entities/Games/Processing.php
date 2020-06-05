<?php

namespace Diplomacy\Models\Entities\Games;

/**
 * @package Diplomacy\Models\Entities\Games
 */
class Processing
{
    /** @var string $status */
    protected $status;
    /** @var int $time */
    protected $time;
    /** @var int $phaseMinutes */
    protected $phaseMinutes;
    /** @var int $pauseTimeRemaining */
    protected $pauseTimeRemaining;

    /**
     *
     * @param string $status Values of 'Not-processing','Processing','Crashed','Paused'
     * @param int $time processing time
     * @param int $pauseTimeRemaining
     * @param int $phaseMinutes
     */
    public function __construct(string $status, int $time, int $phaseMinutes, int $pauseTimeRemaining = 0)
    {
        $this->status = strtolower($status);
        $this->time = $time;
        $this->pauseTimeRemaining = $pauseTimeRemaining;
        $this->phaseMinutes = $phaseMinutes;
    }

    /**
     * @return bool is the processing overdue?
     */
    public function overdue(): bool
    {
        return time() >= $this->time;
    }

    /**
     * @return string
     */
    public function timeRemainingAsText(): string
    {
        return $this->overdue() ? 'Now' : \libTime::remainingText($this->time);
    }

    /**
     * @return string
     */
    public function getTimeAsText(): string
    {
        return \libTime::detailedText($this->time);
    }

    /**
     * @return int
     */
    public function getPauseTimeRemaining() : int
    {
        return $this->pauseTimeRemaining == -1 ? $this->phaseMinutes * 60 : (int)$this->pauseTimeRemaining;
    }

    /**
     * @return string
     */
    public function getPauseTimeRemainingAsText(): string
    {
        return \libTime::timeLengthText($this->getPauseTimeRemaining());
    }

    /**
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->status == 'paused';
    }

    /**
     * @return bool
     */
    public function isCrashed(): bool
    {
        return $this->status == 'crashed';
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->status == 'processing';
    }

    /**
     * @return bool
     */
    public function isNotProcessing(): bool
    {
        return $this->status == 'not-processing';
    }
}