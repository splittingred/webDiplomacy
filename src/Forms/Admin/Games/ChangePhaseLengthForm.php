<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ChangePhaseLengthForm extends BaseForm
{
    public $id = 'admin-game-change-phase-length';
    protected $name = 'admin-game-change-phase-length';
    protected $template = 'forms/admin/games/change_phase_length.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'phase_lengths' => [
            'type' => 'Games\PhaseLengthSelect',
        ]
    ];

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

