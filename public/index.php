<?php

require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (strpos($requestUri, 'register') !== false) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once dirname(__DIR__) . '/app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        exit;
    }
    require_once dirname(__DIR__) . '/app/views/auth/register.php';
    exit;
}

require_once dirname(__DIR__) . '/app/config/routes.php';

echo "Welcome to the Event Management System Framework.";
