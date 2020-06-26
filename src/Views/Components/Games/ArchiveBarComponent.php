<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Views\Components\BaseComponent;

/**
 * Renders archive links for a game
 *
 * @package Diplomacy\Views\Components\Games
 */
class ArchiveBarComponent extends BaseComponent
{
    /** @var string $template */
    protected string $template = 'games/board/archive_bar.twig';
    /** @var int $gameId */
    protected int $gameId;

    /**
     * @param int $gameId
     */
    public function __construct(int $gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game_id' => $this->gameId,
        ];
    }
}