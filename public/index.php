<?php

// Set memory cukup tapi tidak berlebihan
ini_set('memory_limit', '256M');

// Batasi kedalaman serialisasi (cegah Monolog OOM)
ini_set('serialize_precision', -1);
ini_set('zend.exception_ignore_args', 1); // Jangan simpan args di stack trace

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
