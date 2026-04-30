<?php

class ClientController
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

        if ($role === 'organizer') {
            header('Location: /EventManagementSystem/public/organizer/dashboard');
            exit;
        }

        if ($role !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }
    }

    public function home()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/client/home.php';
    }

    public function browseEvents()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/client/browse_events.php';
    }

    public function viewEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }
        require_once dirname(__DIR__) . '/views/client/view_event.php';
    }

    public function bookEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }
        require_once dirname(__DIR__) . '/views/client/book_event.php';
    }

    public function storeBooking()
    {
        $this->checkAuth();
        // POST handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/client/bookings');
        exit;
    }

    public function myBookings()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/client/my_bookings.php';
    }

    public function cancelBooking()
    {
        $this->checkAuth();
        // POST handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/client/bookings');
        exit;
    }

    public function viewBookingDetails()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/client/bookings');
            exit;
        }
        require_once dirname(__DIR__) . '/views/client/view_booking_details.php';
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
}
