<?php
class HomeController {
    public function index() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        /* If logged in, redirect to dashboard
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'client') {
                $redirect = '/EventManagementSystem/public/client/events';
            } else {
                $redirect = '/EventManagementSystem/public/' . $role . '/dashboard';
            }
            header('Location: ' . $redirect);
            exit;
        } */

        // Show the public landing page (from the home folder)
        require_once dirname(__DIR__) . '/views/home/index.php';
    }

    public function homePage() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        require_once dirname(__DIR__) . '/views/home/home.php';
    }
}
