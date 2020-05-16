<?php

use PHPUnit\Framework\TestCase;

class libTimeTest extends TestCase
{
    public function testStamp()
    {
        $stamp = libTime::stamp();
        $this->assertNotEmpty($stamp);
    }
}