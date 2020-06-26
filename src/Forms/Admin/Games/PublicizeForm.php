<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Models\Game;
use Diplomacy\Services\Request;

class PublicizeForm extends BaseForm
{
    public string $id = 'admin-game-publicize';
    protected string $name = 'admin-game-publicize';
    protected string $template = 'forms/admin/games/publicize.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
    ];

    public function handleSubmit(): BaseForm
    {
        $game = Game::find($this->getGame()->id);
        $game->password = null;
        $game->save();
        $this->redirectToSelf();
        return $this;
    }
}

