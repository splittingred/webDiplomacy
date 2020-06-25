<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\PrivatizeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PrivatizeComponent extends BaseGameFormModalComponent
{
    protected $title = 'Make Private';
    protected $submitText = 'Privatize';
    protected $formClass = PrivatizeForm::class;
}