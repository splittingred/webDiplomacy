<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ForceDrawForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceDrawComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Force Draw';
    protected string $submitText = 'Force Draw';
    protected string $formClass = ForceDrawForm::class;
}