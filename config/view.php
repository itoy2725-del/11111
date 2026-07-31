<?php

// Ensure compiled views directory exists in /tmp for Vercel / serverless
$compiledPath = env('VIEW_COMPILED_PATH');

if (!$compiledPath) {
    $compiledPath = is_dir('/tmp') ? '/tmp/storage/framework/views' : storage_path('framework/views');
}

if (!is_dir($compiledPath)) {
    @mkdir($compiledPath, 0755, true);
}

return [

    'paths' => [
        resource_path('views'),
    ],

    'compiled' => $compiledPath,

];
