<?php
class HomeController {
    public function index() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // Load home view for authenticated users
        require_once dirname(__DIR__) . '/views/home/index.php';
    }
}
