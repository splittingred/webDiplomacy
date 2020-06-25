<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ForceDrawForm extends BaseForm
{
    public $id = 'admin-game-force-draw';
    protected $name = 'admin-game-force-draw';
    protected $template = 'forms/admin/games/force_draw.twig';
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

