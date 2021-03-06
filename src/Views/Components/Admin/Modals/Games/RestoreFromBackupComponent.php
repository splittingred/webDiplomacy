<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\RestoreFromBackupForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class RestoreFromBackupComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Restore from Backup';
    protected string $submitText = 'Restore';
    protected string $formClass = RestoreFromBackupForm::class;
}