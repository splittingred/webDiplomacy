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
    protected $name = '';
    /** @var string $id */
    public $id = '';
    /** @var string $nestedIn */
    protected $nestedIn = '';
    /** @var string $requestType */
    protected $requestType = Request::TYPE_POST;
    /** @var array $onSubmissionCallbacks */
    protected $onSubmissionCallbacks = [];
    /** @var string $action */
    protected $action = '';
    /** @var string $formCls */
    protected $formCls = '';

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
        $clsNameLength = strlen(self::class);
        $defaultFieldPrefix = substr(self::class, 0, $clsNameLength < 4 ? $clsNameLength : 4);
        $fieldPrefix = !empty($this->fieldPrefix) ? $this->fieldPrefix : $defaultFieldPrefix;
        $this->fieldFactory = new FieldFactory($this->renderer, $fieldPrefix, $this->nestedIn);
        $this->buildFields($validatorFactory, $defaultValues);
    }

    public function setUp(): void
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
            'name'              => $this->name,
            'action'            => $this->getAction(),
            'method'            => $this->requestType,
            'formCls'           => $this->formCls,
            'nestedIn'          => $this->nestedIn,
            'current_uri'       => $this->request->getCurrentUri(),
        ]);
        $notice = $this->getPlaceholder('notice', false);
        $output = $this->renderer->render('common/form_wrapper.twig', [
            'id' => $this->id,
            'name' => $this->name,
            'action' => $this->getAction(),
            'method' => $this->requestType,
            'notice' => !empty($notice) ? $this->renderer->render('common/notice.twig', ['notice' => $notice]) : '',
            'cls' => $this->getFormCssCls(),
            'content' => $this->renderer->render($this->template, $this->placeholders),
        ]);
        $this->afterRender();
        return $output;
    }

    /**
     * @return string
     */
    public function getFormCssCls(): string
    {
        return !empty($this->formCls) ? $this->formCls : 'form';
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return !empty($this->action) ? $this->action : $this->request->getCurrentUri();
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fieldObjects;
    }

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return $this->validationRules;
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
        return !empty($this->name) && $this->request->get('form', '', $this->requestType) == $this->name;
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
     * Build the field objects.
     *
     * Field default inheritance (last takes precedence):
     * -> Defined on form fields var -> passed into this method -> submitted via Request
     *
     * @param ValidationFactory $validatorFactory
     * @param array $defaultValues
     * @return BaseForm
     */
    protected function buildFields(ValidationFactory $validatorFactory, array $defaultValues = []): BaseForm
    {
        $errors = [];
        $values = array_merge(array_map(function($f) {
            return array_key_exists('default', $f) ? $f['default'] : null;
        }, $this->fields), $defaultValues);


        if ($this->isSubmitted()) {
            $postValues = $this->request->getParameters($this->requestType);
            if (!empty($this->nestedIn)) {
                $postValues = array_key_exists($this->nestedIn, $postValues) ? $postValues[$this->nestedIn] : [];
            }
            $derivedValues = array_merge($values, $postValues);
            $this->validator = $validatorFactory->make($derivedValues, $this->validationRules, $this->validationMessages, $this->validationCustomAttributes);
            $errors = $this->getErrors();
            $values = array_merge($defaultValues, $postValues);
        } else {
            $this->validator = $validatorFactory->make($values, $this->validationRules, $this->validationMessages, $this->validationCustomAttributes);
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

    /**
     * @return $this
     */
    public function redirectToSelf(): BaseForm
    {
        $this->redirectRelative($this->request->getCurrentUri());
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setNotice($message) : BaseForm
    {
        $this->setPlaceholder('notice', $message);
        return $this;
    }

    /**
     * @return string
     */
    public function getNotice(): string
    {
        return $this->getPlaceholder('notice', '');
    }
}