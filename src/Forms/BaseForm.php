<?php

namespace Diplomacy\Forms;

use Diplomacy\Services\Request;
use Diplomacy\Utilities\HasPlaceholders;
use Diplomacy\Views\Renderer;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Validator;

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
    /** @var string $id */
    protected $id = '';
    /** @var string */
    protected $nestedIn = '';
    /** @var string */
    protected $requestType = Request::TYPE_POST;
    /** @var array */
    protected $onSubmissionCallbacks = [];

    /** @var Validator  */
    protected $validator;
    /** @var array $validationRules */
    protected $validationRules = [];
    /** @var array $validationMessages */
    protected $validationMessages = [];
    /** @var array $validationCustomAttributes */
    protected $validationCustomAttributes = [];

    /** @var FieldFactory $fieldFactory */
    protected $fieldFactory;
    /** @var array $fieldObjects */
    protected $fieldObjects = [];
    /** @var string $fieldPrefix */
    protected $fieldPrefix = '';

    /**
     * @param Request $request
     * @param Renderer $renderer
     * @param ValidationFactory $validatorFactory
     * @param array $defaultValues
     */
    public function __construct(Request $request, Renderer $renderer, ValidationFactory $validatorFactory, array $defaultValues = [])
    {
        $this->request = $request;
        $this->renderer = $renderer;
        $this->setUp();
        $this->validator = $validatorFactory->make(array_keys($this->fields), $this->validationRules, $this->validationMessages, $this->validationCustomAttributes);
        $clsNameLength = strlen(self::class);
        $defaultFieldPrefix = substr(self::class, 0, $clsNameLength < 4 ? $clsNameLength : 4);
        $fieldPrefix = !empty($this->fieldPrefix) ? $this->fieldPrefix : $defaultFieldPrefix;
        $this->fieldFactory = new FieldFactory($this->renderer, $fieldPrefix, $this->nestedIn);
        $this->buildFields($defaultValues);
    }

    public function setUp() : void
    {

    }

    /**
     * @param callable $callback
     * @return BaseForm
     */
    public function onSubmit(callable $callback)
    {
        $this->onSubmissionCallbacks[] = $callback;
        return $this;
    }

    public function beforeRender() { }
    public function afterRender() { }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return !$this->validator->fails();
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $data = [];
        foreach (array_keys($this->fields) as $key) {
            $errors = $this->validator->errors()->get($key, '<div class="invalid-feedback">:message</div>');
            $data[$key] = $errors;
        }
        return $data;
    }

    /**
     * @return string
     */
    public function render() : string
    {
        if ($this->isSubmitted() && $this->validate()) {
            $this->handleSubmit();
        }

        $this->beforeRender();
        $this->setPlaceholders([
            'id'                => $this->id,
            'fields'            => $this->getFields(),
            'submitFieldName'   => $this->submitFieldName,
            'nestedIn'          => $this->nestedIn,
        ]);
        $output = $this->renderer->render($this->template, $this->placeholders);
        $this->afterRender();
        return $output;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fieldObjects;
    }

    /**
     * Handle form submissions. You may override this in extended classes.
     *
     * @return BaseForm
     */
    protected function handleSubmit(): BaseForm
    {
        foreach ($this->onSubmissionCallbacks as $callback) {
            $callback($this);
        }
        return $this;
    }

    /**
     * Get the values for the form, falling back to defaults
     *
     * @return array
     */
    public function getValues() : array
    {
        return array_map(function($f) {
            return $f->value;
        }, $this->fieldObjects);
    }

    /**
     * @param string $k
     * @return mixed|null
     */
    public function getValue(string $k)
    {
        return $this->fieldObjects[$k]->value;
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
    protected function getFieldNames() : array
    {
        return array_keys($this->fields);
    }

    /**
     * @param string $path
     * @return BaseForm
     */
    public function redirectRelative(string $path = '/') : BaseForm
    {
        $baseUrl = rtrim(\Config::$url, '/');
        $path = ltrim($path, '/');
        header("Location: {$baseUrl}/{$path}");
        return $this;
    }

    /**
     * Build the field objects
     *
     * @param array $defaultValues
     * @return BaseForm
     */
    protected function buildFields(array $defaultValues = []): BaseForm
    {
        $values = $defaultValues;
        $errors = [];
        if ($this->isSubmitted()) {
            $postValues = $this->request->getParameters($this->requestType);
            if (!empty($this->nestedIn)) {
                $postValues = array_key_exists($this->nestedIn, $postValues) ? $postValues[$this->nestedIn] : [];
            }
            $errors = $this->getErrors();
            $values = array_merge($defaultValues, $postValues);
        }
        $this->fieldObjects = $this->fieldFactory->build($this->id, $this->fields, $values, $errors);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}