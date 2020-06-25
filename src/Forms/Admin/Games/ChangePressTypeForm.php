<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ChangePressTypeForm extends BaseForm
{
    public $id = 'admin-game-change-press-type';
    protected $name = 'admin-game-change-press-type';
    protected $template = 'forms/admin/games/change_press_type.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'press_type_id' => [
            'type' => 'Games\PressTypeSelect',
        ]
    ];

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

