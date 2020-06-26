<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Models\Game;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Services\Request;

class ChangePhaseLengthForm extends BaseForm
{
    public string $id = 'admin-game-change-phase-length';
    protected string $name = 'admin-game-change-phase-length';
    protected string $template = 'forms/admin/games/change_phase_length.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'phase_length' => [
            'type' => 'Games\PhaseLengthSelect',
        ]
    ];

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'game_id'       => 'required',
            'phase_length'  => 'required|between:5,14400|in:'.join(',', OptionsService::PHASE_LENGTHS),
        ];
    }

    public function handleSubmit(): BaseForm
    {
        $game = Game::find($this->getGame()->id);
        $newPhaseMinutes = (int)$this->getValue('phase_length');

        if ($game->processStatus != 'Not-processing' || $game->phase == 'Finished') {
            $this->setNotice('Game is either crashed/paused/finished/processing, and so the next-process time cannot be altered.');
            return $this;
        }
        if ($newPhaseMinutes < 5 || $newPhaseMinutes > 10*24*60) {
            $this->setNotice('Given phase minutes out of bounds (5 minutes to 10 days)');
            return $this;
        }

        $game->phaseMinutes = $newPhaseMinutes;
        $game->processTime = time()+ ($newPhaseMinutes * 60);

        $game->save();
        $this->redirectToSelf();
        return $this;
    }
}

