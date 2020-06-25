<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ForceUserIntoDisorderForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceUserIntoDisorderComponent extends BaseGameFormModalComponent
{
    protected $title = 'Force User into Civil Disorder';
    protected $submitText = 'Force User';
    protected $formClass = ForceUserIntoDisorderForm::class;
}