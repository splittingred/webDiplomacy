<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\ForgotPasswordForm;

class ForgotPasswordController extends BaseController
{
    protected string $template = 'pages/users/forgot-password.twig';
    protected string $pageTitle = 'Reset your password/find lost username';
    protected string $pageDescription = 'Get back into your account!';

    protected array $noticeMappings = [
        'sent' => 'An email has been sent with a reset link, click that link and enter a new password. If you do not see the email check your spam folder.',
        'invalid_code' => 'Invalid forgot password code. Please try again.',
        'user_not_found' => 'Invalid forgot password code. Please try again.',
        'expired_code' => 'Code expired. Please request another forgot password link.',
    ];

    public function setUp(): void
    {
        $this->makeForm(ForgotPasswordForm::class);
        parent::setUp();
    }

    public function call(): array
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
