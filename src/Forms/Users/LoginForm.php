<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
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
        $authService = new Service();
        $result = $authService->login(
            $this->request->get('username', '', Request::TYPE_POST),
            $this->request->get('password', '', Request::TYPE_POST)
        );
        if ($result->successful()) {
            $this->redirectRelative('/');
            close();
        } else {
            $this->setPlaceholder('notice', $result->getValue()->getMessage());
        }
    }
}