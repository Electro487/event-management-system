<?php

class OrganizerController
{
    private function checkAuth()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // FETCH CURRENT ROLE FROM DATABASE FOR REAL-TIME SYNC
        require_once dirname(__DIR__) . '/models/User.php';
        $userModel = new User();
        $currentUser = $userModel->findById($_SESSION['user_id']);

        if (!$currentUser || !empty($currentUser['is_blocked'])) {
            session_destroy();
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // Update session role if it changed in DB
        $_SESSION['user_role'] = $currentUser['role'];
        $role = $_SESSION['user_role'];

        if ($role === 'admin') {
            header('Location: /EventManagementSystem/public/admin/dashboard');
            exit;
        }

        if ($role === 'client') {
            header('Location: /EventManagementSystem/public/client/home');
            exit;
        }

        if ($role !== 'organizer') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth();
        
        // Initials for avatar fallback
        $fullName = $_SESSION['user_fullname'] ?? 'Organizer';
        $parts = explode(' ', trim($fullName));
        $initials = strtoupper(substr($parts[0], 0, 1) . (count($parts) > 1 ? substr(end($parts), 0, 1) : ''));

        require_once dirname(__DIR__) . '/views/organizer/dashboard.php';
    }

    public function bookings()
    {
        $this->checkAuth();
        // Data is now fetched via API in the view
        require_once dirname(__DIR__) . '/views/organizer/bookings.php';
    }

    public function events()
    {
        $this->checkAuth();
        // Data is now fetched via API in the view
        require_once dirname(__DIR__) . '/views/organizer/events.php';
    }

    public function createEvent()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/organizer/create_event.php';
    }

    public function storeEvent()
    {
        $this->checkAuth();
        // POST handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/organizer/events');
        exit;
    }

    public function viewEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/organizer/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/organizer/events');
            exit;
        }

        require_once dirname(__DIR__) . '/views/organizer/view_event.php';
    }

    public function editEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/organizer/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/organizer/events');
            exit;
        }

        $isEdit = true;
        require_once dirname(__DIR__) . '/views/organizer/create_event.php';
    }

    public function updateEvent()
    {
        $this->checkAuth();
        // PUT handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/organizer/events');
        exit;
    }

    public function deleteEvent()
    {
        $this->checkAuth();
        // DELETE handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/organizer/events');
        exit;
    }

    public function viewBookingDetails()
    {
        $this->checkAuth();
        // Define defaults for API-driven view
        $booking = null;
        require_once dirname(__DIR__) . '/views/organizer/booking_detail.php';
    }

    public function approveBooking()
    {
        $this->checkAuth();
        // PATCH handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }

    public function cancelBooking()
    {
        $this->checkAuth();
        // PATCH handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }

    public function messages()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/organizer/messages.php';
    }

    public function updateProfile()
    {
        $this->checkAuth();
        // POST handling has been migrated to AuthApiController
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Endpoint migrated to API.']);
        exit;
    }

    public function deleteProfilePicture()
    {
        $this->checkAuth();
        // DELETE handling has been migrated to AuthApiController
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Endpoint migrated to API.']);
        exit;
    }

    public function markBookingPaid()
    {
        $this->checkAuth();
        // PATCH handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }
}
