<?php
class HomeController {
    public function index() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // If logged in, redirect to dashboard
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'admin') {
                $role = 'organizer';
            }
            $redirect = '/EventManagementSystem/public/' . $role . '/dashboard';
            header('Location: ' . $redirect);
            exit;
        }

        // Show the public landing page (from the home folder)
        require_once dirname(__DIR__) . '/views/home/index.php';
    }
}
