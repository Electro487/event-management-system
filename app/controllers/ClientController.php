<?php

class ClientController {
    public function dashboard() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/dashboard.php';
    }

    public function browseEvents() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $category = $_GET['category'] ?? 'All';
        $search = $_GET['search'] ?? '';

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $events = $eventModel->getAllActiveEvents($category, $search);

        require_once dirname(__DIR__) . '/views/client/browse_events.php';
    }

    public function viewEvent() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event || $event['status'] !== 'active') {
             header('Location: /EventManagementSystem/public/client/events');
             exit;
        }

        require_once dirname(__DIR__) . '/views/client/view_event.php';
    }
}
