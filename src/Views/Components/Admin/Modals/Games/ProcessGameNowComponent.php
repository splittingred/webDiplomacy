<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ProcessGameNowForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ProcessGameNowComponent extends BaseGameFormModalComponent
{
    protected $title = 'Process Game Now';
    protected $submitText = 'Process Now';
    protected $formClass = ProcessGameNowForm::class;
}