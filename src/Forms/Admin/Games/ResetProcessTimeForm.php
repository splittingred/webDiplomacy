<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ResetProcessTimeForm extends BaseForm
{
    public $id = 'admin-game-reset-process-time';
    protected $name = 'admin-game-reset-process-time';
    protected $template = 'forms/admin/games/reset_process_time.twig';
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

