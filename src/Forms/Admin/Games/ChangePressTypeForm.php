<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class ChangePressTypeForm extends BaseForm
{
    public string $id = 'admin-game-change-press-type';
    protected string $name = 'admin-game-change-press-type';
    protected string $template = 'forms/admin/games/change_press_type.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
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

