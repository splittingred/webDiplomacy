<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ProcessGameNowComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/process_game_now.twig';
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
        string $modalTitle = 'Process Game Now',
        string $modalId = 'admin-game-process-now',
        string $modalSubmitText = 'Process Now',
        string $modalCloseText = 'Close'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/processing";
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
        ];
    }
}