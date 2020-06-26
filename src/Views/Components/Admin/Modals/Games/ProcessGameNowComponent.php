<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ProcessGameNowForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ProcessGameNowComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Process Game Now';
    protected string $submitText = 'Process Now';
    protected string $formClass = ProcessGameNowForm::class;
}