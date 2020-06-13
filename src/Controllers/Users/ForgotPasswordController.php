<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\ForgotPasswordForm;

class ForgotPasswordController extends BaseController
{
    /** @var string */
    protected $template = 'pages/users/forgot-password.twig';
    protected $pageTitle = 'Reset your password/find lost username';
    protected $pageDescription = 'Get back into your account!';

    protected $noticeMappings = [
        'sent' => 'An email has been sent with a reset link, click that link and enter a new password. If you do not see the email check your spam folder.',
        'invalid_code' => 'Invalid forgot password code. Please try again.',
        'user_not_found' => 'Invalid forgot password code. Please try again.',
        'expired_code' => 'Code expired. Please request another forgot password link.',
    ];

    public function setUp()
    {
        $this->makeForm(ForgotPasswordForm::class);
        parent::setUp();
    }

    public function call()
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
