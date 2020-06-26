<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ChangePressTypeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePressTypeComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Change Press Type';
    protected string $closeText = 'Change';
    protected string $formClass = ChangePressTypeForm::class;
}