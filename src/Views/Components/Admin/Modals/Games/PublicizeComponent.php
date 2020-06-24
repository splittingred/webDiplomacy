<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PublicizeComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/publicize.twig';
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
        string $modalTitle = 'Make Public',
        string $modalId = 'admin-game-publicize',
        string $modalSubmitText = 'Publicize'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/publicize";
        $this->game = $game;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->modalSubmitText = $modalSubmitText;
    }
}