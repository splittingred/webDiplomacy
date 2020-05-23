<?php
if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    die();
}

define('IN_CODE', 1);