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
            header('Location: /EventManagementSystem/public/client/events');
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
        $organizer_id = $_SESSION['user_id'];

        require_once dirname(__DIR__) . '/models/Event.php';
        require_once dirname(__DIR__) . '/models/Booking.php';

        $eventModel = new Event();
        $bookingModel = new Booking();

        // Fetch Real Statistics
        $totalEvents = $eventModel->getTotalEvents($organizer_id);
        $totalBookings = $bookingModel->countByOrganizer($organizer_id);
        $pendingRequests = $bookingModel->countByStatusForOrganizer($organizer_id, 'pending');
        $revenue = $bookingModel->getRevenueByOrganizer($organizer_id);

        // Fetch Recent Bookings (Limit 5)
        $recentBookings = $bookingModel->getRecentByOrganizer($organizer_id, 5);

        // Fetch Upcoming Events with Confirmed Bookings (Limit 5)
        $upcomingEvents = $eventModel->getUpcomingEvents($organizer_id, 5);

        // Status Summary
        $statusSummary = [
            'confirmed' => $bookingModel->countByStatusForOrganizer($organizer_id, 'confirmed'),
            'pending' => $pendingRequests,
            'cancelled' => $bookingModel->countByStatusForOrganizer($organizer_id, 'cancelled')
        ];

        require_once dirname(__DIR__) . '/views/organizer/dashboard.php';
    }

    public function events()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Event.php';
        /** @var \Event $eventModel */
        $eventModel = new Event();
        $events = $eventModel->getAllByOrganizer($_SESSION['user_id']);

        require_once dirname(__DIR__) . '/views/organizer/events.php';
    }

    public function createEvent()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        require_once dirname(__DIR__) . '/views/organizer/create_event.php';
    }

    public function storeEvent()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();

            $data = $this->getEventDataFromPost();
            $data['image_path'] = $this->handleImageUpload();

            if ($eventModel->create($data)) {
                header('Location: /EventManagementSystem/public/organizer/events?success=1');
            } else {
                $_SESSION['error'] = "Database insertion failed.";
                header('Location: /EventManagementSystem/public/organizer/events?error=1');
            }
            exit;
        }
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if (!$id)
                exit;

            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();
            $existingEvent = $eventModel->getById($id);

            if (!$existingEvent || $existingEvent['organizer_id'] != $_SESSION['user_id']) {
                exit("Unauthorized");
            }

            $data = $this->getEventDataFromPost();

            // Handle image update
            $newImagePath = $this->handleImageUpload();
            $data['image_path'] = $newImagePath ?: $existingEvent['image_path'];

            if ($eventModel->update($id, $data)) {
                header('Location: /EventManagementSystem/public/organizer/events?updated=1');
            } else {
                $_SESSION['error'] = "Update failed.";
                header('Location: /EventManagementSystem/public/organizer/events?error=1');
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

            if ($event && $event['organizer_id'] == $_SESSION['user_id']) {
                $eventModel->delete($id);
                header('Location: /EventManagementSystem/public/organizer/events?deleted=1');
                exit;
            }
        }
        header('Location: /EventManagementSystem/public/organizer/events?error=1');
        exit;
    }

    public function bookings()
    {
        $this->checkAuth();

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();

        $bookings = $bookingModel->getByOrganizer($_SESSION['user_id']);

        $totalBookings = count($bookings);
        $confirmedCount = 0;
        $pendingCount = 0;
        $cancelledCount = 0;
        $completedCount = 0;

        $today = date('Y-m-d');
        foreach ($bookings as &$b) {
            $dateStr = $b['event_date'] ?: ($b['event_start_date'] ?? '9999-12-31');
            $isPast = ($dateStr < $today);
            
            $displayStatus = strtolower($b['status']);
            if ($displayStatus === 'confirmed' && $isPast) {
                $displayStatus = 'completed';
            }
            $b['display_status'] = $displayStatus;

            if ($displayStatus === 'confirmed') $confirmedCount++;
            if ($displayStatus === 'pending') $pendingCount++;
            if ($displayStatus === 'cancelled') $cancelledCount++;
            if ($displayStatus === 'completed') $completedCount++;
        }
        unset($b); // Important: break the reference to the last element

        require_once dirname(__DIR__) . '/views/organizer/bookings.php';
    }

    public function viewBookingDetails()
    {
        $this->checkAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /EventManagementSystem/public/organizer/bookings');
            exit;
        }

        require_once dirname(__DIR__) . '/models/Booking.php';
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($id);

        if (!$booking || $booking['organizer_id'] != $_SESSION['user_id']) {
            header('Location: /EventManagementSystem/public/organizer/bookings');
            exit;
        }

        // Dynamic Status Logic: Past confirmed bookings reflect as 'completed'
        $today = date('Y-m-d');
        $dateStr = $booking['event_date'] ?: ($booking['event_start_date'] ?? '9999-12-31');
        $isPast = ($dateStr < $today);
        
        $displayStatus = strtolower($booking['status']);
        if ($displayStatus === 'confirmed' && $isPast) {
            $displayStatus = 'completed';
        }
        $booking['display_status'] = $displayStatus;

        require_once dirname(__DIR__) . '/views/organizer/booking_detail.php';
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

                if ($booking && $booking['organizer_id'] == $_SESSION['user_id']) {
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    if ($payStatus === 'paid' || $payStatus === 'partially_paid') {
                        $bookingModel->updateStatus($id, 'confirmed');
                        header('Location: /EventManagementSystem/public/organizer/bookings/view?id=' . $id . '&approved=1');
                        exit;
                    } else {
                        header('Location: /EventManagementSystem/public/organizer/bookings/view?id=' . $id . '&error=unpaid');
                        exit;
                    }
                }
            }
        }
        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
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

                if ($booking && $booking['organizer_id'] == $_SESSION['user_id']) {
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    if ($payStatus !== 'paid') {
                        $bookingModel->updateStatus($id, 'cancelled');
                        header('Location: /EventManagementSystem/public/organizer/bookings/view?id=' . $id . '&cancelled=1');
                        exit;
                    }
                }
            }
        }
        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }

    private function getEventDataFromPost()
    {
        return [
            'organizer_id' => $_SESSION['user_id'],
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
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = 'event_' . uniqid() . '_' . time() . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                return '/EventManagementSystem/public/assets/images/events/' . $fileName;
            }
        }
        return null;
    }

    public function messages()
    {
        $this->checkAuth();
        require_once dirname(__DIR__) . '/views/organizer/messages.php';
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
            if ($booking && $booking['organizer_id'] == $_SESSION['user_id']) {
                if (strtolower($booking['payment_status'] ?? 'unpaid') === 'partially_paid') {
                    $bookingModel->updatePaymentStatus($id, 'paid');
                    header('Location: /EventManagementSystem/public/organizer/bookings/view?id=' . $id . '&paid=1');
                    exit;
                }
            }
        }

        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }
}
