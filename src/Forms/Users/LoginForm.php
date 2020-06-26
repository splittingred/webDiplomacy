<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Request;
use Diplomacy\Views\Renderer;

class LoginForm extends BaseForm
{
    protected string $template = 'forms/users/login.twig';
    protected string $requestType = Request::TYPE_POST;
    protected string $action = '/users/login';
    protected string $formCls = '';
    protected string $name = 'users-login';
    protected array $fields = [
        'username' => [],
        'password' => ['type' => 'password'],
    ];
    protected Service $authService;

    public function setUp(): void
    {
        $this->authService = new Service();
        parent::setUp();
    }

    public function handleSubmit(): BaseForm
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
        return parent::handleSubmit();
    }
}