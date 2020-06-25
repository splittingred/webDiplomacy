<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\AdjustExcusedMissedTurnsForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class AdjustExcusedMissedTurnsComponent extends BaseGameFormModalComponent
{
    protected $title = 'Adjust Excused Missed Turns';
    protected $submitText = 'Adjust';
    protected $formClass = AdjustExcusedMissedTurnsForm::class;
}