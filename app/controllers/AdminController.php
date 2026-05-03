<?php

class AdminController
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

        if ($role === 'organizer') {
            header('Location: /EventManagementSystem/public/organizer/dashboard');
            exit;
        }

        if ($role === 'client') {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        if ($role !== 'admin') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth();

        require_once dirname(__DIR__) . '/models/Event.php';
        require_once dirname(__DIR__) . '/models/Booking.php';
        require_once dirname(__DIR__) . '/models/User.php';

        $eventModel = new Event();
        $bookingModel = new Booking();
        $userModel = new User();

        // Fetch Global Statistics
        $totalEvents = $eventModel->countAll();
        $totalBookings = $bookingModel->countAll();
        $totalUsers = $userModel->countAll();
        $pendingRequests = $bookingModel->countByStatus('pending');

        $revenue = $bookingModel->getTotalSystemRevenue();

        $recentBookings = $bookingModel->getRecent(5);
        $upcomingEvents = $eventModel->getUpcoming(5);

        // Status logic for recent bookings
        $today = date('Y-m-d');
        foreach ($recentBookings as &$b) {
            $dateStr = $b['event_date'] ?? null;
            $isPast = ($dateStr && $dateStr < $today);
            $displayStatus = strtolower($b['status']);
            if ($displayStatus === 'confirmed' && $isPast) {
                $displayStatus = 'completed';
            }
            $b['display_status'] = $displayStatus;
        }
        unset($b);

        $statusSummary = [
            'confirmed' => $bookingModel->countByStatus('confirmed'),
            'pending' => $pendingRequests,
            'cancelled' => $bookingModel->countByStatus('cancelled')
        ];

        // Initials for avatar fallback
        $fullName = $_SESSION['user_fullname'] ?? 'Admin';
        $parts = explode(' ', trim($fullName));
        $initials = strtoupper(substr($parts[0], 0, 1) . (count($parts) > 1 ? substr(end($parts), 0, 1) : ''));

        require_once dirname(__DIR__) . '/views/admin/dashboard.php';
    }

    public function users()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/admin/user_management.php';
    }

    public function updateUserRole()
    {
        $this->checkAuth();
        // POST handling has been migrated to UserApiController
        header('Location: /EventManagementSystem/public/admin/users');
        exit;
    }

    public function toggleUserBlock()
    {
        $this->checkAuth();
        // POST handling has been migrated to UserApiController
        header('Location: /EventManagementSystem/public/admin/users');
        exit;
    }

    public function events()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();

        $search = $_GET['search'] ?? null;
        if ($search) {
            $events = $eventModel->searchAll($search);
        } else {
            $events = $eventModel->getAll();
        }

        require_once dirname(__DIR__) . '/views/admin/events.php';
    }

    public function bookings()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $bookings = $bookingModel->getAll(); // Admin sees all

        $totalBookings = count($bookings);
        $confirmedCount = 0;
        $pendingCount = 0;
        $cancelledCount = 0;
        $completedCount = 0;

        $today = date('Y-m-d');

        foreach ($bookings as &$b) {
            $status = strtolower($b['status']);
            $dateStr = $b['event_date'] ?: ($b['event_start_date'] ?? '9999-12-31');
            $isPast = ($dateStr < $today);

            $displayStatus = $status;
            if ($status === 'confirmed' && $isPast) {
                $displayStatus = 'completed';
                $completedCount++;
            } elseif ($status === 'confirmed') {
                $confirmedCount++;
            } elseif ($status === 'pending') {
                $pendingCount++;
            } elseif ($status === 'cancelled') {
                $cancelledCount++;
            }
            $b['display_status'] = $displayStatus;
        }
        unset($b); 

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $allEventsForFilter = $eventModel->getAll();
        $uniqueEventTitles = array_unique(array_column($allEventsForFilter, 'title'));

        require_once dirname(__DIR__) . '/views/admin/bookings.php';
    }

    public function tickets()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/admin/tickets.php';
    }

    public function createEvent()
    {
        $this->checkAuth();
        $isEdit = false;
        require_once dirname(__DIR__) . '/views/admin/create_event.php';
    }

    public function storeEvent()
    {
        $this->checkAuth();
        // POST handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/admin/events');
        exit;
    }

    public function viewEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/admin/events');
            exit;
        }
        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);
        if (!$event) {
            header('Location: /EventManagementSystem/public/admin/events');
            exit;
        }
        require_once dirname(__DIR__) . '/views/admin/view_event.php';
    }

    public function editEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/admin/events');
            exit;
        }
        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);
        if (!$event) {
            header('Location: /EventManagementSystem/public/admin/events');
            exit;
        }
        $isEdit = true;
        require_once dirname(__DIR__) . '/views/admin/create_event.php';
    }

    public function updateEvent()
    {
        $this->checkAuth();
        // PUT handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/admin/events');
        exit;
    }

    public function deleteEvent()
    {
        $this->checkAuth();
        // DELETE handling has been migrated to EventApiController
        header('Location: /EventManagementSystem/public/admin/events');
        exit;
    }

    public function viewBookingDetails()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/admin/bookings');
            exit;
        }
        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($id);

        $today = date('Y-m-d');
        $dateStr = $booking['event_date'] ?: ($booking['event_start_date'] ?? '9999-12-31');
        $isPast = ($dateStr < $today);
        $displayStatus = strtolower($booking['status']);
        if ($displayStatus === 'confirmed' && $isPast)
            $displayStatus = 'completed';
        $booking['display_status'] = $displayStatus;

        require_once dirname(__DIR__) . '/views/admin/booking_detail.php';
    }

    public function approveBooking()
    {
        $this->checkAuth();
        // PATCH handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/admin/bookings');
        exit;
    }

    public function cancelBooking()
    {
        $this->checkAuth();
        // PATCH handling has been migrated to BookingApiController
        header('Location: /EventManagementSystem/public/admin/bookings');
        exit;
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
        header('Location: /EventManagementSystem/public/admin/bookings');
        exit;
    }
}
