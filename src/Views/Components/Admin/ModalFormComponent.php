<?php

namespace Diplomacy\Views\Components\Admin;

use Diplomacy\Views\Components\BaseComponent;

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
    /** @var string $form */
    protected $form;
    /** @var string $submitText */
    protected $submitText;
    /** @var string $closeText */
    protected $closeText;

    /**
     * @param $title
     * @param $form
     * @param string $id
     * @param string $submitText
     * @param string $closeText
     * @throws \Exception
     */
    public function __construct(string $title, string $form, string $id = '', string $submitText = 'Submit', string $closeText = 'Close')
    {
        $this->title = $title;
        $this->form = $form;
        $this->id = !empty($id) ? $id : 'form-' . random_bytes(10);
        $this->submitText = $submitText;
        $this->closeText = $closeText;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title'         => $this->title,
            'form'          => $this->form,
            'id'            => $this->id,
            'submit_text'   => $this->submitText,
            'close_text'    => $this->closeText,
        ];
    }
}