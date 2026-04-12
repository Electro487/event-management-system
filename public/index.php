<?php

require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

require_once dirname(__DIR__) . '/app/config/routes.php';

// Basic Routing Logic
// Adjusting for XAMPP subfolder installation
$base_path = '/EventManagementSystem'; // This might need to cover /public if they access by that
$request_uri = $_SERVER['REQUEST_URI'];

// Clean up route path
$path = parse_url($request_uri, PHP_URL_PATH);
$route = str_replace($base_path . '/public', '', $path);
$route = str_replace($base_path, '', $route); // Fallback

$route = rtrim($route, '/');
if (empty($route)) {
    $route = '/';
}

// Dispatch
if (array_key_exists($route, $routes)) {
    $action = $routes[$route];
    list($controllerName, $methodName) = explode('@', $action);

    $controllerPath = dirname(__DIR__) . '/app/controllers/' . $controllerName . '.php';

    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            echo "Method {$methodName} not found in controller {$controllerName}";
        }
    } else {
        echo "Controller {$controllerName} not found.";
    }
} else {
    http_response_code(404);
    require_once dirname(__DIR__) . '/app/views/errors/404.php';
}
