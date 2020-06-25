<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ReallocateCountriesForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ReallocateCountriesComponent extends BaseGameFormModalComponent
{
    protected $title = 'Reallocate Countries';
    protected $submitText = 'Change';
    protected $formClass = ReallocateCountriesForm::class;
}