<?php

namespace Diplomacy\Forms\Fields;

use Diplomacy\Views\Renderer;

class NumberField extends Field
{
    protected string $template = 'forms/fields/number.twig';

    public int $size;
    public int $min;
    public int $max;
    public int $step;

    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        parent::__construct($renderer, $name, $value, $attributes, $errors);
        $this->size = array_key_exists('size', $attributes) ? (int)$attributes['size'] : 7;
        $this->min = array_key_exists('min', $attributes) ? (int)$attributes['min'] : 0;
        $this->max = array_key_exists('max', $attributes) ? (int)$attributes['max'] : 100;
        $this->step = array_key_exists('step', $attributes) ? (int)$attributes['step'] : 1;
    }
}