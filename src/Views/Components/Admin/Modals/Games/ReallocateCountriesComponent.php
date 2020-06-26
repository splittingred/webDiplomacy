<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ReallocateCountriesForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ReallocateCountriesComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Reallocate Countries';
    protected string $submitText = 'Change';
    protected string $formClass = ReallocateCountriesForm::class;
}