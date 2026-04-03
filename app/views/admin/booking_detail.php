<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Data Preparation
$id = $booking['id'];
$status = strtolower($booking['status']);
$displayStatus = $booking['display_status'] ?? $status;
$fullName = $booking['full_name'] ?: 'Unknown Client';
$initials = strtoupper(substr($fullName, 0, 1) . substr(strrchr($fullName, ' '), 1, 1));
if (strlen($initials) < 2) $initials = strtoupper(substr($fullName, 0, 2));

$eventTitle = $booking['event_title'];
$category = $booking['event_category'];
$packageTier = strtoupper($booking['package_tier']);
$eventDateStr = $booking['event_date'] ?: $booking['event_start_date'];
$eventDate = new DateTime($eventDateStr);
$guestCount = $booking['guest_count'];
$venueName = $booking['venue_name'] ?: 'Royal Palace';
$venueLocation = $booking['venue_location'] ?: 'Bhaktapur';
$eventImage = $booking['event_image'] ?: '/EventManagementSystem/public/assets/images/placeholder.jpg';

// Package Features logic
$allPackages = json_decode($booking['event_packages'] ?? '[]', true);
$features = [];
$packageDesc = "Most popular for " . strtolower($packageTier) . " events";

$tierKey = strtolower($booking['package_tier']);
if (isset($allPackages[$tierKey])) {
    $pkg = $allPackages[$tierKey];
    $items = $pkg['items'] ?? [];
    $features = array_map(function($item) {
        return $item['title'];
    }, $items);
    
    if (!empty($pkg['description'])) {
        $packageDesc = $pkg['description'];
    }
}

$totalAmount = $booking['total_amount'];
$basePrice = $totalAmount;

// Timeline Logic
$currentDate = new DateTime();
$todayStr = $currentDate->format('Y-m-d');
$eventDateStr = $eventDate->format('Y-m-d');

function getStepClass($stepKey, $currentStatus, $currentDate, $eventDate) {
    if ($currentStatus === 'cancelled') return '';
    
    $todayStr = $currentDate->format('Y-m-d');
    $eventDateStr = $eventDate->format('Y-m-d');
    
    switch ($stepKey) {
        case 'received':
        case 'review':
            return 'completed';
        case 'confirmed':
            if ($currentStatus === 'confirmed' || $currentStatus === 'completed') return 'completed';
            return '';
        case 'event':
            if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed') return '';
            if ($todayStr === $eventDateStr) return 'active';
            if ($todayStr > $eventDateStr) return 'completed';
            return '';
        case 'completed':
            if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed') return '';
            return ($todayStr > $eventDateStr) ? 'completed' : '';
        default:
            return '';
    }
}

