<?php

namespace Diplomacy\Forms\Users;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Request;

class ForgotPasswordConfirmationForm extends BaseForm
{
    protected $template = 'forms/users/forgot-password-confirmation.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'user_id';
    protected $fields = [
        'user_id' => [],
        'old_password' => ['type' => 'password'],
        'new_password' => ['type' => 'password'],
        'new_password_confirmation' => ['type' => 'password'],
    ];
    /** @var Service $authService */
    protected $authService;

    public function setUp(): void
    {
        $this->authService = new Service();
        parent::setUp();
    }

    public function handleSubmit(): BaseForm
    {
        $result = $this->authService->changeUserPassword(
            $this->request->get('user_id', 0, Request::TYPE_POST),
            $this->request->get('old_password', '', Request::TYPE_POST),
            $this->request->get('new_password', '', Request::TYPE_POST),
            $this->request->get('new_password_confirm', '', Request::TYPE_POST)
        );
        if ($result->successful()) {
            $this->redirectRelative('/users/login?notice=password_changed');
            close();
        } else {
            $code = $result->getValue()->getCode();
            switch ($code) {
                case 'user_not_found':
                    $this->redirectRelative('/users/forgot?notice=invalid_code');
                    close();
                    break;
                default:
                    $this->setPlaceholder('notice', $result->getValue()->getMessage());
                    break;
            }
        }
        return parent::handleSubmit();
    }
}