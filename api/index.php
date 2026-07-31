<?php

/**
 * Vercel Serverless Entrypoint & Direct Exception Interceptor
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Ensure ephemeral /tmp storage exists
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

putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";
$_SERVER['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL'] = "stderr";

putenv("APP_DEBUG=true");
$_ENV['APP_DEBUG'] = "true";
$_SERVER['APP_DEBUG'] = "true";

try {
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    $request = \Illuminate\Http\Request::capture();
    
    // Process request directly to catch primary exception before Handler masks it with view error
    $response = $app->handleRequest($request);
    $response->send();

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="padding: 20px; font-family: monospace; background: #fff1f0; border: 2px solid #ffa39e; color: #cf1322; border-radius: 8px; margin: 20px;">';
    echo '<h2 style="margin-top:0;">⚠️ Vercel PHP Kök Hata Analizi</h2>';
    echo '<p><strong>Asıl Hata:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Hata Türü:</strong> ' . htmlspecialchars(get_class($e)) . '</p>';
    echo '<p><strong>Hatalı Dosya:</strong> ' . htmlspecialchars($e->getFile()) . ' (Satır: ' . $e->getLine() . ')</p>';
    echo '<h3 style="margin-bottom:5px;">İzleme (Trace):</h3>';
    echo '<pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
