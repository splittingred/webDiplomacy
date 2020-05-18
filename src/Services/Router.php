<?php

namespace Diplomacy\Services;

class Router
{
    public $routes = [
        'default' => \Diplomacy\Controllers\IntroController::class,
        'tournaments/info' => \Diplomacy\Controllers\Tournaments\InfoController::class,
        'help/rules' => \Diplomacy\Controllers\Help\RulesController::class,
    ];

    public function route($path)
    {
        if (array_key_exists($path, $this->routes)) {
            $controller = new $this->routes[$path];
        } else {
            $controller = new $this->routes['default'];
        }
        return $controller->render();
    }
}