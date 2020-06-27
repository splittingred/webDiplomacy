<?php

namespace Diplomacy\Controllers\Admin\Games;

use Diplomacy\Controllers\Admin\BaseController;
use Diplomacy\Forms\Admin\Games\RestoreFromBackupForm;
use Diplomacy\Services\Request;

class BackupController extends BaseController
{
    public string $template = 'admin/games/backup.twig';

    /**
     * @return array
     */
    public function call(): array
    {
        return [
            'form' => $this->makeForm(RestoreFromBackupForm::class)
        ];
    }
}