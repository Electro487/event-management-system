<?php

class Request
{
    private static array $jsonBody = [];
    private static array $routeParams = [];
    private static bool $captured = false;

    public static function capture(array $routeParams = []): void
    {
        self::$routeParams = $routeParams;
        self::$jsonBody = [];

        $raw = file_get_contents('php://input');
        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                self::$jsonBody = $decoded;
            }
        }

        self::$captured = true;
    }

    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function input(string $key, $default = null)
    {
        if (!self::$captured) {
            self::capture();
        }

        if (array_key_exists($key, self::$jsonBody)) {
            return self::$jsonBody[$key];
        }

        if (array_key_exists($key, $_POST)) {
            return $_POST[$key];
        }

        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }

        return $default;
    }

    public static function body(): array
    {
        if (!self::$captured) {
            self::capture();
        }

        return self::$jsonBody;
    }

    public static function param(string $key, $default = null)
    {
        return self::$routeParams[$key] ?? $default;
    }

    public static function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? $_SERVER['Authorization']
            ?? '';

        if ($header === '' && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
            }
        }

        if (!is_string($header) || stripos($header, 'Bearer ') !== 0) {
            return null;
        }

        return trim(substr($header, 7));
    }
}
