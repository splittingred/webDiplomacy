<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class AdjustExcusedMissedTurnsForm extends BaseForm
{
    public $id = 'admin-game-adjust-excused-missed-turns';
    protected $name = 'admin-game-adjust-excused-missed-turns';
    protected $template = 'forms/admin/games/adjust_excused_missed_turns.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'amount' => [
            'type' => 'number',
            'default' => 1,
            'min' => -100,
            'max' => 100,
        ]
    ];

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

