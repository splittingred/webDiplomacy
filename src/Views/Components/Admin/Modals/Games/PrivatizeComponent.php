<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PrivatizeComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/privatize.twig';
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
        string $modalTitle = 'Make Private',
        string $modalId = 'admin-game-privatize',
        string $modalSubmitText = 'Privatize'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/privatize";
        $this->game = $game;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->modalSubmitText = $modalSubmitText;
    }
}