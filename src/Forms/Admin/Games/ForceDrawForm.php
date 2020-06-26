<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class ForceDrawForm extends BaseForm
{
    public string $id = 'admin-game-force-draw';
    protected string $name = 'admin-game-force-draw';
    protected string $template = 'forms/admin/games/force_draw.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
    ];

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

