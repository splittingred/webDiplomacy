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
    protected string $template = 'pages/users/forgot-password-confirmation.twig';
    protected string $pageTitle = 'Reset your password/find lost username';
    protected string $pageDescription = 'Get back into your account!';

    protected Service $authService;

    protected array $noticeMappings = [
        'changed' => 'Your password has successfully been changed. Please login again.',
        'invalid_old_password' => 'Incorrect old password. Please check your spelling and try again.',
        'invalid_new_password' => 'Invalid new password. Please ensure at least 8 characters long, one uppercase, and one lowercase, and try again.',
        'invalid_new_password_confirm' => 'Your new password and confirmation do not match. Please try again.',
    ];

    public function setUp(): void
    {
        $this->authService = new Service();
        $this->makeForm(ForgotPasswordConfirmationForm::class);
        parent::setUp();
    }

    public function call(): array
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

        $this->form->setPlaceholder('user_id', $user->id);
        $this->form->setPlaceholder('token', $token);

        return [
            'form' => $this->form->render(),
        ];
    }
}
