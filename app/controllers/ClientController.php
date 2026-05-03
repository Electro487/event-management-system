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
        
        require_once dirname(__DIR__) . '/models/Booking.php';
        require_once dirname(__DIR__) . '/models/Event.php';
        $bookingModel = new Booking();
        $eventModel = new Event();
        
        $userId = $_SESSION['user_id'];
        $bookings = $bookingModel->getByClient($userId);
        
        // Stats
        $totalBookings = count($bookings);
        $confirmedCount = 0;
        $pendingCount = 0;
        $completedCount = 0;
        $upcomingCount = 0;
        
        foreach ($bookings as $b) {
            $status = strtolower($b['status']);
            if ($status === 'confirmed') {
                $confirmedCount++;
                $upcomingCount++;
            } elseif ($status === 'pending') {
                $pendingCount++;
            } elseif ($status === 'completed') {
                $completedCount++;
            }
        }
        
        $recentBookings = array_slice($bookings, 0, 5);
        $nextEvent = null;
        $daysLeft = 0;
        foreach ($bookings as $b) {
            if (strtolower($b['status']) === 'confirmed') {
                $nextEvent = $b;
                // Calculate days left
                $eventDate = !empty($b['event_date']) ? $b['event_date'] : null;
                if ($eventDate) {
                    $now = new DateTime();
                    $target = new DateTime($eventDate);
                    $interval = $now->diff($target);
                    $daysLeft = (int)$interval->format('%r%a');
                    if ($daysLeft < 0) $daysLeft = 0;
                }
                break;
            }
        }
        
        $featuredEvents = $eventModel->getRandomActiveEvents(3);

        // Initials for avatar fallback
        $fullName = $_SESSION['user_fullname'] ?? 'User';
        $parts = explode(' ', trim($fullName));
        $initials = strtoupper(substr($parts[0], 0, 1) . (count($parts) > 1 ? substr(end($parts), 0, 1) : ''));

        require_once dirname(__DIR__) . '/views/client/home.php';
    }

    public function browseEvents()
    {
        $this->checkAuth();
        
        require_once dirname(__DIR__) . '/models/Event.php';
        require_once dirname(__DIR__) . '/models/Booking.php';
        $eventModel = new Event();
        $bookingModel = new Booking();
        
        $currentCategory = $_GET['category'] ?? 'All';
        $searchQuery = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 6;
        
        if ($currentCategory !== 'All' || !empty($searchQuery)) {
            $events = $eventModel->getAllActiveEvents($currentCategory, $searchQuery, 100, 0); 
        } else {
            $events = $eventModel->getAllActiveEvents('All', null, 100, 0);
        }
        
        $totalActiveEvents = $eventModel->countActiveEvents($currentCategory, $searchQuery);
        $totalPages = ceil($totalActiveEvents / $itemsPerPage);
        
        // Paginate for the PHP render (first load)
        $eventsSlice = array_slice($events, ($page - 1) * $itemsPerPage, $itemsPerPage);
        $events = $eventsSlice; 
        
        $categories = ['All', 'Weddings', 'Meetings', 'Cultural Events', 'Family Functions', 'Other Events and Programs'];
        
        // Also need bookings for the toggleable "My Bookings" section on the same page
        $bookings = $bookingModel->getByClient($_SESSION['user_id']);
        $totalBookings = count($bookings);
        $confirmedCount = 0;
        $pendingCount = 0;
        $completedCount = 0;
        $upcomingCount = 0;
        $cancelledCount = 0;
        foreach($bookings as $b) {
            $status = strtolower($b['status']);
            if($status === 'confirmed') {
                $confirmedCount++;
                $upcomingCount++;
            }
            elseif($status === 'pending') $pendingCount++;
            elseif($status === 'completed') $completedCount++;
            elseif($status === 'cancelled') $cancelledCount++;
        }

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

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/view_event.php';
    }

    public function bookEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        $packageTier = $_GET['package'] ?? 'basic';

        if (!$id) {
            header('Location: /EventManagementSystem/public/client/events');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
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
        
        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $allBookings = $bookingModel->getByClient($_SESSION['user_id']);
        
        // Filter out concerts
        $bookings = array_filter($allBookings, function($b) {
            return trim(strtolower($b['event_category'] ?? '')) !== 'concert';
        });

        // Calculate stats for the view
        $totalBookings = count($bookings);
        $confirmedCount = 0;
        $pendingCount = 0;
        $cancelledCount = 0;
        
        foreach ($bookings as $b) {
            $status = strtolower($b['status']);
            if ($status === 'confirmed') $confirmedCount++;
            elseif ($status === 'pending') $pendingCount++;
            elseif ($status === 'cancelled') $cancelledCount++;
        }

        $categories = ['All', 'Pending', 'Confirmed', 'Completed', 'Cancelled'];
        
        require_once dirname(__DIR__) . '/views/client/my_bookings.php';
    }

    public function myTickets()
    {
        $this->checkAuth();
        
        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $allBookings = $bookingModel->getByClient($_SESSION['user_id']);
        
        // Filter for concerts only
        $tickets = array_filter($allBookings, function($b) {
            return trim(strtolower($b['event_category'] ?? '')) === 'concert';
        });

        require_once dirname(__DIR__) . '/views/client/my_tickets.php';
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

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($id);

        if (!$booking || (int)$booking['client_id'] !== (int)$_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/client/bookings');
            exit;
        }

        // Calculate payment progress for the view
        require_once dirname(__DIR__) . '/models/Payment.php';
        $paymentModel = new Payment();
        
        $totalAmount = (float)$booking['total_amount'];
        $isConcert = (strtolower($booking['event_category'] ?? '') === 'concert');
        $advancePercent = $isConcert ? 1.00 : 0.50;
        $advanceTarget = $totalAmount * $advancePercent;
        
        $paidAmount = $paymentModel->getSucceededTotalByBookingId($id);
        
        // Backward compatibility: if DB says 'paid' but payments table is empty (Edge case)
        if ($paidAmount < 0.01 && (strtolower($booking['payment_status']) === 'paid')) {
            $paidAmount = $totalAmount;
        }
        
        $paidAdvance = $paidAmount;
        $remainingAdvance = max(0, $advanceTarget - $paidAdvance);
        
        // Next installment is either the remaining advance or 0 if advance is complete
        $nextInstallmentAmount = $remainingAdvance;

        $lastPayment = $paymentModel->getByBookingId($id);
        $transactionId = $lastPayment['transaction_id'] ?? null;

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

    public function viewTicket()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/client/bookings');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($id);

        if (!$booking || (int)$booking['client_id'] !== (int)$_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/client/bookings');
            exit;
        }

        $eSnap = !empty($booking['event_snapshot']) ? json_decode($booking['event_snapshot'], true) : [];
        if (strtolower($eSnap['category'] ?? '') !== 'concert') {
            header('Location: /EventManagementSystem/public/client/bookings/view?id=' . $id);
            exit;
        }

        require_once dirname(__DIR__) . '/views/client/ticket.php';
    }

    public function feedback()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/client/feedback.php';
    }
}
