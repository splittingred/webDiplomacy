<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\PrivatizeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PrivatizeComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Make Private';
    protected string $submitText = 'Privatize';
    protected string $formClass = PrivatizeForm::class;
}