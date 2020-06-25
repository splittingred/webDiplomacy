<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class PrivatizeForm extends BaseForm
{
    public $id = 'admin-game-privatize';
    protected $name = 'admin-game-privatize';
    protected $template = 'forms/admin/games/privatize.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'password' => [
            'type' => 'password',
            'default' => '',
        ],
    ];

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

