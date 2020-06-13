<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Request;

class ForgotPasswordForm extends BaseForm
{
    protected $template = 'forms/users/forgot-password.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'username';
    protected $fields = [
        'username' => [],
    ];
    /** @var Service $authService */
    protected $authService;

    public function setUp(): void
    {
        $this->authService = new Service();
        parent::setUp();
    }

    public function handleSubmit()
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
    }
}