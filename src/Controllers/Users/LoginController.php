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

    protected $loginForm;

    public function setUp()
    {
        $this->loginForm = new LoginForm($this->request, $this->renderer);
        parent::setUp();;
    }

    public function call()
    {
        return [
            'login_form' => $this->loginForm->render(),
        ];
    }
}
