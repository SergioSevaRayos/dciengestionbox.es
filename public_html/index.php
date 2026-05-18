<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel_core/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';

$app->handleRequest(Request::capture());
