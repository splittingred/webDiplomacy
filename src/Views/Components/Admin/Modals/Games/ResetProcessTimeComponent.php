<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ResetProcessTimeComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/reset_process_time.twig';
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
        string $modalTitle = 'Reset Process Time',
        string $modalId = 'admin-game-reset-process-time',
        string $modalSubmitText = 'Reset'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/processing/reset";
        $this->game = $game;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->modalSubmitText = $modalSubmitText;
    }
}