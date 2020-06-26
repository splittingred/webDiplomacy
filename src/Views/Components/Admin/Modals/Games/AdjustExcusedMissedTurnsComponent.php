<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\AdjustExcusedMissedTurnsForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class AdjustExcusedMissedTurnsComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Adjust Excused Missed Turns';
    protected string $submitText = 'Adjust';
    protected string $formClass = AdjustExcusedMissedTurnsForm::class;
}