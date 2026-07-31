<?php

/**
 * Vercel Serverless Entrypoint & Storage Re-configuration
 */

// Ensure writable directories exist in Vercel's ephemeral /tmp storage
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Set environment variable overrides for Vercel
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL'] = "stderr";

// Forward request to public/index.php
require __DIR__ . '/../public/index.php';
