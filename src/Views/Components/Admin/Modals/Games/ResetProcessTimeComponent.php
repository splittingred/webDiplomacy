<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ResetProcessTimeComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/reset_process_time.twig';
    protected $modalId = 'admin-game-reset-process-time';
    protected $modalTitle = 'Reset Process Time';
    protected $modalSubmitText = 'Reset';
    protected $formAction = '/admin/games/:game_id/processing/reset';
}