<?php

class ApiResponse
{
    public static function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    public static function success(array $data = [], int $statusCode = 200, array $meta = []): void
    {
        self::json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ], $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $meta = [], ?string $code = null): void
    {
        self::json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'meta' => $meta,
            ],
        ], $statusCode);
    }
}
