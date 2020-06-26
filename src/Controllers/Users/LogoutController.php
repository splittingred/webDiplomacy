<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as BaseController;
use Diplomacy\Forms\Users\LoginForm;
use Diplomacy\Services\Authorization\SessionHandler;

class LogoutController extends BaseController
{
    /** @var string */
    protected string $template = 'pages/users/logout.twig';
    protected string $pageTitle = 'Logout';
    protected string $pageDescription = '';

    protected SessionHandler $sessionHandler;

    public function setUp(): void
    {
        $this->sessionHandler = new SessionHandler();
        parent::setUp();
    }

    public function call(): array
    {
        $this->sessionHandler->destroy();
        $this->redirectRelative('/');
        return [];
    }
}
