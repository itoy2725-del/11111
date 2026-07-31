<?php

/**
 * Vercel Serverless Entrypoint
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// --- Writable /tmp paths for Vercel's read-only filesystem ---
$tmpStorage   = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    $tmpBootstrap,
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Redirect all Laravel cache files to writable /tmp
putenv("APP_SERVICES_CACHE={$tmpBootstrap}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpBootstrap}/packages.php");
putenv("APP_CONFIG_CACHE={$tmpBootstrap}/config.php");
putenv("APP_ROUTES_CACHE={$tmpBootstrap}/routes.php");
putenv("APP_EVENTS_CACHE={$tmpBootstrap}/events.php");

$_ENV['APP_SERVICES_CACHE']  = "{$tmpBootstrap}/services.php";
$_ENV['APP_PACKAGES_CACHE']  = "{$tmpBootstrap}/packages.php";
$_ENV['APP_CONFIG_CACHE']    = "{$tmpBootstrap}/config.php";
$_ENV['APP_ROUTES_CACHE']    = "{$tmpBootstrap}/routes.php";
$_ENV['APP_EVENTS_CACHE']    = "{$tmpBootstrap}/events.php";

putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL'] = "stderr";

putenv("SESSION_DRIVER=cookie");
$_ENV['SESSION_DRIVER'] = "cookie";

putenv("CACHE_STORE=array");
$_ENV['CACHE_STORE'] = "array";

putenv("APP_DEBUG=true");
$_ENV['APP_DEBUG'] = "true";

try {
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    $request = \Illuminate\Http\Request::capture();
    $response = $app->handleRequest($request);
    $response->send();

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="padding:20px;font-family:monospace;background:#fff1f0;border:2px solid #ffa39e;color:#cf1322;border-radius:8px;margin:20px;">';
    echo '<h2 style="margin-top:0;">⚠️ Vercel PHP Kök Hata Analizi</h2>';
    echo '<p><strong>Asıl Hata:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Hata Türü:</strong> ' . htmlspecialchars(get_class($e)) . '</p>';
    echo '<p><strong>Dosya:</strong> ' . htmlspecialchars($e->getFile()) . ' (Satır: ' . $e->getLine() . ')</p>';
    echo '<pre style="background:#fff;padding:10px;border-radius:4px;overflow-x:auto;font-size:12px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
