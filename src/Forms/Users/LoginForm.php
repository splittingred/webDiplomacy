<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class LoginForm extends BaseForm
{
    protected $template = 'forms/users/login.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'username';
    protected $fields = [
        'username' => '',
        'password' => '',
    ];

    public function handleSubmit()
    {
        die();
    }
}