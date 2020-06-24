<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class PressTypeSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Game Messaging';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 'Regular';
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    public function helpIcon(): string
    {
        $this->attributes['helpIcon'] = [
            'title' => 'Press Type',
            'text'  => 'The type of messaging allowed in a game.<br />
                    All: Global and Private Messaging allowed.<br />
                    Global Only: Only Global Messaging allowed.<br />
                    None: No messaging allowed.<br />
                    Rulebook: No messaging allowed during build and retreat phases.',
        ];
        return parent::helpIcon();
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return OptionsService::getPressTypes();
    }
}