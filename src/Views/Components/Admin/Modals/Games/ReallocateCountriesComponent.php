<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ReallocateCountriesComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/reallocate_countries.twig';
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
        string $modalTitle = 'Reallocate Countries',
        string $modalId = 'admin-game-reallocate-countries',
        string $modalSubmitText = 'Change',
        string $modalCloseText = 'Close'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/members/reallocate";
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
            'countries' => $this->game->countries,
        ];
    }
}