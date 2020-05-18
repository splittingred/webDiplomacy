<?php

namespace Diplomacy\Services;

use Diplomacy\Controllers\BaseController;

class Router
{
    public $routes = [
        'default' => \Diplomacy\Controllers\IntroController::class,
        'tournaments/info' => \Diplomacy\Controllers\Tournaments\InfoController::class,
        'help/faq' => \Diplomacy\Controllers\Help\FaqController::class,
        'help/rules' => \Diplomacy\Controllers\Help\RulesController::class,
        'help/points' => \Diplomacy\Controllers\Help\PointsController::class,
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