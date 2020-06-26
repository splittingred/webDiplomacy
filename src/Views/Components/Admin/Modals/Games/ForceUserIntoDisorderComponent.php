<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ForceUserIntoDisorderForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceUserIntoDisorderComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Force User into Civil Disorder';
    protected string $submitText = 'Force User';
    protected string $formClass = ForceUserIntoDisorderForm::class;
}