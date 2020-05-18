<?php

namespace Diplomacy\Controllers\Stats;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Stats\HallOfFame;

class HallOfFameController extends BaseController
{
    /** @var string */
    protected $template = 'pages/stats/hall_of_fame.twig';
    protected $pageTitle = 'Hall of Fame';
    protected $pageDescription = 'The webDiplomacy hall of fame; the 100 highest ranking players on this server.';

    protected $hallOfFame;

    public function setUp()
    {
        $this->hallOfFame = new HallOfFame($this->database);
    }

    public function call()
    {
        return [
            'user' => $this->user,
            'user_ranking' => $this->user->rankingDetails(),
            'hof_users' => $this->hallOfFame->getUsers(),
            'hof_active_users' => $this->hallOfFame->getActiveUsers(),
        ];
    }
}