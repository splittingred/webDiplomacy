<?php

namespace Diplomacy\Forms;

use Diplomacy\Forms\Fields\Field;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms
 */
class FieldFactory
{
    protected $renderer;
    protected $fieldPrefix;
    protected $nestedIn;

    public function __construct(Renderer $renderer, string $fieldPrefix = '', string $nestedIn = '')
    {
        $this->renderer = $renderer;
        $this->fieldPrefix = $fieldPrefix;
        $this->nestedIn = $nestedIn;
    }

    /**
     * @param string $formId
     * @param array $fields
     * @param array $postValues
     * @param array $errors
     * @return array<Field>
     */
    public function build(string $formId, array $fields, array $postValues, array $errors = [])
    {
        $data = [];
        foreach ($fields as $key => $attributes) {
            $attributes['nestedIn'] = $this->nestedIn;
            $type = array_key_exists('type', $attributes) ? $attributes['type'] : 'text';
            $default = array_key_exists('default', $attributes) ? $attributes['default'] : '';
            $value = array_key_exists($key, $postValues) ? $postValues[$key] : $default;
            $fieldErrors = array_key_exists($key, $errors) ? $errors[$key] : [];

            if (!array_key_exists('idPrefix', $attributes)) $attributes['idPrefix'] = $formId;

            $class = $this->getFieldClass($type);
            $data[$key] = new $class($this->renderer, $key, $value, $attributes, $fieldErrors);
        }
        return $data;
    }

    /**
     * @param $type
     * @return string
     */
    protected function getFieldClass($type): string
    {
        $ucType = ucfirst(strtolower($type));
        return "\\Diplomacy\\Forms\\Fields\\${ucType}Field";
    }
}