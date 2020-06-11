<?php

namespace Unit\Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\Processing;
use Support\TestCase;

class ProcessingTest extends TestCase
{
    public function buildObject(string $status = Processing::STATUS_PROCESSING, int $time = 0, int $phaseMinutes = 60, int $pauseTimeRemaining = 0)
    {
        $time = $time > 0 ? $time : time() - 3600;
        return new Processing($status, $time, $phaseMinutes, $pauseTimeRemaining);
    }

    public function testGetTime()
    {
        $time = time() - 600;
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, $time);
        $this->assertEquals($time, $processing->getTime());
    }

    public function testOverdue()
    {
        $processing = $this->buildObject();
        $this->assertTrue($processing->overdue());
    }

    public function testOverdueWhenNotDue()
    {
        $future = time() + 3600;
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, $future);
        $this->assertFalse($processing->overdue());
    }

    public function testTimeRemainingAsText()
    {
        $future = time() + 3600;
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, $future);
        $this->assertEquals(\libTime::remainingText($future), $processing->timeRemainingAsText());
    }

    public function testTimeRemainingAsTextWhenOverdue()
    {
        $past = time() - 3600;
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, $past);
        $this->assertEquals(Processing::TIME_NOW_TEXT, $processing->timeRemainingAsText());
    }

    public function testGetPauseTimeRemaining()
    {
        // Default behavior
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, 0, 60, 120);
        $this->assertEquals(120, $processing->getPauseTimeRemaining());
    }

    public function testGetPauseTimeRemainingWhenUnspecified()
    {
        // When pause time remaining == -1
        $processing = $this->buildObject(Processing::STATUS_PROCESSING, 0, 6, -1);
        $this->assertEquals(360, $processing->getPauseTimeRemaining());
    }
}