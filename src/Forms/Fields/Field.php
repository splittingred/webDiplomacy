<?php

namespace Diplomacy\Forms\Fields;

use Diplomacy\Views\Renderer;
use function PHPUnit\Framework\callback;

/**
 * Represents a generic field in a form
 *
 * @package Diplomacy\Forms\Fields
 */
abstract class Field
{
    /** @var string */
    const BASE_CSS_CLS = 'form-control';
    /** @var string $template */
    protected string $template = 'forms/fields/text.twig';

    public string $id;
    public string $name;
    public string $label;
    public string $default;
    public string $cssCls = self::BASE_CSS_CLS;
    public $value = '';
    public string $helpText = '';
    public array $attributes = [];
    public array $errors = [];
    protected Renderer $renderer;

    public function __construct(Renderer $renderer, string $name, $value, array $attributes = [], array $errors = [])
    {
        $this->name = $name;
        $this->label = array_key_exists('label', $attributes) ? $attributes['label'] : ucfirst($name);
        $this->renderer = $renderer;
        $this->default = array_key_exists('default', $attributes) ? $attributes['default'] : '';
        $this->helpText = array_key_exists('helpText', $attributes) ? $attributes['helpText'] : '';
        $this->value = $value;
        $this->errors = $errors;
        $this->cssCls .= !empty($errors) ? ' is-invalid' : '';
        $idPrefix = !empty($attributes['idPrefix']) ? $attributes['idPrefix'] : substr(sha1(time()), 0, 5);
        $this->id = $idPrefix.'-'.str_replace(['_', '.', ' '], '-', strtolower($name));
        $this->attributes = $attributes;
    }

    /**
     * @param mixed $default
     * @return Field
     */
    public function setDefault($default): Field
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): Field
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function errorMessages(): string
    {
        return is_array($this->errors) ? join('', $this->errors) : '';
    }

    /**
     * @return string
     */
    public function fieldName(): string
    {
        return !empty($this->attributes['nestedIn']) ? "{$this->attributes['nestedIn']}[{$this->name}]" : $this->name;
    }

    /**
     * @return array
     */
    public function inputAttributes(): array
    {
        return array_key_exists('input', $this->attributes) ? $this->attributes['input'] : [];
    }

    /**
     * @return string
     */
    public function helpIcon(): string
    {
        if (!array_key_exists('helpIcon', $this->attributes)) return '';

        $help = $this->attributes['helpIcon'];
        $attrs = array_merge([
            'id' => $this->id.'-help',
            'title' => 'Help',
            'text' => '',
            'toggle' => 'popover',
            'trigger' => 'hover',
            'alt' => 'Help',
            'delay' => false,
        ], $this->attributes['helpIcon']);

        if (array_key_exists('text', $help)) {
            $text = $this->attributes['helpIcon']['text'];
            if (is_callable($text)) $attrs['text'] = $text();
        }
        return $this->renderer->render('forms/fields/help_icon.twig', $attrs);
    }

    /**
     * @return array
     */
    public function options(): array
    {
        if (!array_key_exists('options', $this->attributes)) return [];

        $opts = $this->attributes['options'];
        if (is_callable($opts)) {
            return $opts();
        }

        return $opts;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->renderer->render($this->template, [
            'field' => $this,
        ]);
    }
}