$steps = [
    ['label' => 'Received', 'desc' => 'Booking received successfully', 'key' => 'received'],
    ['label' => 'Under Review', 'desc' => ($status === 'cancelled') ? 'Booking cancelled' : 'Reviewing event details', 'key' => 'review'],
    ['label' => 'Confirmed', 'desc' => ($status === 'confirmed' || $status === 'completed') ? 'Booking confirmed' : 'Awaiting confirmation', 'key' => 'confirmed'],
    ['label' => 'Event Day', 'desc' => 'Scheduled for ' . $eventDate->format('M d, Y'), 'key' => 'event'],
    ['label' => 'Completed', 'desc' => ($todayStr > $eventDateStr && $status !== 'cancelled') ? 'Event successfully completed' : 'Pending event day', 'key' => 'completed']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #BK-<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?> | Organizer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking-detail.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php 
        $activePage = 'bookings';
        include_once dirname(__DIR__) . "/admin/partials/sidebar.php"; 
    ?>

    <main class="main-content">
        <header class="detail-header">
            <div class="header-left-info">
                <div class="breadcrumb-container">
                    <a href="/EventManagementSystem/public/admin/bookings" class="bc-link">Bookings</a> 
                    <span class="separator">❯</span> 
                    <a href="/EventManagementSystem/public/admin/bookings/view?id=<?php echo $id; ?>" class="bc-link current">Booking Detail</a>
                </div>
                <div class="title-section">
                    <h1>Booking #BK-<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?> <span class="badge-status <?php echo $displayStatus; ?>"><?php echo strtoupper($displayStatus); ?></span></h1>
                    <p class="sub-title"><?php echo htmlspecialchars($eventTitle); ?> — <?php echo ucfirst(strtolower($packageTier)); ?> Package</p>
                </div>
            </div>

            <div class="header-right-actions">
                <a href="/EventManagementSystem/public/admin/bookings" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Bookings</a>
                <div class="header-icons-center">
                    <button class="icon-btn-plain"><i class="fa-regular fa-bell"></i></button>
                    <button class="icon-btn-plain"><i class="fa-solid fa-gear"></i></button>
                    <div class="user-avatar-circle">
                        <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="detail-grid">
            <div class="grid-left-col">
                <!-- Client Card -->
                <div class="card client-card">
                    <div class="card-header-small">
                        <h4>Client Information</h4>
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="client-info-main">
                        <div class="client-avatar-large">
                            <?php if (!empty($booking['client_profile_pic'])): ?>
                                <img src="<?php echo htmlspecialchars($booking['client_profile_pic']); ?>" alt="Client" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                        <div class="client-details">
                            <h3><?php echo htmlspecialchars($fullName); ?></h3>
                            <div class="contact-row">
                                <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($booking['phone'] ?: '+977 9801234567'); ?></span>
                                <span><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($booking['email']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Overview Card -->
                <div class="card event-overview-card">
                    <div class="event-hero">
                        <img src="<?php echo $eventImage; ?>" alt="Event">
                        <div class="event-hero-overlay">
                            <span class="cat-chip"><?php echo htmlspecialchars($category); ?></span>
                            <h2><?php echo htmlspecialchars($eventTitle); ?></h2>
                            <p class="event-hero-desc"><?php echo htmlspecialchars($booking['event_description'] ?? 'Curating timeless moments for your once-in-a-lifetime celebration with architectural precision.'); ?></p>
                        </div>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="stat-item">
                            <label>Date</label>
                            <span><?php echo $eventDate->format('F d, Y'); ?></span>
                        </div>
                        <div class="stat-item">
                            <label>Guests</label>
                            <span><?php echo $guestCount; ?> Persons</span>
                        </div>
                        <div class="stat-item">
                            <label>Venue</label>
                            <span><?php echo htmlspecialchars($venueName); ?></span>
                        </div>
                        <div class="stat-item">
                            <label>Location</label>
                            <span><?php echo htmlspecialchars($venueLocation); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Package Details Card -->
                <div class="card package-card">
                    <div class="pkg-header">
                        <div class="pkg-title">
                            <h3><?php echo ucfirst(strtolower($packageTier)); ?> Package</h3>
                            <p><?php echo $packageDesc; ?></p>
                        </div>
                        <div class="pkg-price">
                            <span class="lbl">Price</span>
                            <span class="amt">Rs. <?php echo number_format($basePrice, 0); ?></span>
                        </div>
                    </div>

                    <div class="features-grid">
                        <?php if(!empty($features)): ?>
                            <?php foreach($features as $feat): ?>
                                <div class="feature-box">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><?php echo htmlspecialchars($feat); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Basic Event Management</span></div>
                            <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Standard Decoration</span></div>
                            <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Venue Coordination</span></div>
                            <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Essential Refreshments</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="grid-right-col">
                <!-- Booking Journey -->
                <div class="card">
                    <div class="card-header-small"><h4>Booking Journey</h4></div>
                    <div class="timeline">
                        <?php foreach($steps as $step): 
                            $cls = getStepClass($step['key'], $displayStatus, $currentDate, $eventDate);
                        ?>
                        <div class="timeline-item <?php echo $cls; ?>">
                            <div class="tl-dot">
                                <div class="dot-inner"><i class="fa-solid fa-check"></i></div>
                            </div>
                            <div class="tl-content">
                                <h5><?php echo $step['label']; ?></h5>
                                <p><?php echo $step['desc']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Manage Status -->
                <?php 
                    $hasButtons = ($displayStatus === 'pending' || $displayStatus === 'confirmed');
                ?>
                <div class="card <?php echo (!$hasButtons) ? 'card-status-empty' : ''; ?>">
                    <?php if (!$hasButtons): ?>
                        <div class="status-centered-box">
                            <h4>Manage Status</h4>
                            <span class="badge-status-lg <?php echo $displayStatus; ?>"><?php echo strtoupper($displayStatus); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="card-header-small">
                            <h4>Manage Status</h4>
                            <span class="badge-status <?php echo $displayStatus; ?>"><?php echo strtoupper($displayStatus); ?></span>
                        </div>
                        
                        <?php if($status === 'pending'): ?>
                            <form action="/EventManagementSystem/public/admin/bookings/approve" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to CONFIRM this booking?')">
                                <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn-manage btn-confirm"><i class="fa-solid fa-circle-check"></i> Confirm Booking</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if($status === 'pending' || $status === 'confirmed'): ?>
                            <form action="/EventManagementSystem/public/admin/bookings/cancel" method="POST"
                                  onsubmit="return confirm('Are you sure you want to CANCEL this booking? This action cannot be undone.')">
                                <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn-manage btn-cancel"><i class="fa-solid fa-circle-xmark"></i> Cancel Booking</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Financial Summary -->
                <div class="card">
                    <div class="card-header-small"><h4>Financial Summary</h4></div>
                    <div class="finance-row">
                        <span>Package Price</span>
                        <span>Rs. <?php echo number_format($basePrice, 0); ?></span>
                    </div>
                    <div class="finance-row total">
                        <span>Total Amount</span>
                        <span>Rs. <?php echo number_format($totalAmount, 0); ?></span>
                    </div>
                    
                    <div class="payment-status">
                        <span class="lbl">Payment Status</span>
                        <?php 
                            $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                            $payLabel = ($payStatus === 'paid') ? 'PAID' : 'PENDING';
                            $payCls = ($payStatus === 'paid') ? 'paid' : 'pending';
                        ?>
                        <span class="badge-status <?php echo $payCls; ?>"><?php echo $payLabel; ?></span>
                    </div>
                </div>

                <button class="btn-manage btn-message" disabled><i class="fa-regular fa-paper-plane"></i> Send Message to Client</button>
            </div>
        </div>
    </main>

</body>
</html>
