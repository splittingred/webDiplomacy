<?php

namespace Unit\Diplomacy\Services\Monads;

use Diplomacy\Services\Monads\Success;
use Support\TestCase;

class SuccessTest extends TestCase
{
    public function testSuccessful()
    {
        $failure = new Success();
        $this->assertTrue($failure->successful());
    }

    public function testFailure()
    {
        $failure = new Success();
        $this->assertFalse($failure->failure());
    }
}