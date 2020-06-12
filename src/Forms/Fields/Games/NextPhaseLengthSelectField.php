<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class NextPhaseLengthSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Phase Length After Swap';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 1440;
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    protected function getDefaultOptions(): array
    {
        return OptionsService::getNextPhaseLengths();
    }
}