<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class AdjustExcusedMissedTurnsComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/adjust_excused_missed_turns.twig';
    protected $modalId = 'admin-game-adjust-excused-missed-turns';
    protected $modalTitle = 'Adjust Excused Missed Turns';
    protected $modalSubmitText = 'Adjust';
    protected $formAction = '/admin/games/:game_id/users/excused-missed-turns';
}