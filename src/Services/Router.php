<?php

namespace Diplomacy\Services;

use Diplomacy\Controllers\BaseController;
use \Bramus\Router\Router as RouterService;

class Router
{
    /** @var RouterService */
    protected $router;

    public function __construct()
    {
        $this->router = new RouterService();
        $this->router->setNamespace('\Diplomacy\Controllers');
        $this->configure();
    }

    /**
     * Configure our router
     */
    private function configure() : void
    {
        $this->router->set404('IntroController@handle');

        /* game view */
        $this->router->get('games/(\d+)', function($gameId) {
            \Diplomacy\Controllers\Games\View\GameController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/(\d+)/orders', function($gameId) {
            \Diplomacy\Controllers\Games\View\OrdersController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/(\d+)/maps', function($gameId) {
            \Diplomacy\Controllers\Games\View\MapsController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/(\d+)/messages', function($gameId) {
            \Diplomacy\Controllers\Games\View\MessagesController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/(\d+)/graph', function($gameId) {
            \Diplomacy\Controllers\Games\View\GraphController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/(\d+)/moderator-notes', function($gameId) {
            \Diplomacy\Controllers\Games\View\ModeratorNotesController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/mine', 'Games\Search\MineController@handle');
        $this->router->get('games/search/new', 'Games\Search\NewController@handle');
        $this->router->get('games/open', 'Games\Search\OpenController@handle');
        $this->router->get('games/active', 'Games\Search\ActiveController@handle');
        $this->router->get('games/finished', 'Games\Search\FinishedController@handle');
        $this->router->get('games/search', 'Games\Search\IndexController@handle');

        /* help */
        $this->router->get('help', 'Help\HelpController@handle');
        $this->router->get('help/developers', 'Help\DevelopersController@handle');
        $this->router->get('help/donations', 'Help\DonationsController@handle');
        $this->router->get('help/faq', 'Help\FaqController@handle');
        $this->router->get('help/points', 'Help\PointsController@handle');
        $this->router->get('help/rules', 'Help\RulesController@handle');
        $this->router->get('help/recent-changes', 'Help\RecentChangesController@handle');
        $this->router->get('help/contact', 'Help\ContactController@handle');
        $this->router->get('help/contact-direct', 'Help\ContactDirectController@handle');
        $this->router->post('help/contact-direct', 'Help\ContactDirectController@handle');

        /* misc */
        $this->router->get('intro', 'IntroController@handle');
        $this->router->get('stats/hall-of-fame', 'Stats\HallOfFameController@handle');
        $this->router->get('variants/list', 'Variants\IndexController@handle');

        /* users */
        $this->router->get('users/(\d+)', function($userId) {
            \Diplomacy\Controllers\Users\ProfileController::handle(['id' => (int)$userId]);
        });
        $this->router->get('users/(\d+)/civil-disorders', function($userId) {
            \Diplomacy\Controllers\Users\CivilDisordersController::handle(['id' => (int)$userId]);
        });
        $this->router->get('users/(\d+)/threads', function($userId) {
            \Diplomacy\Controllers\Users\ThreadsController::handle(['id' => (int)$userId]);
        });
        $this->router->get('users/settings', 'Users\SettingsController@handle');
        $this->router->get('users/notices', 'Users\NoticesController@handle');
        $this->router->all('users/login', 'Users\LoginController@handle');
        $this->router->get('users/logout', 'Users\LogoutController@handle');
        $this->router->all('users/forgot', 'Users\ForgotPasswordController@handle');
        $this->router->all('users/forgot', 'Users\ForgotPasswordController@handle');
        $this->router->all('users/forgot-confirmation', 'Users\ForgotPasswordConfirmationController@handle');

        /* tournaments */
        $this->router->get('tournaments/info', 'Tournaments\InfoController@handle');
        $this->router->get('tournaments/', 'Tournaments\IndexController@handle');
        $this->router->get('tournaments/(\d+)', function($id) {
            \Diplomacy\Controllers\Tournaments\ViewController::handle(['id' => (int)$id]);
        });
        $this->router->post('tournaments/(\d+)', function($id) {
            \Diplomacy\Controllers\Tournaments\ViewController::handle(['id' => (int)$id]);
        });

        /* admin */
        $this->router->get('admin/logs', 'Admin\LogsController@handle');
    }

    /**
     * @return void
     */
    public function route() : void
    {
        $this->router->run();
    }
}