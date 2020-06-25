<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ForceUserIntoDisorderForm extends BaseForm
{
    public $id = 'admin-game-publicize';
    protected $name = 'admin-game-publicize';
    protected $template = 'forms/admin/games/force_user_into_disorder.twig';
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

