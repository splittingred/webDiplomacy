<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class TogglePauseForm extends BaseForm
{
    public string $id = 'admin-game-toggle-pause';
    protected string $name = 'admin-game-toggle-pause';
    protected string $template = 'forms/admin/games/toggle_pause.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
    ];

    public function handleSubmit(): BaseForm
    {
        /* @see processGame::togglePause */
        return $this;
    }
}

