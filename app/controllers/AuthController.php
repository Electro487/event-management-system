<?php

class AuthController
{

    private $userModel;

    public function __construct()
    {
        // Need to require User.php inside index.php or here.
        require_once dirname(__DIR__) . '/models/User.php';
        require_once dirname(__DIR__) . '/helpers/MailHelper.php';
        require_once APP_ROOT . '/../api/src/utils/JwtHelper.php';
        $this->userModel = new User();

        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function redirectIfLoggedIn()
    {
        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['user_role'] ?? 'client';
            if ($role === 'admin') {
                header('Location: /EventManagementSystem/public/admin/dashboard');
            } elseif ($role === 'organizer') {
                header('Location: /EventManagementSystem/public/organizer/dashboard');
            } else {
                header('Location: /EventManagementSystem/public/client/home');
            }
            exit;
        }
    }

    public function register()
    {
        $this->redirectIfLoggedIn();
        // POST handling has been migrated to AuthApiController
        // Load register view for GET requests
        require_once dirname(__DIR__) . '/views/auth/register.php';
    }

    public function login()
    {
        $this->redirectIfLoggedIn();
        // POST handling has been migrated to AuthApiController
        // Load view for GET requests
        require_once dirname(__DIR__) . '/views/auth/login.php';
    }

    public function forgotPassword()
    {
        // POST handling has been migrated to AuthApiController
        require_once dirname(__DIR__) . '/views/auth/forgot_password.php';
    }

    public function verifyOtp()
    {
        $email = $_SESSION['otp_email'] ?? '';

        if (empty($email)) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // POST handling has been migrated to AuthApiController
        require_once dirname(__DIR__) . '/views/auth/verify_otp.php';
    }

    public function resetPassword()
    {
        // POST handling has been migrated to AuthApiController
        require_once dirname(__DIR__) . '/views/auth/reset_password.php';
    }

    public function logout()
    {
        // Clear PHP Session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_fullname']);
        unset($_SESSION['user_role']);
        session_destroy();

        // Clear JWT Cookie
        setcookie('ems_jwt', '', time() - 3600, '/');

        header('Location: /EventManagementSystem/public/login');
        exit;
    }
}
