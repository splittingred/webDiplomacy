<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class TogglePauseForm extends BaseForm
{
    public $id = 'admin-game-toggle-pause';
    protected $name = 'admin-game-toggle-pause';
    protected $template = 'forms/admin/games/toggle_pause.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
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

