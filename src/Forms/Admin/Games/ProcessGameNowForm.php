<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ProcessGameNowForm extends BaseForm
{
    public $id = 'admin-game-process-game-now';
    protected $name = 'admin-game-process-game-now';
    protected $template = 'forms/admin/games/process_game_now.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
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

