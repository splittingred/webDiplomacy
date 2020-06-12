<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class JoinPeriodSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Time to Fill Game: (5 min - 14 days)';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 0;
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    public function getDefaultOptions(): array
    {
        return OptionsService::getJoinPeriods();
    }
}