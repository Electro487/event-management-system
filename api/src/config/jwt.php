<?php

$envFile = dirname(__DIR__, 3) . '/.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

return [
    'secret' => $env['JWT_SECRET'] ?? 'replace-this-jwt-secret-in-env',
    'issuer' => $env['JWT_ISSUER'] ?? 'ems-api',
    'ttl_seconds' => (int)($env['JWT_TTL_SECONDS'] ?? 7200),
];
