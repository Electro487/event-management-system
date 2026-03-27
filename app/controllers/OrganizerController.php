<?php

class OrganizerController {
    public function dashboard() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['organizer', 'admin'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/views/organizer/dashboard.php';
    }

    public function events() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['organizer', 'admin'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $events = $eventModel->getAllByOrganizer($_SESSION['user_id']);

        require_once dirname(__DIR__) . '/views/organizer/events.php';
    }

    public function createEvent() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['organizer', 'admin'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/views/organizer/create_event.php';
    }
}
