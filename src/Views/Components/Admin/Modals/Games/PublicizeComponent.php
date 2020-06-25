<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\PublicizeForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PublicizeComponent extends BaseGameFormModalComponent
{
    protected $title = 'Make Public';
    protected $submitText = 'Publicize';
    protected $formClass = PublicizeForm::class;
}