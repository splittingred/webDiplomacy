<?php

namespace Diplomacy\Forms;

use Diplomacy\Services\Request;
use Diplomacy\Utilities\HasPlaceholders;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms
 */
abstract class BaseForm
{
    use HasPlaceholders;

    /** @var Request $request */
    protected $request;
    /** @var Renderer $renderer */
    protected $renderer;
    /** @var string $template */
    protected $template;
    /** @var array */
    protected $fields = [];
    /** @var string */
    protected $submitFieldName = 'submit';
    /** @var string */
    protected $requestType = Request::TYPE_POST;
    /** @var array */
    protected $onSubmissionCallbacks = [];

    /**
     * @param Request $request
     * @param Renderer $renderer
     */
    public function __construct(Request $request, Renderer $renderer)
    {
        $this->request = $request;
        $this->renderer = $renderer;
    }

    public function onSubmit(callable $callback)
    {
        $this->onSubmissionCallbacks[] = $callback;
    }

    public function beforeRender() { }

    /**
     * @return string
     */
    public function render() : string
    {
        if ($this->isSubmitted()) {
            foreach ($this->onSubmissionCallbacks as $callback) {
                $callback($this);
            }
        }

        $this->beforeRender();
        $this->setPlaceholder('values', $this->getValues());
        return $this->renderer->render($this->template, $this->placeholders);
    }

    /**
     * Get the values for the form, falling back to defaults
     *
     * @return array
     */
    public function getValues() : array
    {
        if (!$this->isSubmitted()) return $this->getFields();

        $postValues = $this->request->getParameters($this->requestType);
        $acceptedKeys = $this->getFieldNames();
        return array_filter($postValues, function ($key) use ($acceptedKeys) { return in_array($key, $acceptedKeys); }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string $k
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getValue(string $k, $default = null)
    {
        $values = $this->getValues();
        return array_key_exists($k, $values) ? $values[$k] : $default;
    }

    /**
     * @return bool
     */
    public function isSubmitted() : bool
    {
        return !$this->request->isEmpty($this->submitFieldName, $this->requestType);
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    protected function getFieldNames() : array
    {
        return array_keys($this->getFields());
    }
}