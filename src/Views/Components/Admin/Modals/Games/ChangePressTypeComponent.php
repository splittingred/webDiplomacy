<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePressTypeComponent extends BaseFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/change_press_type.twig';
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
        string $modalTitle = 'Change Press Type',
        string $modalId = 'admin-game-change-press-type',
        string $modalSubmitText = 'Change',
        string $modalCloseText = 'Close'
    )
    {
        $this->formAction = "/admin/games/{$game->id}/press-type";
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
        $pressTypes = OptionsService::getPressTypes((string)$this->game->pressType);
        foreach ($pressTypes as &$pressType) {
            $pressType['selected'] = (string)$this->game->pressType == $pressType['value'];
        }
        return [
            'game' => $this->game,
            'press_types' => $pressTypes,
        ];
    }
}