<?php

declare(strict_types=1);

// Initialize writable storage directories in /tmp for Vercel Serverless
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy pre-compiled bootstrap manifests to /tmp for fast cold starts
foreach (['packages.php', 'services.php'] as $manifest) {
    $src = __DIR__ . '/../bootstrap/cache/' . $manifest;
    $dst = '/tmp/bootstrap/cache/' . $manifest;
    if (! file_exists($dst) && file_exists($src)) {
        @copy($src, $dst);
    }
}

require __DIR__ . '/../public/index.php';
