<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class ReallocateCountriesForm extends BaseForm
{
    public string $id = 'admin-game-reallocate-countries';
    protected string $name = 'admin-game-reallocate-countries';
    protected string $template = 'forms/admin/games/reallocate_countries.twig';
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

