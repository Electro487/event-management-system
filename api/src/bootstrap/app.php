<?php

require_once dirname(__DIR__) . '/utils/ApiResponse.php';
require_once dirname(__DIR__) . '/middleware/SessionAuthMiddleware.php';
require_once dirname(__DIR__) . '/controllers/SystemApiController.php';

$routes = require __DIR__ . '/routes.php';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$basePath = '/EventManagementSystem/public';
$path = str_replace($basePath, '', $requestPath);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$matchedRoute = null;
foreach ($routes as $route) {
    [$method, $routePath] = $route;
    if ($requestMethod === $method && $path === $routePath) {
        $matchedRoute = $route;
        break;
    }
}

if ($matchedRoute === null) {
    ApiResponse::error('API route not found', 404, [
        'method' => $requestMethod,
        'path' => $path,
    ]);
    return;
}

[,, $handler, $middlewareStack] = $matchedRoute;

foreach ($middlewareStack as $middlewareClass) {
    $middlewareClass::handle();
}

[$controllerClass, $controllerMethod] = $handler;
$controller = new $controllerClass();
$controller->{$controllerMethod}();
