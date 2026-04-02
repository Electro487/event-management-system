<?php
$bgImage = !empty($booking['event_image']) ? $booking['event_image'] : '/EventManagementSystem/public/assets/images/placeholder.jpg';
$eventTitle = htmlspecialchars($booking['event_title']);
$displayStatus = $booking['display_status'] ?? strtolower($booking['status']);
$statusStr = strtoupper($displayStatus);
$statusClass = "status-" . strtolower($displayStatus);

// Parse package features
$items = $selectedPackage['items'] ?? [];
if (empty($items)) {
    if ($booking['package_tier'] == 'premium') {
        $items = [['title' => 'Exclusive Catering & Decor'], ['title' => 'Premium 5-course meal'], ['title' => 'Luxury imported floral arrangements']];
    } else if ($booking['package_tier'] == 'standard') {
         $items = [['title' => 'Full Venue Coordination'], ['title' => 'Premium Floral Arrangement'], ['title' => 'Standard Catering (150 guests)'], ['title' => 'Live String Quartet']];
    } else {
         $items = [['title' => 'Basic Management'], ['title' => 'Standard Decor'], ['title' => 'Venue Rental']];
    }
}

// Timeline Logic
$currentDate = new DateTime();
$todayStr = $currentDate->format('Y-m-d');
$eventDate = new DateTime($booking['event_date']);
$eventDateStr = $eventDate->format('Y-m-d');
$status = strtolower($booking['status']);

if (!function_exists('getStepClass')) {
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
}

$steps = [
    ['label' => 'Received', 'desc' => 'Booking received', 'key' => 'received'],
    ['label' => 'Under Review', 'desc' => ($status === 'cancelled') ? 'Booking cancelled' : 'Awaiting review', 'key' => 'review'],
    ['label' => 'Confirmed', 'desc' => ($status === 'confirmed' || $status === 'completed') ? 'Booking confirmed' : 'Pending confirmation', 'key' => 'confirmed'],
    ['label' => 'Event Day', 'desc' => 'Scheduled for ' . $eventDate->format('M d, Y'), 'key' => 'event'],
    ['label' => 'Completed', 'desc' => ($todayStr > $eventDateStr && $status !== 'cancelled') ? 'Event completed' : 'Awaiting event day', 'key' => 'completed']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #EPLN-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?> - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-booking-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo">e-Plan</a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
            <a href="#">About</a>
        </nav>
        <div class="nav-icons">
            <i class="fa-regular fa-bell" style="font-size: 20px; color: #1f6f59; cursor: pointer;"></i>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="width: 32px; height: 32px; background: #1f6f59; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; cursor: pointer;">
                    <?php echo strtoupper(substr($_SESSION['user_fullname'], 0, 1)); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Immersive Hero Background -->
    <div class="hero">
        <img src="<?php echo htmlspecialchars($bgImage); ?>" alt="Event Background">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="category-tag"><?php echo htmlspecialchars($booking['event_category'] ?: 'Event'); ?></span>
            <h1><?php echo $eventTitle; ?></h1>
            <p>Your curated architectural event experience is <?php echo strtolower($statusStr); ?>.</p>
        </div>
    </div>

    <div class="container">
        <!-- Breadcrumbs/Status Bar -->
        <div class="status-bar">
            <div class="sb-left">
                <span class="sb-ref">BOOKING REF:</span>
                <span class="sb-id">#EPLN-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="status-badge <?php echo $statusClass; ?>">
                <?php echo $statusStr; ?>
            </div>
        </div>

        <!-- 2-Column Grid -->
        <div class="content-grid">
            
            <!-- Left Column: All Booking Details -->
            <div class="left-col">
                <div class="card-section">
                    <h2 class="card-title"><i class="fa-regular fa-id-badge"></i> Booking Information</h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Booked By</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['full_name']); ?></span>
                            <i class="fa-regular fa-user info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Event Date</span>
                            <span class="info-val"><?php echo date('F d, Y', strtotime($booking['event_date'])); ?></span>
                            <i class="fa-regular fa-calendar info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Check-in Time</span>
                            <span class="info-val"><?php 
                                $time = !empty($booking['checkin_time']) ? $booking['checkin_time'] : '10:00 AM';
                                // If it's in 24hr format from input (HH:mm), convert to AM/PM
                                if (preg_match('/^\d{2}:\d{2}$/', $time)) {
                                    echo date('h:i A', strtotime($time)); 
                                } else {
                                    echo htmlspecialchars($time);
                                }
                            ?></span>
                            <i class="fa-regular fa-clock info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Guests</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['guest_count']); ?> Attendees</span>
                            <i class="fa-solid fa-user-group info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Primary Email</span>
                            <span class="info-val" style="word-break:break-all; font-size:14px;"><?php echo htmlspecialchars($booking['email']); ?></span>
                            <i class="fa-regular fa-envelope info-icon"></i>
                        </div>
                    </div>

                    <div class="pkg-details">
                        <div class="pkg-header">
                            <span class="pkg-name">Package Details</span>
                            <span class="pkg-tier-label"><?php echo htmlspecialchars($booking['package_tier']); ?> Package</span>
                        </div>
                        <p class="pkg-desc"><?php echo htmlspecialchars($selectedPackage['description'] ?? 'Your selected package features exclusive services carefully curated by our team.'); ?></p>
                        
                        <h4 style="font-size:13px; margin-bottom:10px; color:#1f6f59;">WHAT'S INCLUDED:</h4>
                        <ul class="items-list">
                            <?php foreach($items as $item): ?>
                                <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($item['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Venue & Organizer Info -->
                <div class="card-section">
                    <h2 class="card-title"><i class="fa-solid fa-location-dot"></i> Venue & Organizer</h2>
                    <div class="info-grid" style="grid-template-columns: 1fr;">
                        <div class="info-item" style="border-left-color: #ffc241;">
                            <span class="info-label">Venue</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['venue_name'] ?: 'Venue TBD'); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">
                                <?php echo htmlspecialchars($booking['venue_location'] ?: 'Address will be confirmed shortly.'); ?>
                            </span>
                        </div>
                        <div class="info-item" style="border-left-color: #ffc241;">
                            <span class="info-label">Organizer</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['organizer_name'] ?: 'e-Plan Elite Team'); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">Lead Architect & Coordinator</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Journey -->
            <div class="right-col">
                <div class="card-section journey-card">
                    <h2 class="card-title"><i class="fa-solid fa-route"></i> Booking Journey</h2>
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

                <div class="summary-box">
                    <h3>Payment Summary</h3>
                    
                    <div class="price-row">
                        <span><?php echo ucfirst($booking['package_tier']); ?> Package</span>
                        <span>Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>
                    
                    <div class="price-row total">
                        <span>Total Paid</span>
                        <span>Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>

                    <?php if (in_array($displayStatus, ['pending', 'confirmed'])): ?>
                        <div style="margin-top: 30px;">
                            <form action="/EventManagementSystem/public/client/bookings/cancel" method="POST" style="margin:0;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                                    <i class="fa-solid fa-xmark"></i> Cancel Reservation
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <a href="/EventManagementSystem/public/client/events#my-bookings" class="btn-primary" style="margin-top: 15px; color:#1a1e23; background:#ffc241;">
                        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-left">
            <div class="footer-logo">e-Plan</div>
            <div class="copyright">&copy; 2026 e.plan Architectural Event Curation. All rights reserved.</div>
        </div>
        <div class="footer-links">
            <a href="#">PRIVACY POLICY</a>
            <a href="#">TERMS OF SERVICE</a>
            <a href="#">CONTACT SUPPORT</a>
        </div>
    </footer>

</body>
</html>
