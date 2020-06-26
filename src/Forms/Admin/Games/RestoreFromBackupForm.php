<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class RestoreFromBackupForm extends BaseForm
{
    public string $id = 'admin-game-restore-from-backup';
    protected string $name = 'admin-game-restore-from-backup';
    protected string $template = 'forms/admin/games/restore_from_backup.twig';
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

