<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\Admin\ModalFormComponent;

/**
 * Abstract class for rendering an admin form dealing with a game
 *
 * @package Diplomacy\Views\Components\Admin
 */
abstract class BaseGameFormModalComponent extends ModalFormComponent
{
    /** @var Game $game */
    protected Game $game;

    /**
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'game' => $this->game,
        ]);
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [
            'game_id' => $this->game->id,
        ];
    }

    /**
     * @return array
     */
    public function getFormPlaceholders(): array
    {
        return array_merge(parent::getFormPlaceholders(), [
            'game' => $this->game,
        ]);
    }
}