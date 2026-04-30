<?php

class SystemApiController
{
    public function health(): void
    {
        ApiResponse::success([
            'service' => 'ems-api',
            'status' => 'ok',
            'time' => date('c'),
        ]);
    }

    public function protectedPing(): void
    {
        ApiResponse::success([
            'message' => 'Authenticated API access is working.',
            'user_id' => $_SESSION['user_id'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
        ]);
    }
}
