<?php

namespace Diplomacy\Forms\Fields;

use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class PercentSelectField extends SelectField
{
    public $min;
    public $max;
    public $step;

    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        $this->min = array_key_exists('min', $attributes) ? (int)$attributes['min'] : 0;
        $this->max = array_key_exists('max', $attributes) ? (int)$attributes['max'] : 100;
        $this->step = array_key_exists('step', $attributes) ? (int)$attributes['step'] : 10;
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    protected function getDefaultOptions(): array
    {
        $values = [];
        for ($i = $this->min; $i <= $this->max; $i += $this->step) {
            $values[] = [
                'value' => $i,
                'text' => "{$i}%",
            ];
        }
        return $values;
    }
}