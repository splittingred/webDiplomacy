<?php
use Diplomacy\Views\Renderer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container as Container;
use Illuminate\Support\Facades\Facade as Facade;
use \Illuminate\Pagination\Paginator as Paginator;

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

$app = new Container();
$app->singleton('app', 'Illuminate\Container\Container');
Facade::setFacadeApplication($app);

Paginator::currentPageResolver(function ($pageName) {
    return empty($_GET[$pageName]) ? 1 : $_GET[$pageName];
});

global $renderer;
$renderer = Renderer::getInstance();

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
