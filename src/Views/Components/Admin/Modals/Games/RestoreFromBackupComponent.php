<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class RestoreFromBackupComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/restore.twig';
    protected $formAction = '/admin/games/:game_id/restore';
    protected $modalTitle = 'Restore from Backup';
    protected $modalId = 'admin-game-restore';
    protected $modalSubmitText = 'Restore';
}