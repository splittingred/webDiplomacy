<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Request;

class ForgotPasswordForm extends BaseForm
{
    protected string $template = 'forms/users/forgot-password.twig';
    protected string $requestType = Request::TYPE_POST;
    protected string $action = '/users/forgot';
    protected string $formCls = '';
    protected string $name = 'users-forgot-password';
    public string $submitBtnText = 'Send Reset Password Confirmation';
    protected array $fields = [
        'username' => [],
    ];
    protected Service $authService;

    public function setUp(): void
    {
        $this->authService = new Service();
        parent::setUp();
    }

    public function handleSubmit(): BaseForm
    {
        $result = $this->authService->sendForgotPasswordConfirmation(
            $this->request->get('username', '', Request::TYPE_POST)
        );
        if ($result->successful()) {
            $this->redirectRelative('/users/forgot?notice=sent');
            close();
        } else {
            $this->setPlaceholder('notice', $result->getValue()->getMessage());
        }
        return parent::handleSubmit();
    }
}