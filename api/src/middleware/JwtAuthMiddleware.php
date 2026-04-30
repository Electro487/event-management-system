<?php

class JwtAuthMiddleware
{
    public static function handle(): void
    {
        $token = Request::bearerToken();
        if (!$token) {
            ApiResponse::error('Unauthorized: bearer token required.', 401, [], 'TOKEN_REQUIRED');
            exit;
        }

        try {
            $claims = JwtHelper::verify($token);
        } catch (Throwable $e) {
            ApiResponse::error('Unauthorized: invalid or expired token.', 401, [
                'reason' => $e->getMessage(),
            ], 'TOKEN_INVALID');
            exit;
        }

        // Re-sync user state from DB on protected requests (parity rule).
        $userModel = new User();
        $freshUser = $userModel->findById($claims['sub'] ?? 0);
        if (!$freshUser || !empty($freshUser['is_blocked'])) {
            ApiResponse::error('Unauthorized: account not available.', 401, [], 'ACCOUNT_UNAVAILABLE');
            exit;
        }

        $GLOBALS['api_auth_user'] = [
            'id' => (int)$freshUser['id'],
            'email' => $freshUser['email'],
            'fullname' => $freshUser['fullname'],
            'role' => $freshUser['role'],
            'profile_picture' => $freshUser['profile_picture'] ?? null,
            'is_verified' => (int)$freshUser['is_verified'],
        ];
    }
}
