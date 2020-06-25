<?php

namespace Diplomacy\Views\Components\Admin;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Views\Components\BaseComponent;
use Illuminate\Validation\Factory as ValidatorFactory;

/**
 * Wrapper component for an admin form modal
 *
 * @package Diplomacy\Views\Components\Admin
 */
class ModalFormComponent extends BaseComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/modal_form.twig';
    /** @var string $id */
    protected $id;
    /** @var string $title */
    protected $title;
    /** @var string $submitText */
    protected $submitText = 'Submit';
    /** @var string $closeText */
    protected $closeText = 'Close';

    /** @var string $formClass */
    protected $formClass;
    /** @var BaseForm $form */
    protected $form;
    /** @var array $defaultValues */
    protected $defaultValues = [];

    public function __construct()
    {
        $this->id = $this->generateId();
        $this->form = $this->buildForm();
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    /**
     * @return array
     */
    public function getFormPlaceholders(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title'          => $this->title,
            'form'           => (string)$this->form,
            'form_id'        => $this->form->id,
            'id'             => $this->id,
            'submit_text'    => $this->submitText,
            'close_text'     => $this->closeText,
        ];
    }

    /**
     * @return string
     */
    public function generateId(): string
    {
        return $this->getReferenceId();
    }

    /**
     * @return string
     */
    private function getReferenceId(): string
    {
        return strtolower(str_replace('Component', '',
            str_replace('\\', '-',
            str_replace('Diplomacy\Views\Components\Admin\\', '', get_class($this)))));
    }

    /**
     * @return BaseForm
     */
    private function buildForm(): BaseForm
    {
        global $app;
        try {
            $validationFactory = $app->make('validation.factory');
            $formClass = $this->formClass;
            $form = new $formClass($this->getRequest(), $this->renderer(), $validationFactory, $this->getDefaultValues());
            $form->setPlaceholders($this->getFormPlaceholders());
            return $form;
        } catch (\Exception $e) {}
    }
}