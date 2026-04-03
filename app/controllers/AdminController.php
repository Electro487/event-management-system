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

            if ($eventModel->create($data)) {
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
            if (!$existingEvent) exit("Not found");

            $data = $this->getEventDataFromPost();
            $newImagePath = $this->handleImageUpload();
            $data['image_path'] = $newImagePath ?: $existingEvent['image_path'];

            if ($eventModel->update($id, $data)) {
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
            $eventModel->delete($id);
            header('Location: /EventManagementSystem/public/admin/events?deleted=1');
            exit;
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
        if ($displayStatus === 'confirmed' && $isPast) $displayStatus = 'completed';
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
                $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');

                if ($booking && ($payStatus === 'paid' || $payStatus === 'partially_paid')) {
                    $bookingModel->updateStatus($id, 'confirmed');
                    header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&approved=1');
                    exit;
                } else {
                    header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&error=unpaid');
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
                
                if ($booking && strtolower($booking['payment_status'] ?? 'unpaid') !== 'paid') {
                    $bookingModel->updateStatus($id, 'cancelled');
                    header('Location: /EventManagementSystem/public/admin/bookings?cancelled=1');
                    exit;
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
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
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
                header('Location: /EventManagementSystem/public/admin/bookings/view?id=' . $id . '&paid=1');
                exit;
            }
        }

        header('Location: /EventManagementSystem/public/admin/bookings');
        exit;
    }
}
