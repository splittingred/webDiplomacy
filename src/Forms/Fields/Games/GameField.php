<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Renderer;


/**
 * Show a select box for whether draw votes are shown
 *
 * @package Diplomacy\Forms\Fields
 */
class GameField extends SelectField
{
    protected Game $game;

    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        $this->game = $attributes['game'];
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Game';
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    /**
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return [
            ['value' => $this->game->id, 'text' => $this->game->name],
        ];
    }
}
