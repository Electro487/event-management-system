<?php
class HomeController {
    public function index() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Show the public landing page (from the home folder)
        require_once dirname(__DIR__) . '/views/home/index.php';
    }
}
