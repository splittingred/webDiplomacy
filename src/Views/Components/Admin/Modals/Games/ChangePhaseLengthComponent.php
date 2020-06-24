<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePhaseLengthComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/change_phase_length.twig';
    /** @var Game $game */
    protected $game;

    /**
     * @param Game $game
     * @param string $modalTitle
     * @param string $modalId
     * @param string $modalSubmitText
     * @param string $modalCloseText
     * @throws \Exception
     */
    public function __construct(
        Game $game,
        string $modalTitle = 'Change Phase Length',
        string $modalId = 'admin-game-change-phase-length',
        string $modalSubmitText = 'Change',
        string $modalCloseText = 'Close'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/processing/phase-length";
        $this->formMethod = 'post';
        $this->game = $game;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->modalSubmitText = $modalSubmitText;
        $this->modalCloseText = $modalCloseText;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'phase_lengths' => OptionsService::getPhaseLengths(),
        ];
    }
}