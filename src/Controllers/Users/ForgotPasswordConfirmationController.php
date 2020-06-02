<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\ForgotPasswordConfirmationForm;
use Diplomacy\Forms\Users\ForgotPasswordForm;
use Diplomacy\Models\User;
use Diplomacy\Services\Authorization\Service;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Request;

class ForgotPasswordConfirmationController extends BaseController
{
    /** @var string */
    protected $template = 'pages/users/forgot-password-confirmation.twig';
    protected $pageTitle = 'Reset your password/find lost username';
    protected $pageDescription = 'Get back into your account!';

    /** @var Service */
    protected $authService;
    /** @var ForgotPasswordConfirmationForm */
    protected $forgotPasswordConfirmationForm;

    protected $noticeMappings = [
        'changed' => 'Your password has successfully been changed. Please login again.',
        'invalid_old_password' => 'Incorrect old password. Please check your spelling and try again.',
        'invalid_new_password' => 'Invalid new password. Please ensure at least 8 characters long, one uppercase, and one lowercase, and try again.',
        'invalid_new_password_confirm' => 'Your new password and confirmation do not match. Please try again.',
    ];

    public function setUp()
    {
        $this->authService = new Service();
        $this->forgotPasswordConfirmationForm = new ForgotPasswordConfirmationForm($this->request, $this->renderer);
        parent::setUp();
    }

    public function call()
    {
        $token = $this->request->get('token', '', Request::TYPE_REQUEST);
        /** @var Result $result */
        $result = $this->authService->verifyForgotPasswordToken($token);

        if ($result->failure()) {
            $this->redirectRelative('/users/forgot?notice=' . $result->getValue()->getCode());
            return [];
        } else {
            $user = $result->getValue();
        }

        $this->forgotPasswordConfirmationForm->setPlaceholder('user_id', $user->id);
        $this->forgotPasswordConfirmationForm->setPlaceholder('token', $token);

        return [
            'form' => $this->forgotPasswordConfirmationForm->render(),
        ];
    }
}
