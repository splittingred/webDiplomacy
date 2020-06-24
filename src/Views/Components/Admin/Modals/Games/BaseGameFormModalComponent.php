<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\Admin\Modals\BaseFormModalComponent;

/**
 * Abstract class for rendering an admin form dealing with a game
 *
 * @package Diplomacy\Views\Components\Admin
 */
abstract class BaseGameFormModalComponent extends BaseFormModalComponent
{
    /** @var Game $game */
    protected $game;

    /**
     * @param Game $game
     * @param bool $showLink
     */
    public function __construct(Game $game, bool $showLink = true)
    {
        parent::__construct(
            $showLink,
            $this->modalTitle,
            $this->modalId
        );
        $this->game = $game;
        $this->formAction = str_replace(':game_id', $this->game->id, $this->formAction);
    }

    /**
     * @return array|Game[]
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
        ];
    }
}