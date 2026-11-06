<?php

/*
|--------------------------------------------------------------------------
| Hostinger front controller
|--------------------------------------------------------------------------
| This file lives in public_html/. The Laravel application lives in
| public_html/kq-app/, which its own .htaccess blocks from the web.
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appPath = __DIR__.'/kq-app';

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appPath.'/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
