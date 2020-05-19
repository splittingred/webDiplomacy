<?php

namespace Diplomacy\Services;

use Diplomacy\Controllers\BaseController;

class Router
{
    public $routes = [
        'default' => \Diplomacy\Controllers\IntroController::class,
        'help' => \Diplomacy\Controllers\Help\HelpController::class,
        'help/developers' => \Diplomacy\Controllers\Help\DevelopersController::class,
        'help/donations' => \Diplomacy\Controllers\Help\DonationsController::class,
        'help/faq' => \Diplomacy\Controllers\Help\FaqController::class,
        'help/points' => \Diplomacy\Controllers\Help\PointsController::class,
        'help/rules' => \Diplomacy\Controllers\Help\RulesController::class,
        'help/recent-changes' => \Diplomacy\Controllers\Help\RecentChangesController::class,
        'intro' => \Diplomacy\Controllers\IntroController::class,
        'stats/hall-of-fame' => \Diplomacy\Controllers\Stats\HallOfFameController::class,
        'tournaments/info' => \Diplomacy\Controllers\Tournaments\InfoController::class,
        'variants/list' => \Diplomacy\Controllers\Variants\IndexController::class,
    ];

    public function route($path)
    {
        /** @var $controller BaseController */
        if (array_key_exists($path, $this->routes)) {
            $controller = new $this->routes[$path];
        } else {
            $controller = new $this->routes['default'];
        }
        return $controller->render();
    }
}