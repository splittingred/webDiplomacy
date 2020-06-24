<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class TogglePauseComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/toggle_pause.twig';
    protected $modalId = 'admin-game-toggle-pause';
    protected $modalTitle = 'Toggle Game Pause Status';
    protected $modalSubmitText  = 'Toggle Pause';
    protected $formAction = '/admin/games/:game_id/processing/toggle-pause';
}