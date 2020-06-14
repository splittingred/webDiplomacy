<?php
$_ENV['DB_NAME'] = 'webdiplomacy_test';
define('ROOT_PATH', dirname(__DIR__) . '/');
define('IN_CODE', true);

require_once ROOT_PATH . '/src/bootstrap.php';
require_once ROOT_PATH . '/lib/time.php';
require_once ROOT_PATH . '/tests/Support/TestCase.php';
