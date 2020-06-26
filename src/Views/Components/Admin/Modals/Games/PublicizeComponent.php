<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\PublicizeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PublicizeComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Make Public';
    protected string $submitText = 'Publicize';
    protected string $formClass = PublicizeForm::class;
}