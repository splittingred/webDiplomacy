<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ResetProcessTimeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ResetProcessTimeComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Reset Process Time';
    protected string $submitText = 'Reset';
    protected string $formClass = ResetProcessTimeForm::class;
}