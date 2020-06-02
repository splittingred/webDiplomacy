<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\LoginForm;
use Diplomacy\Services\Authorization\SessionHandler;

class LogoutController extends BaseController
{
    /** @var string */
    protected $template = 'pages/users/logout.twig';
    protected $pageTitle = 'Logout';
    protected $pageDescription = '';

    /** @var SessionHandler */
    protected $sessionHandler;

    public function setUp()
    {
        $this->sessionHandler = new SessionHandler();
        parent::setUp();
    }

    public function call()
    {
        $this->sessionHandler->destroy();
        $this->redirectRelative('/');
        return [];
    }
}
