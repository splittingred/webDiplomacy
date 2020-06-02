<?php
use Diplomacy\Views\Renderer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container as Container;
use Illuminate\Support\Facades\Facade as Facade;
use \Illuminate\Pagination\Paginator as Paginator;
use Twig\Loader\FilesystemLoader;

if (!defined('IN_CODE')) {
    http_response_code(404);
    exit(1);
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
}

require_once ROOT_PATH . 'vendor/autoload.php';
require_once ROOT_PATH . 'config.php';
require_once ROOT_PATH . 'src/bootstrap_legacy.php';
require_once ROOT_PATH . 'global/definitions.php';
require_once ROOT_PATH . 'objects/mailer.php';

global $app;
$app = new Container();
$app->singleton('app', 'Illuminate\Container\Container');
$app->singleton('renderer', function($app) {
    $loader = new FilesystemLoader(ROOT_PATH . 'templates');
    $env = new Renderer($loader, [
        'cache' => ROOT_PATH . '/cache/templates',
        'debug' => true,
    ]);
    $env->addGlobal('current_user', $app->make('user'));
    return $env;
});
$app->instance('mailer', new \Mailer());
Facade::setFacadeApplication($app);

Paginator::currentPageResolver(function ($pageName) {
    return empty($_GET[$pageName]) ? 1 : $_GET[$pageName];
});

global $capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => \Config::$database_socket,
    'database' => \Config::$database_name,
    'username' => \Config::$database_username,
    'password' => \Config::$database_password,
]);
//Make this Capsule instance available globally.
$capsule->setAsGlobal();
// Setup the Eloquent ORM.
$capsule->bootEloquent();
