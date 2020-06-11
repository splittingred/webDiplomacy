<?php
namespace Support;

use League\FactoryMuffin\FactoryMuffin;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** @var FactoryMuffin $fm */
    public static $fm;

    public static function setupBeforeClass(): void
    {
        static::$fm = new FactoryMuffin();
        static::$fm->loadFactories([ROOT_PATH . 'tests/Factories']);
    }

    /**
     * @return FactoryMuffin
     */
    public function factories()
    {
        return static::$fm;
    }

    public static function tearDownAfterClass(): void
    {
        static::$fm->deleteSaved();
    }
}