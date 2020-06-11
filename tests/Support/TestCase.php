<?php
namespace Support;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected static $factory;

    public static function getFactory()
    {
        if (!static::$factory) {
            $generator = new \Faker\Generator();
            // static::$factory = \Illuminate\Database\Eloquent\Factory::construct($generator, ROOT_PATH . '/tests/Factories/');
        }
        return static::$factory;
    }

    public function factory($type, array $atttributes = [])
    {
        return static::getFactory()->make($type, $atttributes);
    }
}