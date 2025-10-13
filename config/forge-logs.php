<?php

// config for FrankFlow/LaravelForgeLogs
return [
    'forge_token' => $_ENV['FORGE_TOKEN'] ?? null,
    'forge_server_id' => $_ENV['FORGE_SERVER_ID'] ?? null,
    'forge_site_id' => $_ENV['FORGE_SITE_ID'] ?? null,
    'forge_organization' => $_ENV['FORGE_ORGANIZATION'] ?? null,

    /*
    |--------------------------------------------------------------------------
    | Log File Paths
    |--------------------------------------------------------------------------
    |
    | Customize the storage paths for each type of log file.
    | These paths are relative to the storage_path() directory.
    |
    */
    'log_paths' => [
        'laravel' => 'logs/laravel.log',
        'nginx_access' => 'logs/nginx/access.log',
        'nginx_error' => 'logs/nginx/error.log',
    ],
];
