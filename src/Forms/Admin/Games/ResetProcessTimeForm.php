<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Models\Game;
use Diplomacy\Services\Request;

class ResetProcessTimeForm extends BaseForm
{
    public string $id = 'admin-game-reset-process-time';
    protected string $name = 'admin-game-reset-process-time';
    protected string $template = 'forms/admin/games/reset_process_time.twig';
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

        if ($game->processStatus != 'Not-processing' || $game->phase == 'Finished') {
            $this->setNotice('This game is paused/crashed/finished.');
            return $this;
        }

        $game->processTime = time();
        $game->save();
        $this->redirectToSelf();

        return $this;
    }
}

