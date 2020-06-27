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
    protected string $template = 'admin/modals/modal_form.twig';
    protected string $id = '';
    protected string $title = '';
    protected string $submitText = 'Submit';
    protected string $closeText = 'Close';

    protected string $formClass;
    protected ?BaseForm $form;
    protected array $defaultValues = [];

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
            /** @var BaseForm $form */
            $form = new $formClass($this->getRequest(), $this->renderer(), $validationFactory, $this->getDefaultValues(), $this->getFormPlaceholders());
            $form->submitBtn = false;
            return $form;
        } catch (\Exception $e) {}
    }
}