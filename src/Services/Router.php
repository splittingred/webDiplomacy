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
     *
     */
    private function configure() : void
    {
        $this->router->set404('IntroController@handle');
        $this->router->get('games/{id}/orders', function($gameId) {
            \Diplomacy\Controllers\Games\View\OrdersController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/{id}/maps', function($gameId) {
            \Diplomacy\Controllers\Games\View\MapsController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('games/{id}/messages', function($gameId) {
            \Diplomacy\Controllers\Games\View\MessagesController::handle(['id' => (int)$gameId]);
        });
        $this->router->get('help', 'Help\HelpController@handle');
        $this->router->get('help/developers', 'Help\DevelopersController@handle');
        $this->router->get('help/donations', 'Help\DonationsController@handle');
        $this->router->get('help/faq', 'Help\FaqController@handle');
        $this->router->get('help/points', 'Help\PointsController@handle');
        $this->router->get('help/rules', 'Help\RulesController@handle');
        $this->router->get('help/recent-changes', 'Help\RecentChangesController@handle');
        $this->router->get('intro', 'IntroController@handle');
        $this->router->get('stats/hall-of-fame', 'Stats\HallOfFameController@handle');
        $this->router->get('tournaments/info', 'Tournaments\InfoController@handle');
        $this->router->get('variants/list', 'Variants\IndexController@handle');
        $this->router->get('users/settings', 'Users\SettingsController@handle');
    }

    /**
     * @return void
     */
    public function route() : void
    {
        $this->router->run();
    }
}