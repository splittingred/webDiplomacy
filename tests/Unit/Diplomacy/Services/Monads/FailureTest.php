<?php

namespace Unit\Diplomacy\Services\Monads;

use Diplomacy\Services\Monads\Error;
use Diplomacy\Services\Monads\Failure;
use Support\TestCase;

class FailureTest extends TestCase
{
    public function testSuccessful()
    {
        $failure = new Failure();
        $this->assertFalse($failure->successful());
    }

    public function testFailure()
    {
        $failure = new Failure();
        $this->assertTrue($failure->failure());
    }

    public function testWithError()
    {
        $code = 'internal';
        $message = 'Something failed';
        $failure = Failure::withError($code, $message);

        /** @var Error $error */
        $error = $failure->getValue();
        $this->assertInstanceOf(Error::class, $error);
        $this->assertEquals($code, $error->getCode());
        $this->assertEquals($message, $error->getMessage());
    }
}