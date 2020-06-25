<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ForceDrawForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceDrawComponent extends BaseGameFormModalComponent
{
    protected $title = 'Force Draw';
    protected $submitText = 'Force Draw';
    protected $formClass = ForceDrawForm::class;
}