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

        // Mock Revenue for now
        $revenue = 580000;

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

        require_once dirname(__DIR__) . '/views/admin/dashboard.php';
    }

    public function users()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/models/User.php';
        $userModel = new User();

        $users = $userModel->getAll();

        $stats = [
            'total' => count($users),
            'clients' => $userModel->countByRole('client'),
            'organizers' => $userModel->countByRole('organizer'),
            'blocked' => $userModel->countBlocked()
        ];

        require_once dirname(__DIR__) . '/views/admin/user_management.php';
    }

    public function updateUserRole()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $newRole = $_POST['role'] ?? null;

            if ($userId && $newRole) {
                require_once dirname(__DIR__) . '/models/User.php';
                $userModel = new User();
                $userModel->updateRole($userId, $newRole);
                header('Location: /EventManagementSystem/public/admin/users?success=role_updated');
                exit;
            }
        }
        header('Location: /EventManagementSystem/public/admin/users?error=1');
    }

    public function toggleUserBlock()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $status = $_POST['status'] ?? null; // 1 for block, 0 for unblock

            if ($userId !== null && $status !== null) {
                require_once dirname(__DIR__) . '/models/User.php';
                $userModel = new User();
                $userModel->toggleBlock($userId, $status);
                header('Location: /EventManagementSystem/public/admin/users?success=block_toggled');
                exit;
            }
        }
        header('Location: /EventManagementSystem/public/admin/users?error=1');
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
        unset($b); // Clear reference to avoid bug in next loop

        // Fetch all unique event titles for the filter dropdown
        require_once dirname(__DIR__) . '/models/Event.php';
        $eventModel = new Event();
        $allEventsForFilter = $eventModel->getAll();
        $uniqueEventTitles = array_unique(array_column($allEventsForFilter, 'title'));

        require_once dirname(__DIR__) . '/views/admin/bookings.php';
    }

    // --- Admin Event Management (Mirrors Organizer but for Admin Space) ---

    public function createEvent()
    {
        $this->checkAuth();
        $isEdit = false;
        require_once dirname(__DIR__) . '/views/admin/create_event.php';
    }

    public function storeEvent()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();
            $data = $this->getEventDataFromPost();
            $data['image_path'] = $this->handleImageUpload();

            $eventId = $eventModel->create($data);
            if ($eventId) {
                require_once dirname(__DIR__) . '/models/User.php';
                require_once dirname(__DIR__) . '/models/Notification.php';
                $userModel = new User();
                $notificationModel = new Notification();
                
                $clientIds = $userModel->getClients();
                $clientTitle = "New Event Launched by Admin!";
                $clientMsg = "A new official event '{$data['title']}' has been created. Register now!";
                foreach ($clientIds as $client) {
                    $notificationModel->create($client['id'], $clientTitle, $clientMsg, 'event', $eventId);
                }

                header('Location: /EventManagementSystem/public/admin/events?success=1');
            } else {
                header('Location: /EventManagementSystem/public/admin/events?error=1');
            }
            exit;
        }
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();
            $existingEvent = $eventModel->getById($id);
            if (!$existingEvent)
                exit("Not found");

            $data = $this->getEventDataFromPost();
            $newImagePath = $this->handleImageUpload();
            $data['image_path'] = $newImagePath ?: $existingEvent['image_path'];

            // Identify changed fields for detailed notification
            $changedFields = [];
            $labels = [
                'title' => 'Title',
                'description' => 'Description',
                'category' => 'Category',
                'event_date' => 'Date',
                'event_time' => 'Time',
                'venue_name' => 'Venue Name',
                'venue_location' => 'Venue Location',
                'image_path' => 'Event Image'
            ];
            foreach ($labels as $key => $label) {
                if (isset($data[$key]) && $data[$key] != $existingEvent[$key]) {
                    $changedFields[] = $label;
                }
            }

            // Specific package comparison
            $oldPackages = json_decode($existingEvent['packages'] ?? '{}', true) ?: [];
            $newPackages = $_POST['packages'] ?? [];
            $packageMap = ['basic' => 'Basic Package', 'standard' => 'Standard Package', 'premium' => 'Premium Package'];
            foreach ($packageMap as $pKey => $pLabel) {
                $oldP = $oldPackages[$pKey] ?? null;
                $newP = $newPackages[$pKey] ?? null;
                if ($oldP != $newP) {
                    $changedFields[] = $pLabel;
                }
            }

            $diffText = !empty($changedFields) ? " (Fields updated: " . implode(', ', $changedFields) . ")" : "";

            if ($eventModel->update($id, $data)) {
                // If this event belongs to an organizer, notify them that Admin modified it (unless it's the same person)
                if (!empty($existingEvent['organizer_id']) && $existingEvent['organizer_id'] != $_SESSION['user_id']) {
                    require_once dirname(__DIR__) . '/models/Notification.php';
                    $notificationModel = new Notification();
                    $title = "Event Modified by Administration";
                    $message = "The administration has updated the details for your event: '{$existingEvent['title']}'." . $diffText;
                    $notificationModel->create($existingEvent['organizer_id'], $title, $message, 'event_update', $id);
                }

                // Notify ALL active clients about the event update
                require_once dirname(__DIR__) . '/models/User.php';
                $userModel = new User();
                $allClients = $userModel->getClients();
                if (!empty($allClients)) {
                     require_once dirname(__DIR__) . '/models/Notification.php';
                     $notificationModel = $notificationModel ?? new Notification();
                     $clientTitle = "Event Details Updated by Admin";
                     $clientMsg = "The administration has updated the details for '{$existingEvent['title']}'." . $diffText . " Please review.";
                     foreach ($allClients as $client) {
                         $notificationModel->create($client['id'], $clientTitle, $clientMsg, 'event_update', $id);
                     }
                }

                header('Location: /EventManagementSystem/public/admin/events?updated=1');
            } else {
                header('Location: /EventManagementSystem/public/admin/events?error=1');
            }
            exit;
        }
    }

    public function deleteEvent()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();
            $event = $eventModel->getById($id);
            if ($event) {
                // Prepare notification and booking models to capture clients BEFORE deletion
                require_once dirname(__DIR__) . '/models/Notification.php';
                require_once dirname(__DIR__) . '/models/Booking.php';
                $notificationModel = new Notification();
                $bookingModel = new Booking();
                $clientIds = $bookingModel->getClientsByEvent($id);

                if ($eventModel->delete($id)) {
                    // Update: Notify Organizer
                    if (!empty($event['organizer_id'])) {
                        $title = "Event Removed by Administration";
                        $message = "The administration has removed your event: '{$event['title']}'.";
                        $notificationModel->create($event['organizer_id'], $title, $message, 'event_delete', 0);
                    }

                    // Update: Notify Booked Clients with new refund wording
                    if (!empty($clientIds)) {
                         $clientTitle = "Event Cancelled by Administration";
                         $clientMsg = "Sorry, the event '{$event['title']}' has been removed by the administration. we will refund your money as soon as possible as the event is cancelled and you already booked the event.";
                         foreach ($clientIds as $clientId) {
                             $notificationModel->create($clientId, $clientTitle, $clientMsg, 'event_delete', 0);
                         }
                    }
                    header('Location: /EventManagementSystem/public/admin/events?deleted=1');
                    exit;
                }
            }
        }
        header('Location: /EventManagementSystem/public/admin/events?error=1');
        exit;
    }

    // --- Admin Booking Management ---

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

        // Status logic
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'] ?? null;
            if ($id) {
                require_once dirname(__DIR__) . '/models/Booking.php';
                $bookingModel = new Booking();
                $booking = $bookingModel->getById($id);
                if ($booking) {
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');

                    if ($payStatus === 'paid' || $payStatus === 'partially_paid') {
                        $bookingModel->updateStatus($id, 'confirmed');

                        // Notify Client
                        require_once dirname(__DIR__) . '/models/Notification.php';
                        $notificationModel = new Notification();
                        $title = "Booking Confirmed";
                        $message = "Your booking for '{$booking['event_title']}' has been confirmed by the administration.";
                        $notificationModel->create($booking['client_id'], $title, $message, 'booking_approve', $id);

                        header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&approved=1');
                    } else {
                        header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&error=unpaid');
                    }
                    exit;
                }
            }
        }
        header('Location: /EventManagementSystem/public/admin/bookings?error=1');
    }

    public function cancelBooking()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'] ?? null;
            if ($id) {
                require_once dirname(__DIR__) . '/models/Booking.php';
                $bookingModel = new Booking();
                $booking = $bookingModel->getById($id);
                if ($booking) {
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    
                    if ($payStatus !== 'paid') {
                        $bookingModel->updateStatus($id, 'cancelled');

                        // Notify Client
                        require_once dirname(__DIR__) . '/models/Notification.php';
                        $notificationModel = new Notification();
                        $title = "Booking Cancelled";
                        $message = "Your booking for '{$booking['event_title']}' has been cancelled by the administration.";
                        $notificationModel->create($booking['client_id'], $title, $message, 'booking_cancel', $id);

                        header('Location: /EventManagementSystem/public/admin/bookings?cancelled=1');
                        exit;
                    }
                }
            }
        }
        header('Location: /EventManagementSystem/public/admin/bookings?error=1');
    }

    // --- Helper Methods ---

    private function getEventDataFromPost()
    {
        return [
            'organizer_id' => $_POST['organizer_id'] ?? $_SESSION['user_id'],
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'event_date' => null,
            'venue_name' => $_POST['venue_name'] ?? '',
            'venue_location' => $_POST['venue_location'] ?? '',
            'packages' => json_encode($_POST['packages'] ?? [])
        ];
    }

    private function handleImageUpload()
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(dirname(__DIR__)) . '/public/assets/images/events/';
            if (!file_exists($uploadDir))
                mkdir($uploadDir, 0777, true);
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = 'event_' . uniqid() . '_' . time() . '.' . $fileExtension;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                return '/EventManagementSystem/public/assets/images/events/' . $fileName;
            }
        }
        return null;
    }

    public function updateProfile()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = dirname(dirname(__DIR__)) . '/public/assets/images/profiles/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid file format.']);
                    exit;
                }

                $fileName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                    $publicPath = '/EventManagementSystem/public/assets/images/profiles/' . $fileName;

                    require_once dirname(__DIR__) . '/models/User.php';
                    $userModel = new User();

                    $oldProfilePath = $_SESSION['user_profile_pic'] ?? null;
                    if ($userModel->updateProfilePicture($_SESSION['user_id'], $publicPath)) {

                        if ($oldProfilePath) {
                            $oldFilePath = dirname(dirname(__DIR__)) . str_replace('/EventManagementSystem', '', $oldProfilePath);
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }
                        }

                        $_SESSION['user_profile_pic'] = $publicPath;
                        echo json_encode(['success' => true, 'path' => $publicPath]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'File movement failed.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No file or upload error.']);
            }
        }
        exit;
    }

    public function deleteProfilePicture()
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__) . '/models/User.php';
            $userModel = new User();

            $oldProfilePath = $_SESSION['user_profile_pic'] ?? null;
            if ($userModel->updateProfilePicture($_SESSION['user_id'], null)) {

                if ($oldProfilePath) {
                    $oldFilePath = dirname(dirname(__DIR__)) . str_replace('/EventManagementSystem', '', $oldProfilePath);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $_SESSION['user_profile_pic'] = null;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove from database.']);
            }
            exit;
        }
    }

    public function markBookingPaid()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
            require_once dirname(__DIR__) . '/models/Booking.php';
            $bookingModel = new Booking();
            $id = $_POST['booking_id'];

            $booking = $bookingModel->getById($id);
            if ($booking && strtolower($booking['payment_status'] ?? 'unpaid') === 'partially_paid') {
                $bookingModel->updatePaymentStatus($id, 'paid');

                // --- Notification Logic ---
                require_once dirname(__DIR__) . '/models/Notification.php';
                require_once dirname(__DIR__) . '/models/User.php';
                $notificationModel = new Notification();
                $userModel = new User();

                $eventTitle = $booking['event_title'] ?? ($booking['event_name'] ?? 'your event');

                // 1. Notify Client
                $clientTitle = "Payment Fully Paid (Cash)";
                $clientMsg = "Your payment for '{$eventTitle}' has been recorded as Fully Paid (Cash). Thank you!";
                $notificationModel->create($booking['client_id'], $clientTitle, $clientMsg, 'payment', $id);

                // 2. Notify Organizer (Only if NOT an admin event)
                $organizer = $userModel->findById($booking['organizer_id']);
                if ($organizer && $organizer['role'] !== 'admin') {
                    $orgTitle = "Cash Payment Received";
                    $orgMsg = "An administrator has marked the booking for '{$eventTitle}' as Fully Paid (Cash).";
                    $notificationModel->create($booking['organizer_id'], $orgTitle, $orgMsg, 'payment_alert', $id);
                }
                // --------------------------

                header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&paid=1');
                exit;
            }
        }

        header('Location: /EventManagementSystem/public/admin/bookings');
        exit;
    }
}
