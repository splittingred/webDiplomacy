<?php

namespace Diplomacy\Forms\Fields;

use Diplomacy\Views\Renderer;

class CheckboxesField extends Field
{
    public $template = 'forms/fields/checkboxes.twig';

    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('options', $attributes)) $attributes['options'] = $this->getDefaultOptions();
        parent::__construct($renderer, $name, $value, $attributes, $errors);
        $this->attributes['options'] = $this->adjustOptions($this->attributes['options']);
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

    /**
     * @param array $options
     * @return array
     */
    private function adjustOptions(array $options): array
    {
        $idx = 0;
        $newOpts = [];
        foreach ($options as $option) {
            $opt = $option;
            $opt['name'] = !empty($this->attributes['nestedIn']) ? "{$this->attributes['nestedIn']}[{$this->name}][$idx]" : "{$this->name}[{$idx}]";
            $opt['idx'] = $idx;
            $opt['id'] = $this->id.'-'.$idx;
            $newOpts[] = $opt;
            $idx++;
        }
        return $newOpts;
    }
}