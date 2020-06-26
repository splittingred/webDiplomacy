<?php

namespace Diplomacy\Controllers\Stats;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Stats\HallOfFame;

class HallOfFameController extends BaseController
{
    protected string $template = 'pages/stats/hall_of_fame.twig';
    protected string $pageTitle = 'Hall of Fame';
    protected string $pageDescription = 'The webDiplomacy hall of fame; the 100 highest ranking players on this server.';
    protected HallOfFame $hallOfFame;

    public function setUp(): void
    {
        $this->hallOfFame = new HallOfFame();
    }

    public function call(): array
    {
        return [
            'current_user_ranking' => $this->currentUser->rankingDetails(),
            'hof_users' => $this->hallOfFame->getUsers(),
            'hof_active_users' => $this->hallOfFame->getActiveUsers(),
        ];
    }
}