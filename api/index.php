<?php

/**
 * Vercel Serverless Entrypoint & Storage Handler
 */

$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
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

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="padding: 20px; font-family: monospace; background: #fff1f0; border: 2px solid #ffa39e; color: #cf1322; border-radius: 8px; margin: 20px;">';
    echo '<h2 style="margin-top:0;">⚠️ Vercel PHP Çalışma Zamanı Hatası</h2>';
    echo '<p><strong>Hata Mesajı:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Dosya:</strong> ' . htmlspecialchars($e->getFile()) . ' (Satır: ' . $e->getLine() . ')</p>';
    echo '<pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
