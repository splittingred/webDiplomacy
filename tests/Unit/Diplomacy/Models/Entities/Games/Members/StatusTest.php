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

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestIsPlaying
     */
    public function testIsPlaying(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->isPlaying());
    }
    public function providerTestIsPlaying(): array
    {
        return [
            [true, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
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
     * @dataProvider providerTestHasWon
     */
    public function testHasWon(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasWon());
    }
    public function providerTestHasWon(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [true, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestHasLost
     */
    public function testHasLost(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasLost());
    }
    public function providerTestHasLost(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [true, Status::STATUS_DEFEATED],
            [true, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestIsDefeated
     */
    public function testIsDefeated(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->isDefeated());
    }
    public function providerTestIsDefeated(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [true, Status::STATUS_DEFEATED],
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
     * @dataProvider providerTestHasLeft
     */
    public function testHasLeft(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasLeft());
    }
    public function providerTestHasLeft(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [true, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestHasDrawn
     */
    public function testHasDrawn(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasDrawn());
    }
    public function providerTestHasDrawn(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [true, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestHasSurvived
     */
    public function testHasSurvived(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasSurvived());
    }
    public function providerTestHasSurvived(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [false, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [true, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    /**
     * @param bool $expected
     * @param string $statusType
     * @dataProvider providerTestHasResigned
     */
    public function testHasResigned(bool $expected = true, string $statusType = 'playing')
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $status->type = $statusType;
        $this->assertEquals($expected, $status->hasResigned());
    }
    public function providerTestHasResigned(): array
    {
        return [
            [false, Status::STATUS_PLAYING],
            [false, Status::STATUS_UNASSIGNED],
            [false, Status::STATUS_DEFEATED],
            [true, Status::STATUS_RESIGNED],
            [false, Status::STATUS_LEFT],
            [false, Status::STATUS_DRAWN],
            [false, Status::STATUS_SURVIVED],
            [false, Status::STATUS_WON],
        ];
    }

    public function testToString()
    {
        /** @var Status $status */
        $status = $this->factories()->instance(Status::class);
        $this->assertEquals('playing', (string)$status);
    }
}