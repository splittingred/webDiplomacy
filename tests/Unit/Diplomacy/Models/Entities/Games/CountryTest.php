<?php

namespace Unit\Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    /**
     * @dataProvider providerTestShortName
     * @param string $input
     * @param string $expected
     */
    public function testShortName(string $input, string $expected)
    {
        $country = new Country(1, $input);
        $this->assertSame($expected, $country->shortName());
    }
    public function providerTestShortName(): array
    {
        return [
            ['England', 'Eng'],
            ['France', 'Fra'],
            ['Germany', 'Ger'],
            ['Italy', 'Ita'],
            ['Russia', 'Rus'],
            ['Austria', 'Aus'],
            ['Turkey', 'Tur'],
        ];
    }

    public function testIsGlobal()
    {
        $global = new Country(0, 'Global');
        $this->assertTrue($global->isGlobal());

        $england = new Country(1, 'England');
        $this->assertFalse($england->isGlobal());
    }

    public function testToString()
    {
        $england = new Country(1, 'England');
        $this->assertEquals('England', (string)$england);
    }
}