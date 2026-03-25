<?php
class HomeController {
    public function index() {
        // Redirect to login as the default entry point since no homepage is defined
        header('Location: ' . (defined('URL_ROOT') ? URL_ROOT : '/EventManagementSystem/public') . '/login');
        exit;
    }
}
