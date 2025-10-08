<?php

// config for FrankFlow/LaravelForgeLogs
return [
    'forge_token' => $_ENV['FORGE_TOKEN'] ?? null,
    'forge_server_id' => $_ENV['FORGE_SERVER_ID'] ?? null,
    'forge_site_id' => $_ENV['FORGE_SITE_ID'] ?? null,
    'forge_organization' => $_ENV['FORGE_ORGANIZATION'] ?? null,
];
