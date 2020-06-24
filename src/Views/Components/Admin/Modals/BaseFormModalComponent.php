<?php
namespace Diplomacy\Views\Components\Admin\Modals;

use Diplomacy\Views\Components\BaseComponent;

/**
 * Abstract class for rendering a form in a modal
 *
 * @package Diplomacy\Views\Components\Admin
 */
abstract class BaseFormModalComponent extends BaseComponent
{
    /** @var string $modalId */
    protected $modalId = '';
    /** @var string $modalTitle */
    protected $modalTitle = '';
    /** @var string $modalSubmitText */
    protected $modalSubmitText = 'Submit';
    /** @var string $modalCloseText */
    protected $modalCloseText = 'Close';
    /** @var string $formAction */
    protected $formAction = '';
    /** @var string $formMethod */
    protected $formMethod = 'post';

    /**
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->formAction;
    }

    /**
     * @return string
     */
    public function getFormMethod(): string
    {
        return $this->formMethod;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $form = parent::__toString();
        return $this->renderer()->render('admin/modals/modal_form.twig', [
            'title'         => $this->modalTitle,
            'form'          => $form,
            'id'            => $this->modalId,
            'submit_text'   => $this->modalSubmitText,
            'close_text'    => $this->modalCloseText,
            'action'        => $this->getFormAction(),
            'method'        => $this->getFormMethod(),
        ]);
    }
}