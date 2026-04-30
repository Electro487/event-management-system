<?php

class AuthBridge
{
    /**
     * Synchronizes the PHP Session with the JWT token found in cookies.
     * This allows the MVC views to work even when the user logged in via API.
     */
    public static function sync(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If session already exists and has name, we are good
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_fullname'])) {
            return;
        }

        // Check for JWT cookie
        $token = $_COOKIE['ems_jwt'] ?? null;
        if (!$token) {
            return;
        }

        // We need the JWT library. Since this is MVC, we'll try to use the API's JWT logic if available,
        // or a simple decode if we just need the claims.
        try {
            require_once dirname(dirname(__DIR__)) . '/api/src/utils/JwtHelper.php';
            $decoded = JwtHelper::verify($token);

            if ($decoded && isset($decoded['sub'])) {
                // Populate session from JWT claims
                $_SESSION['user_id'] = $decoded['sub'];
                $_SESSION['user_email'] = $decoded['email'] ?? '';
                $_SESSION['user_fullname'] = $decoded['name'] ?? '';
                $_SESSION['user_role'] = $decoded['role'] ?? 'client';
                $_SESSION['user_profile_pic'] = $decoded['profile_pic'] ?? null;
                
                // Optional: Log sync for debugging
                // error_log("AuthBridge: Session populated for user " . $_SESSION['user_id']);
            }
        } catch (Exception $e) {
            // Invalid token, clear cookie to avoid infinite loops
            setcookie('ems_jwt', '', time() - 3600, '/');
        }
    }
}
