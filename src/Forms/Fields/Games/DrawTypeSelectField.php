<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * Show a select box for whether draw votes are shown
 *
 * @package Diplomacy\Forms\Fields
 */
class DrawTypeSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Draw votes';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 'draw-votes-public';
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    /**
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return [
            ['value' => 'draw-votes-public', 'text' => 'Show draw votes'],
            ['value' => 'draw-votes-hidden', 'text' => 'Hide draw votes'],
        ];
    }
}