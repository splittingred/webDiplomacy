<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Forms\Admin\Games\ChangePhaseLengthForm;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePhaseLengthComponent extends BaseGameFormModalComponent
{
    protected string $title = 'Change Phase Length';
    protected string $submitText = 'Change';
    protected string $formClass = ChangePhaseLengthForm::class;

    public function getDefaultValues(): array
    {
        return array_merge(parent::getDefaultValues(), [
            'phase_length' => $this->game->phase->minutes,
        ]);
    }
}