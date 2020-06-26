<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Services\Users\ReliabilityService;

class CivilDisordersController extends BaseController
{
    protected string $template = 'pages/users/civil-disorders.twig';
    protected ReliabilityService $reliabilityService;

    public function setUp(): void
    {
        $this->reliabilityService = new ReliabilityService();
        parent::setUp();
    }

    public function call(): array
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