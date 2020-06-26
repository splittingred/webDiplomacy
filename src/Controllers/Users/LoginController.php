<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\LoginForm;

class LoginController extends BaseController
{
    /** @var string */
    protected string $template = 'pages/users/login.twig';
    protected string $pageTitle = 'Log on';
    protected string $pageDescription = 'Enter your webDiplomacy account username and password to log into your account.';

    protected array $noticeMappings = [
        'password_changed' => 'Your password has successfully been changed. Please login again.',
    ];

    public function setUp(): void
    {
        $this->makeForm(LoginForm::class);
        parent::setUp();
    }

    public function call(): array
    {
        return [
            'login_form' => $this->form->render(),
        ];
    }
}
