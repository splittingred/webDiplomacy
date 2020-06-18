<?php

use Diplomacy\Services\Request;
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
$dotEnv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotEnv->load();
require_once ROOT_PATH . 'config.php';

global $app;
$app = new Container();
$app->singleton('app', 'Illuminate\Container\Container');

require_once ROOT_PATH . 'src/bootstrap_legacy.php';
require_once ROOT_PATH . 'global/definitions.php';
require_once ROOT_PATH . 'objects/mailer.php';

Facade::setFacadeApplication($app);

/****************************************************************
/* Logger
/****************************************************************/
$log = new Illuminate\Log\Logger(new Monolog\Logger('webDiplomacy Logger'));
$log->pushHandler(new Monolog\Handler\StreamHandler('./log/development.log')); // TODO: make env specific
$app->instance('logger', $log);

/****************************************************************
/* Database
/****************************************************************/
global $capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => !empty($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : \Config::$database_socket,
    'database' => !empty($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : \Config::$database_name,
    'username' => !empty($_ENV['DB_USER']) ? $_ENV['DB_USER'] : \Config::$database_username,
    'password' => !empty($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : \Config::$database_password,
]);
//Make this Capsule instance available globally.
$capsule->setAsGlobal();
// Setup the Eloquent ORM.
$capsule->bootEloquent();
$app->instance('database.capsule', $capsule);
$app->instance('database.connection', function($app) {
    return $app->make('database.capsule')->getConnection();
});

/****************************************************************
/* Controllers
/****************************************************************/
$request = new Request();
$app->instance('request', $request);
$app->singleton('renderer', function($app) {
    return Renderer::initialize($app);
});

Paginator::currentPageResolver(function ($pageName) {
    return empty($_GET[$pageName]) ? 1 : $_GET[$pageName];
});

/****************************************************************
/* Validation
/****************************************************************/

$fileSystem = new \Illuminate\Filesystem\Filesystem();
$fileLoader = new \Illuminate\Translation\FileLoader($fileSystem, ROOT_PATH. 'resources/lang/');
$translator = new \Illuminate\Translation\Translator($fileLoader, 'en');
$app->instance('translation.translator', $translator);
$validatorFactory = new \Illuminate\Validation\Factory($translator, $app);
$app->instance('validation.factory', $validatorFactory);

/****************************************************************
/* Misc
/****************************************************************/
$app->instance('mailer', new \Mailer());