<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class ReallocateCountriesForm extends BaseForm
{
    public $id = 'admin-game-reallocate-countries';
    protected $name = 'admin-game-reallocate-countries';
    protected $template = 'forms/admin/games/reallocate_countries.twig';
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

