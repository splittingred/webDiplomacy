<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class AdjustExcusedMissedTurnsComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/adjust_excused_missed_turns.twig';
    /** @var Game $game */
    protected $game;

    /**
     * @param Game $game
     * @param string $modalTitle
     * @param string $modalId
     * @param string $modalSubmitText
     * @throws \Exception
     */
    public function __construct(
        Game $game,
        string $modalTitle = 'Adjust Excused Missed Turns',
        string $modalId = 'admin-game-adjust-excused-missed-turns',
        string $modalSubmitText = 'Adjust'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/users/excused-missed-turns";
        $this->formMethod = 'post';
        $this->game = $game;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->modalSubmitText = $modalSubmitText;
    }

    public function attributes(): array
    {
        return [
            'game' => $this->game,
        ];
    }
}