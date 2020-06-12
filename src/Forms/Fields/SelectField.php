<?php

namespace Diplomacy\Forms\Fields;

use Diplomacy\Views\Renderer;

class SelectField extends Field
{
    protected $template = 'forms/fields/select.twig';

    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('options', $attributes)) $attributes['options'] = $this->getDefaultOptions();
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function showAll(): bool
    {
        return !empty($this->attributes['showAll']);
    }

    /**
     * @return string
     */
    public function showAllValue(): string
    {
        return array_key_exists('showAllValue', $this->attributes) ? $this->attributes['showAllValue'] : '-1';
    }
}