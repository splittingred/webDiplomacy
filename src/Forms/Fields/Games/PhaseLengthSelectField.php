<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class PhaseLengthSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Phase length (5 min - 10 days)';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 300;
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    protected function getDefaultOptions(): array
    {
        return OptionsService::getPhaseLengths();
    }

    public function helpIcon(): string
    {
        $this->attributes['helpIcon'] = [
            'title' => 'Phase Length',
            'text' => 'How long each phase of the game will last in hours. Longer phase hours means a slow game with more time to talk. Shorter phases require players be available to check the game frequently.',
        ];
        return parent::helpIcon();
    }
}