<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Services\Games\OptionsService;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePhaseLengthComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/change_phase_length.twig';
    protected $modalId = 'admin-game-change-phase-length';
    protected $modalTitle = 'Change Phase Length';
    protected $modalSubmitText = 'Change';
    protected $formAction = '/admin/games/:game_id/processing/phase-length';

    /**
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'phase_lengths' => OptionsService::getPhaseLengths(),
        ]);
    }
}