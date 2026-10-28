<?php

declare(strict_types=1);

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup writable directories in /tmp for Vercel Serverless environment
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/storage/app/public/catalogue',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Copy pre-seeded SQLite database to writable /tmp
$sqliteSource = __DIR__ . '/../database/database.sqlite';
$sqliteTarget = '/tmp/database.sqlite';

if (file_exists($sqliteSource)) {
    if (! file_exists($sqliteTarget) || filesize($sqliteTarget) === 0) {
        @copy($sqliteSource, $sqliteTarget);
        @chmod($sqliteTarget, 0666);
    }
}

// Forward storage path to writable /tmp
$_ENV['APP_STORAGE'] = '/tmp/storage';
putenv('APP_STORAGE=/tmp/storage');

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

$app->handleRequest(Request::capture());
