<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\RestoreFromBackupForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class RestoreFromBackupComponent extends BaseGameFormModalComponent
{
    protected $title = 'Restore from Backup';
    protected $submitText = 'Restore';
    protected $formClass = RestoreFromBackupForm::class;
}