<?php

namespace Diplomacy\Forms\Users\Messages;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class SendPrivateMessageForm extends BaseForm
{
    public string $id = 'new-user-pm';
    protected string $template = 'forms/users/messages/send_pm.twig';
    protected string $requestType = Request::TYPE_POST;
    protected string $action = '/users/messages#messagebox';
    protected string $formCls = '';
    protected string $name = 'users-new-pm';
    protected string $nestedIn = 'new_user_pm';
    public string $submitBtnText = 'Send Message';
    protected array $fields = [
        'user_id' => [
            'type' => 'hidden',
        ],
        'message' => [
            'type' => 'textarea',
            'label' => 'Message',
            'helpText' => 'Send a private message to the user',
            'inputAttributes' => [
                'rows' => 4,
                'style' => 'width: 100%',
            ]
        ],
    ];

    protected array $validationRules = [
        'user_id'   => 'required',
        'message'   => 'required',
    ];

    public function handleSubmit(): BaseForm
    {
        // TODO: handle PM submissions
        return parent::handleSubmit();
    }
}