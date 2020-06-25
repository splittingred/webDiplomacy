<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\TogglePauseForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class TogglePauseComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $title = 'Toggle Game Pause Status';
    protected $submitText  = 'Toggle Pause';
    protected $formClass = TogglePauseForm::class;
}