<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Services\Users\ReliabilityService;

class CivilDisordersController extends BaseController
{
    /** @var string  */
    protected $template = 'pages/users/civil-disorders.twig';

    /** @var ReliabilityService */
    protected $reliabilityService;

    public function setUp()
    {
        $this->reliabilityService = new ReliabilityService();
        parent::setUp();
    }

    public function call()
    {
        return array_merge($this->reliabilityService->forUser($this->user),[
           'missed_turns' => $this->getMissedTurns(),
        ]);
    }

    protected function getMissedTurns()
    {
        $turns = $this->user->missedTurns()->with('game')->get();
        foreach ($turns as $turn) {
            $turn->game->getVariant();
        }
        return $turns;
    }
}