<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ProcessGameNowComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/process_game_now.twig';
    protected $modalId = 'admin-game-process-now';
    protected $modalTitle = 'Process Game Now';
    protected $modalSubmitText = 'Process Now';
    protected $formAction = '/admin/games/:game_id/processing';
}