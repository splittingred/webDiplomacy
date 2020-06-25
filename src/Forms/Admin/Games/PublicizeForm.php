<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class PublicizeForm extends BaseForm
{
    public $id = 'admin-game-publicize';
    protected $name = 'admin-game-publicize';
    protected $template = 'forms/admin/games/publicize.twig';
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

