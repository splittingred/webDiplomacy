<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class RestoreFromBackupForm extends BaseForm
{
    public string $id = 'admin-game-restore-from-backup';
    protected string $name = 'admin-game-restore-from-backup';
    protected string $template = 'forms/admin/games/restore_from_backup.twig';
    protected string $requestType = Request::TYPE_POST;

    /**
     * @return array
     */
    public function getFieldDefinitions(): array
    {
        $game = $this->getGame();
        if ($game) {
            return [
                'game_id' => [
                    'type' => 'Games\Game',
                    'game' => $game,
                    'input' => [
                        'disabled' => 'disabled',
                    ],
                    'default' => 0,
                ],
            ];
        } else {
            return [
                'game_id' => [
                    'type' => 'text',
                    'label' => 'Game ID',
                    'default' => '',
                ],
            ];
        }
    }

    public function handleSubmit(): BaseForm
    {
        return $this;
    }
}

