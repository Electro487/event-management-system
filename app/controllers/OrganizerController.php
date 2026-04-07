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
        $organizer_id = $_SESSION['user_id'];

        require_once dirname(__DIR__) . '/models/Event.php';
        require_once dirname(__DIR__) . '/models/Booking.php';

        $eventModel = new Event();
        $bookingModel = new Booking();

        // Fetch Real Statistics
        $totalEvents = $eventModel->getTotalEvents($organizer_id);
        $totalBookings = $bookingModel->countByOrganizer($organizer_id);
        $pendingRequests = $bookingModel->countByStatusForOrganizer($organizer_id, 'pending');

        // Fetch Recent Bookings (Limit 5)
        $recentBookings = $bookingModel->getRecentByOrganizer($organizer_id, 5);

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
        $this->checkAuth();

        require_once dirname(__DIR__) . '/models/Event.php';
        /** @var \Event $eventModel */
        $eventModel = new Event();
        $events = $eventModel->getAllByOrganizer($_SESSION['user_id']);

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__) . '/models/Event.php';
            $eventModel = new Event();

            try {
                $data = $this->getEventDataFromPost();
            } catch (\InvalidArgumentException $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /EventManagementSystem/public/organizer/events/create');
                exit;
            }
            $data['image_path'] = $this->handleImageUpload();

            $eventId = $eventModel->create($data);
            if ($eventId) {
                // Notify Admins
                require_once dirname(__DIR__) . '/models/User.php';
                require_once dirname(__DIR__) . '/models/Notification.php';
                $userModel = new User();
                $notificationModel = new Notification();
                $adminIds = $userModel->getAdmins();
                $title = "New Event Campaign";
                $message = "Organizer {$_SESSION['user_fullname']} has created a new event: {$data['title']}.";
                foreach ($adminIds as $admin) {
                    $notificationModel->create($admin['id'], $title, $message, 'event', $eventId);
                }

                // Notify All Clients
                $clientIds = $userModel->getClients();
                $clientTitle = "New Event Launched!";
                $clientMessage = "A new event '{$data['title']}' has been created by {$_SESSION['user_fullname']}. Check it out!";
                foreach ($clientIds as $client) {
                    $notificationModel->create($client['id'], $clientTitle, $clientMessage, 'event', $eventId);
                }

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

            try {
                $data = $this->getEventDataFromPost();
            } catch (\InvalidArgumentException $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /EventManagementSystem/public/organizer/events/edit?id=' . urlencode((string) $id));
                exit;
            }

            // Handle image update
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
                // Notify ALL active clients about the event update
                require_once dirname(__DIR__) . '/models/User.php';
                require_once dirname(__DIR__) . '/models/Notification.php';
                $userModel = new User();
                $notificationModel = new Notification();
                $allClients = $userModel->getClients();
                $title = "Event Details Updated";
                $message = "The organizer has updated the details for '{$existingEvent['title']}'." . $diffText . " Please review.";
                foreach ($allClients as $client) {
                    $notificationModel->create($client['id'], $title, $message, 'event_update', $id);
                }

                // Notify Admins
                require_once dirname(__DIR__) . '/models/User.php';
                $userModel = new User();
                $allAdmins = $userModel->getAdmins();
                $adminTitle = "Event Updated by Organizer";
                $adminMsg = "Organizer {$_SESSION['user_fullname']} updated '{$existingEvent['title']}'." . $diffText;
                foreach ($allAdmins as $admin) {
                    if ($admin['id'] == ($_SESSION['user_id'] ?? 0))
                        continue; // Skip self
                    $notificationModel->create($admin['id'], $adminTitle, $adminMsg, 'event_update', $id);
                }

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
                // Prepare notification and booking models to capture clients BEFORE deletion
                require_once dirname(__DIR__) . '/models/User.php';
                require_once dirname(__DIR__) . '/models/Notification.php';
                require_once dirname(__DIR__) . '/models/Booking.php';

                $userModel = new User();
                $notificationModel = new Notification();
                $bookingModel = new Booking();

                $clientIds = $bookingModel->getClientsByEvent($id);

                if ($eventModel->delete($id)) {
                    // Update: Notify Admins
                    $allAdmins = $userModel->getAdmins();
                    $adminTitle = "Event Cancelled by Organizer";
                    $adminMsg = "Organizer {$_SESSION['user_fullname']} cancelled '{$event['title']}'.";
                    foreach ($allAdmins as $admin) {
                        if ($admin['id'] == ($_SESSION['user_id'] ?? 0))
                            continue; // Skip self
                        $notificationModel->create($admin['id'], $adminTitle, $adminMsg, 'event_delete', 0);
                    }

                    // Update: Notify Booked Clients with new refund wording
                    if (!empty($clientIds)) {
                        $clientTitle = "Event Cancelled by Organizer";
                        $clientMsg = "Sorry, the event '{$event['title']}' has been removed by the organizer. we will refund your money as soon as possible as the event is cancelled and you already booked the event.";
                        foreach ($clientIds as $clientId) {
                            $notificationModel->create($clientId, $clientTitle, $clientMsg, 'event_delete', 0);
                        }
                    }
                    header('Location: /EventManagementSystem/public/organizer/events?deleted=1');
                    exit;
                }
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

            if ($displayStatus === 'confirmed')
                $confirmedCount++;
            if ($displayStatus === 'pending')
                $pendingCount++;
            if ($displayStatus === 'cancelled')
                $cancelledCount++;
            if ($displayStatus === 'completed')
                $completedCount++;
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

                        // Notify Client
                        require_once dirname(__DIR__) . '/models/Notification.php';
                        $notificationModel = new Notification();
                        $clientTitle = "Booking Confirmed";
                        $eventTitle = $booking['event_title'] ?? ($booking['event_name'] ?? 'your event');
                        $clientMessage = "Your '{$eventTitle}' booking has been confirmed by the venue manager.";
                        $notificationModel->create($booking['client_id'], $clientTitle, $clientMessage, 'booking_approve', $id);

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

                        // Notify Client
                        require_once dirname(__DIR__) . '/models/Notification.php';
                        $notificationModel = new Notification();
                        $clientTitle = "Booking Cancelled";
                        $eventTitle = $booking['event_title'] ?? ($booking['event_name'] ?? 'your event');
                        $clientMessage = "Your '{$eventTitle}' booking has been cancelled by the organizer.";
                        $notificationModel->create($booking['client_id'], $clientTitle, $clientMessage, 'booking_cancel', $id);

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
        $validatedPackages = $this->validatePackagePricing($_POST['packages'] ?? []);

        return [
            'organizer_id' => $_SESSION['user_id'],
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'event_date' => null,
            'venue_name' => $_POST['venue_name'] ?? '',
            'venue_location' => $_POST['venue_location'] ?? '',
            'packages' => json_encode($validatedPackages)
        ];
    }

    private function validatePackagePricing($packages)
    {
        if (!is_array($packages)) {
            throw new \InvalidArgumentException('Invalid package data submitted.');
        }

        $maxAmount = 20000000;
        $tiers = ['basic', 'standard', 'premium'];
        $prices = [];

        foreach ($tiers as $tier) {
            if (!isset($packages[$tier]) || !is_array($packages[$tier])) {
                throw new \InvalidArgumentException('Please provide all package tiers: basic, standard, and premium.');
            }

            $rawPrice = $packages[$tier]['price'] ?? null;
            if ($rawPrice === null || $rawPrice === '') {
                throw new \InvalidArgumentException(ucfirst($tier) . ' package price is required.');
            }

            if (!preg_match('/^\d+$/', (string) $rawPrice)) {
                throw new \InvalidArgumentException(ucfirst($tier) . ' package price must be a whole number only.');
            }

            $price = (int) $rawPrice;
            if ($price <= 0) {
                throw new \InvalidArgumentException(ucfirst($tier) . ' package price must be greater than 0.');
            }

            if ($price > $maxAmount) {
                throw new \InvalidArgumentException(ucfirst($tier) . ' package price cannot exceed NPR 2,00,00,000.');
            }

            if (($tier === 'basic' || $tier === 'standard') && $price >= $maxAmount) {
                throw new \InvalidArgumentException('Only premium package can be set to NPR 2,00,00,000. Basic and standard must be lower.');
            }

            $prices[$tier] = $price;
            $packages[$tier]['price'] = $price;
        }

        if (!($prices['basic'] < $prices['standard'] && $prices['standard'] < $prices['premium'])) {
            throw new \InvalidArgumentException('Price order must be: Basic < Standard < Premium.');
        }

        return $packages;
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
            require_once dirname(__DIR__) . '/models/Payment.php';
            $bookingModel = new Booking();
            $paymentModel = new Payment();
            $id = $_POST['booking_id'];

            $booking = $bookingModel->getById($id);
            if ($booking && $booking['organizer_id'] == $_SESSION['user_id']) {
                if (strtolower($booking['payment_status'] ?? 'unpaid') === 'partially_paid') {
                    $paidSoFar = $paymentModel->getSucceededTotalByBookingId($id);
                    $cashAmount = max(0, (float) $booking['total_amount'] - $paidSoFar);

                    if ($cashAmount > 0.009) {
                        $paymentModel->create([
                            'booking_id' => $id,
                            'client_id' => $booking['client_id'],
                            'transaction_id' => str_replace('.', '_', uniqid('cash_org_' . $id . '_', true)),
                            'amount' => $cashAmount,
                            'payment_method' => 'cash',
                            'status' => 'succeeded',
                            'stripe_session_id' => null,
                        ]);
                    }

                    $bookingModel->updatePaymentStatus($id, 'paid');

                    // --- Notification Logic ---
                    require_once dirname(__DIR__) . '/models/Notification.php';
                    require_once dirname(__DIR__) . '/models/User.php';
                    $notificationModel = new Notification();
                    $userModel = new User();

                    $eventTitle = $booking['event_title'] ?? ($booking['event_name'] ?? 'your event');
                    $organizerName = $_SESSION['user_fullname'] ?? 'Organizer';

                    // 1. Notify Client
                    $clientTitle = "Payment Fully Paid (Cash)";
                    $clientMsg = "Your payment for '{$eventTitle}' has been recorded as Fully Paid (Cash) by the venue manager. Thank you!";
                    $notificationModel->create($booking['client_id'], $clientTitle, $clientMsg, 'payment', $id);

                    // 2. Notify ALL Admins
                    $admins = $userModel->getAdmins();
                    $adminTitle = "Cash Payment Marked by Organizer";
                    $adminMsg = "Organizer '{$organizerName}' has marked the booking for '{$eventTitle}' as Fully Paid (Cash).";
                    foreach ($admins as $admin) {
                        $notificationModel->create($admin['id'], $adminTitle, $adminMsg, 'payment_alert', $id);
                    }
                    header('Location: /EventManagementSystem/public/organizer/bookings/view?id=' . $id . '&paid=1');
                    exit;
                }
            }
        }

        header('Location: /EventManagementSystem/public/organizer/bookings');
        exit;
    }
}
