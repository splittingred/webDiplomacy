<?php

namespace Unit\Diplomacy\Models\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Members\Status;
use Support\TestCase;

class StatusTest extends TestCase
{
    public function testToText()
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $this->assertEquals('Playing', $status->text());
    }

    public function testCssClass()
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $this->assertEquals('memberStatusPlaying', $status->cssClass());
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestIsAlive
     */
    public function testIsAlive(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->isAlive());
    }
    public function providerTestIsAlive(): array
    {
        return [
            [true, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [true, Status::STATUS_DRAWN],
            [true, Status::STATUS_SURVIVED],
            [true, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestIsUnassigned
     */
    public function testIsUnassigned(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->isUnassigned());
    }
    public function providerTestIsUnassigned(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [true, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestIsDead
     */
    public function testIsDead(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->isDead());
    }
    public function providerTestIsDead(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [true, Status::STATUS_DEFEATED],
            [true, Status::STATUS_RESIGNED],
            [true, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }
}