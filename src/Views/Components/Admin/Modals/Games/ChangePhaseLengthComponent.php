<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ChangePhaseLengthForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePhaseLengthComponent extends BaseGameFormModalComponent
{
    protected $title = 'Change Phase Length';
    protected $submitText = 'Change';
    protected $formClass = ChangePhaseLengthForm::class;
}