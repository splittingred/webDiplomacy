<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceUserIntoDisorderComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/force_user_into_disorder.twig';
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
        string $modalTitle = 'Force User into Civil Disorder',
        string $modalId = 'admin-game-force-user-cd',
        string $modalSubmitText = 'Force User'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/users/disorders";
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