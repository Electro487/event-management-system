<?php

require_once dirname(__DIR__) . '/utils/ApiResponse.php';
require_once dirname(__DIR__) . '/utils/Request.php';
require_once dirname(__DIR__) . '/utils/JwtHelper.php';
require_once dirname(__DIR__) . '/middleware/SessionAuthMiddleware.php';
require_once dirname(__DIR__) . '/middleware/JwtAuthMiddleware.php';
require_once dirname(__DIR__) . '/controllers/SystemApiController.php';
require_once dirname(__DIR__) . '/controllers/AuthApiController.php';
require_once dirname(__DIR__) . '/controllers/EventApiController.php';
require_once dirname(__DIR__) . '/controllers/BookingApiController.php';
require_once dirname(__DIR__) . '/controllers/DashboardApiController.php';
require_once dirname(__DIR__) . '/controllers/PaymentApiController.php';
require_once dirname(__DIR__) . '/controllers/NotificationApiController.php';
require_once dirname(__DIR__, 3) . '/app/models/User.php';
require_once dirname(__DIR__, 3) . '/app/models/Event.php';
require_once dirname(__DIR__, 3) . '/app/models/Booking.php';
require_once dirname(__DIR__, 3) . '/app/models/Payment.php';
require_once dirname(__DIR__, 3) . '/app/models/Notification.php';
require_once dirname(__DIR__, 3) . '/app/helpers/MailHelper.php';
require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/EventService.php';
require_once dirname(__DIR__) . '/services/BookingService.php';
require_once dirname(__DIR__) . '/services/DashboardService.php';
require_once dirname(__DIR__) . '/services/PaymentService.php';
require_once dirname(__DIR__) . '/services/NotificationService.php';

$routes = require __DIR__ . '/routes.php';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$basePath = '/EventManagementSystem/public';
$path = str_replace($basePath, '', $requestPath);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

try {
    $matchedRoute = null;
    $routeParams = [];

    foreach ($routes as $route) {
        [$method, $routePath] = $route;
        if ($requestMethod !== $method) {
            continue;
        }

        $paramNames = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '([^\/]+)';
        }, $routePath);

        $regex = '#^' . $pattern . '$#';
        if (!preg_match($regex, $path, $matches)) {
            continue;
        }

        array_shift($matches);
        foreach ($matches as $index => $value) {
            $routeParams[$paramNames[$index]] = urldecode($value);
        }

        $matchedRoute = $route;
        break;
    }

    if ($matchedRoute === null) {
        ApiResponse::error('API route not found', 404, [
            'method' => $requestMethod,
            'path' => $path,
        ], 'ROUTE_NOT_FOUND');
        return;
    }

    [,, $handler, $middlewareStack] = $matchedRoute;
    Request::capture($routeParams);

    foreach ($middlewareStack as $middlewareClass) {
        $middlewareClass::handle();
    }

    [$controllerClass, $controllerMethod] = $handler;
    $controller = new $controllerClass();
    $controller->{$controllerMethod}();
} catch (Throwable $e) {
    ApiResponse::error('Internal server error', 500, [
        'exception' => $e->getMessage(),
    ], 'INTERNAL_SERVER_ERROR');
}
