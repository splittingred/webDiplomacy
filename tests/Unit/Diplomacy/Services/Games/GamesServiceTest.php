<?php

namespace Unit\Diplomacy\Services\Games;

use Diplomacy\Models\Game;
use Diplomacy\Services\Games\GamesService;
use Support\TestCase;

class GamesServiceTest extends TestCase
{
    private function getService()
    {
        return new GamesService();
    }

    public function testFind()
    {
        $factoryGame = $this->factories()->create(Game::class);
        $game = $this->getService()->find($factoryGame->id);
        $this->assertInstanceOf(Game::class, $game);
    }
    public function testFindNotFound()
    {
        $this->expectException(\Exception::class);
        $this->getService()->find(999999999);
    }
}