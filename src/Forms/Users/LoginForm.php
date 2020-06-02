<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Request;
use Diplomacy\Views\Renderer;

class LoginForm extends BaseForm
{
    protected $template = 'forms/users/login.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'username';
    protected $fields = [
        'username' => '',
        'password' => '',
    ];
    /** @var Service */
    protected $authService;

    public function setUp(): void
    {
        $this->authService = new Service();
        parent::setUp();
    }

    public function handleSubmit()
    {
        $result = $this->authService->login(
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