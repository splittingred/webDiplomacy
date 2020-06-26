<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class ProcessGameNowForm extends BaseForm
{
    public string $id = 'admin-game-process-game-now';
    protected string $name = 'admin-game-process-game-now';
    protected string $template = 'forms/admin/games/process_game_now.twig';
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

