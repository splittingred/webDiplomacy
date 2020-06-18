<?php

namespace Diplomacy\Forms\Users\Messages;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class SendPrivateMessageForm extends BaseForm
{
    protected $template = 'forms/users/messages/send_pm.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'new_user_pm';
    protected $nestedIn = 'new_user_pm';
    protected $id = 'new-user-pm';
    protected $fields = [
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

    protected $validationRules = [
        'user_id'   => 'required',
        'message'   => 'required',
    ];

    public function handleSubmit(): BaseForm
    {
        // TODO: handle PM submissions
        return parent::handleSubmit();
    }
}