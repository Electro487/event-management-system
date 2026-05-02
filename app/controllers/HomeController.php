<?php
class HomeController
{
    public function index()
    {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Show the public landing page
        require_once dirname(__DIR__) . '/views/home/index.php';
    }

    public function homePage()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect to login if not logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // If logged in, redirect to respective home/dashboard
        if (isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'client') {
                header('Location: /EventManagementSystem/public/client/home');
                exit;
            } else {
                header('Location: /EventManagementSystem/public/' . $role . '/dashboard');
                exit;
            }
        }

        require_once dirname(__DIR__) . '/views/home/home.php';
    }
}
