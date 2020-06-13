<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\LoginForm;

class LoginController extends BaseController
{
    /** @var string */
    protected $template = 'pages/users/login.twig';
    protected $pageTitle = 'Log on';
    protected $pageDescription = 'Enter your webDiplomacy account username and password to log into your account.';

    protected $noticeMappings = [
        'password_changed' => 'Your password has successfully been changed. Please login again.',
    ];

    public function setUp()
    {
        $this->makeForm(LoginForm::class);
        parent::setUp();
    }

    public function call()
    {
        return [
            'login_form' => $this->form->render(),
        ];
    }
}
