<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\TogglePauseForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class TogglePauseComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Toggle Game Pause Status';
    protected string $submitText  = 'Toggle Pause';
    protected string $formClass = TogglePauseForm::class;
}