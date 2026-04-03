<?php
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost/EventManagementSystem/public');
define('SITE_NAME', 'Event Management System');

// Load Environment Variables
$envFile = dirname(dirname(dirname(__FILE__))) . '/.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

// Stripe Configuration
define('STRIPE_PUBLISHABLE_KEY', $env['STRIPE_PUBLISHABLE_KEY'] ?? 'pk_test_placeholder');
define('STRIPE_SECRET_KEY', $env['STRIPE_SECRET_KEY'] ?? 'sk_test_placeholder');
