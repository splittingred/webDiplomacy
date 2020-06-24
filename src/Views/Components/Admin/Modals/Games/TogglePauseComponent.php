<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class TogglePauseComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/toggle_pause.twig';
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
        string $modalTitle = 'Toggle Game Pause Status',
        string $modalId = 'admin-game-toggle-pause',
        string $modalSubmitText = 'Toggle Pause',
        string $modalCloseText = 'Close'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/processing/toggle-pause";
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

        ];
    }
}