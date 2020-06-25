<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class RestoreFromBackupForm extends BaseForm
{
    public $id = 'admin-game-restore-from-backup';
    protected $name = 'admin-game-restore-from-backup';
    protected $template = 'forms/admin/games/restore_from_backup.twig';
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

