<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ChangePressTypeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePressTypeComponent extends BaseGameFormModalComponent
{
    protected $title = 'Change Press Type';
    protected $closeText = 'Change';
    protected $formClass = ChangePressTypeForm::class;
}