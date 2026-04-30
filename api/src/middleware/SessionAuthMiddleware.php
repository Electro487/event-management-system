<?php

class SessionAuthMiddleware
{
    public static function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            ApiResponse::error('Unauthorized', 401);
            exit;
        }
    }
}
