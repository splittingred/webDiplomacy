<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ResetProcessTimeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ResetProcessTimeComponent extends BaseGameFormModalComponent
{
    protected $title = 'Reset Process Time';
    protected $submitText = 'Reset';
    protected $formClass = ResetProcessTimeForm::class;
}