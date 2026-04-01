<?php

class ClientController
{
    public function dashboard()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/dashboard.php';
    }

    public function browseEvents()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $category = $_GET['category'] ?? 'All';
        $search = $_GET['search'] ?? '';

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $events = $eventModel->getAllActiveEvents($category, $search);

        // Fetch bookings for the dynamic tab view
        $bookings = [];
        $totalBookings = 0;
        $confirmedCount = 0;
        $pendingCount = 0;
        $completedCount = 0;
        $upcomingCount = 0;
        $cancelledCount = 0;

        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'client') {
            require_once dirname(__DIR__) . '/models/Booking.php';
            $bookingModel = new Booking();
            $bookings = $bookingModel->getByClient($_SESSION['user_id']);

            $totalBookings = count($bookings);

            foreach ($bookings as &$b) {
                if ($b['status'] == 'confirmed')
                    $confirmedCount++;
                if ($b['status'] == 'pending')
                    $pendingCount++;
                if ($b['status'] == 'completed')
                    $completedCount++;
                if ($b['status'] == 'cancelled')
                    $cancelledCount++;
                if (in_array($b['status'], ['pending', 'confirmed']))
                    $upcomingCount++;

                if (!empty($b['event_packages'])) {
                    $b['packages_data'] = json_decode($b['event_packages'], true);
                } else {
                    $b['packages_data'] = [];
                }
            }
        }

        require_once dirname(__DIR__) . '/views/client/browse_events.php';
    }

    public function viewEvent()
    {
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

    public function bookEvent()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        $event_id = $_GET['event_id'] ?? null;
        $packageTier = $_GET['package'] ?? null;

        if (!$event_id || !$packageTier) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($event_id);

        if (!$event || $event['status'] !== 'active') {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/book_event.php';
    }

    public function storeBooking()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__) . '/models/Booking.php';
            $bookingModel = new Booking();

            $data = [
                'event_id' => $_POST['event_id'],
                'client_id' => $_SESSION['user_id'],
                'package_tier' => $_POST['package_tier'],
                'event_date' => $_POST['event_date'],
                'guest_count' => $_POST['guest_count'],
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'total_amount' => $_POST['total_amount'],
                'service_fee' => 0,
                'status' => 'pending'
            ];

            $bookingId = $bookingModel->create($data);

            if ($bookingId) {
                // Redirect directly to My Bookings
                header('Location: /EventManagementSystem/public/client/events#my-bookings');
                exit;
            } else {
                // Handle error
                header('Location: /EventManagementSystem/public/client/events');
                exit;
            }
        }
    }

    public function myBookings()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // Redirect the old bookings page to the new combined SPA view
        header('Location: /EventManagementSystem/public/client/events#my-bookings');
        exit;
    }

    public function cancelBooking()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
            require_once dirname(__DIR__) . '/models/Booking.php';
            $bookingModel = new Booking();
            $bookingModel->cancel($_POST['booking_id'], $_SESSION['user_id']);
        }

        header('Location: /EventManagementSystem/public/client/events#my-bookings');
        exit;
    }

    public function viewBookingDetails()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        $booking_id = $_GET['id'] ?? null;
        if (!$booking_id) {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($booking_id);

        if (!$booking || $booking['client_id'] !== $_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/client/events#my-bookings');
            exit;
        }

        $packages = !empty($booking['event_packages']) ? json_decode($booking['event_packages'], true) : [];
        $selectedPackage = $packages[$booking['package_tier']] ?? null;

        require_once dirname(__DIR__) . '/views/client/view_booking_details.php';
    }
}
