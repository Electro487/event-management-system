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
    function getStepClass($stepKey, $currentStatus, $currentDate, $eventDate)
    {
        if ($currentStatus === 'cancelled')
            return '';
        $todayStr = $currentDate->format('Y-m-d');
        $eventDateStr = $eventDate->format('Y-m-d');

        switch ($stepKey) {
            case 'received':
            case 'review':
                return 'completed';
            case 'confirmed':
                if ($currentStatus === 'confirmed' || $currentStatus === 'completed')
                    return 'completed';
                return '';
            case 'event':
                if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed')
                    return '';
                if ($todayStr === $eventDateStr)
                    return 'active';
                if ($todayStr > $eventDateStr)
                    return 'completed';
                return '';
            case 'completed':
                if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed')
                    return '';
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
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-booking-details.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/events#my-bookings" class="active">My Bookings</a>
        </nav>
        <div class="nav-icons">
            <div class="notifications-wrapper">
                <div class="notification-bell-btn" id="notification-bell">
                    <i class="fa-regular fa-bell"></i>
                    <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                </div>
                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown" id="notifications-dropdown">
                    <div class="nd-header">
                        <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 New</span></h3>
                        <a href="javascript:void(0)" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                    </div>
                    <div class="nd-content" id="nd-list">
                        <div class="nd-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            <p>No new notifications</p>
                        </div>
                    </div>
                    <div class="nd-footer">
                        <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $headerInitials = '';
                $nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
                foreach ($nameParts as $p) {
                    $headerInitials .= strtoupper(substr($p, 0, 1));
                }
                if (strlen($headerInitials) > 2) $headerInitials = substr($headerInitials, 0, 2);
                ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="header-avatar">
                        <?php else: ?>
                            <span id="header-initials"><?php echo htmlspecialchars($headerInitials); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Dropdown Modal -->
                    <div id="profile-dropdown" class="profile-dropdown">
                        <div class="pd-top">
                            <div class="pd-avatar-container">
                                <div class="pd-avatar">
                                    <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">
                                    <?php else: ?>
                                        <span id="dropdown-initials"><?php echo htmlspecialchars($headerInitials); ?></span>
                                    <?php endif; ?>
                                </div>
                                <label for="profile_picture_upload" class="pd-edit-icon" title="Change Photo">
                                    <i class="fa-solid fa-pen"></i>
                                </label>
                                <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                    <div class="pd-delete-icon" onclick="deleteProfilePicture()" title="Remove Photo">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="profile_picture_upload" accept="image/*" style="display: none;" onchange="uploadProfilePicture(this)">
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
                            <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            <span class="pd-role"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Client')); ?></span>
                        </div>
                        <div class="pd-bottom">
                            <?php
                            $firstName = $nameParts[0] ?? '';
                            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                            ?>
                            <div class="pd-detail">
                                <label>FIRST NAME</label>
                                <div><?php echo htmlspecialchars($firstName); ?></div>
                            </div>
                            <div class="pd-detail">
                                <label>LAST NAME</label>
                                <div><?php echo htmlspecialchars($lastName); ?></div>
                            </div>
                            <div class="pd-detail">
                                <label>EMAIL ADDRESS</label>
                                <div><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
                            </div>

                            <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <script>
                    function toggleProfileDropdown() {
                        const dropdown = document.getElementById('profile-dropdown');
                        dropdown.classList.toggle('show');
                    }

                    // Hide dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const container = document.getElementById('profile-container');
                        if (container && !container.contains(event.target)) {
                            document.getElementById('profile-dropdown').classList.remove('show');
                        }
                    });

                    function uploadProfilePicture(input) {
                        if (input.files && input.files[0]) {
                            const formData = new FormData();
                            formData.append('profile_picture', input.files[0]);

                            fetch('/EventManagementSystem/public/client/profile/update', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update header avatar
                                        let headerIcon = document.getElementById('profile-icon');
                                        headerIcon.innerHTML = '<img src="' + data.path + '" style="width: 100%; height: 100%; object-fit: cover;" id="header-avatar">';

                                        // Update dropdown avatar
                                        let dropdownAvatar = document.querySelector('.pd-avatar');
                                        dropdownAvatar.innerHTML = '<img src="' + data.path + '" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">';

                                        // Add delete icon if not exists
                                        if (!document.querySelector('.pd-delete-icon')) {
                                            let avatarContainer = document.querySelector('.pd-avatar-container');
                                            let deleteBtn = document.createElement('div');
                                            deleteBtn.className = 'pd-delete-icon';
                                            deleteBtn.title = 'Remove Photo';
                                            deleteBtn.onclick = deleteProfilePicture;
                                            deleteBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                                            avatarContainer.appendChild(deleteBtn);
                                        }
                                    } else {
                                        alert(data.message || 'Error uploading image.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred during upload.');
                                });
                        }
                    }

                    function deleteProfilePicture() {
                        if (confirm('Are you sure you want to remove your profile picture?')) {
                            fetch('/EventManagementSystem/public/client/profile/delete-picture', {
                                    method: 'POST'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const initialsElement = '<span id="header-initials"><?php echo htmlspecialchars($headerInitials); ?></span>';

                                        // Update header avatar
                                        let headerIcon = document.getElementById('profile-icon');
                                        headerIcon.innerHTML = initialsElement;

                                        // Update dropdown avatar
                                        let dropdownAvatar = document.querySelector('.pd-avatar');
                                        dropdownAvatar.innerHTML = '<span id="dropdown-initials"><?php echo htmlspecialchars($headerInitials); ?></span>';

                                        // Remove delete icon if exists
                                        let deleteIcon = document.querySelector('.pd-delete-icon');
                                        if (deleteIcon) deleteIcon.remove();
                                    } else {
                                        alert('Error removing image.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred.');
                                });
                        }
                    }
                </script>
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
                            <span
                                class="info-val"><?php echo date('F d, Y', strtotime($booking['event_date'])); ?></span>
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
                            <span class="info-val"><?php echo htmlspecialchars($booking['guest_count']); ?>
                                Attendees</span>
                            <i class="fa-solid fa-user-group info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Primary Email</span>
                            <span class="info-val"
                                style="word-break:break-all; font-size:14px;"><?php echo htmlspecialchars($booking['email']); ?></span>
                            <i class="fa-regular fa-envelope info-icon"></i>
                        </div>
                    </div>

                    <div class="pkg-details">
                        <div class="pkg-header">
                            <span class="pkg-name">Package Details</span>
                            <span class="pkg-tier-label"><?php echo htmlspecialchars($booking['package_tier']); ?>
                                Package</span>
                        </div>
                        <p class="pkg-desc">
                            <?php echo htmlspecialchars($selectedPackage['description'] ?? 'Your selected package features exclusive services carefully curated by our team.'); ?>
                        </p>

                        <h4 style="font-size:13px; margin-bottom:10px; color:#1f6f59;">WHAT'S INCLUDED:</h4>
                        <ul class="items-list">
                            <?php foreach ($items as $item): ?>
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
                            <span
                                class="info-val"><?php echo htmlspecialchars($booking['venue_name'] ?: 'Venue TBD'); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">
                                <?php echo htmlspecialchars($booking['venue_location'] ?: 'Address will be confirmed shortly.'); ?>
                            </span>
                        </div>
                        <div class="info-item" style="border-left-color: #ffc241;">
                            <span class="info-label">Organizer</span>
                            <span
                                class="info-val"><?php echo htmlspecialchars($booking['organizer_name'] ?: 'e-Plan Elite Team'); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">Lead
                                Architect & Coordinator</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Journey -->
            <div class="right-col">
                <div class="card-section journey-card">
                    <h2 class="card-title"><i class="fa-solid fa-route"></i> Booking Journey</h2>
                    <div class="timeline">
                        <?php foreach ($steps as $step):
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
                        <span>Total Booking Amount</span>
                        <span>Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>

                    <?php
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    $advance = $advanceTarget;
                    $balance = $booking['total_amount'] * 0.5;
                    $hasAnyAdvancePaid = ($paidAdvance > 0.009);
                    $isAdvanceComplete = ($remainingAdvance <= 0.009);

                    $isPartiallyPaid = ($payStatus === 'partially_paid');
                    $isFullyPaid = ($payStatus === 'paid');
                    ?>

                    <div class="price-row">
                        <span>Advance (50% Online)</span>
                        <span style="color: <?php echo ($isAdvanceComplete || $isFullyPaid) ? '#10b981' : '#64748b'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($paidAdvance, 2); ?> / <?php echo number_format($advance, 2); ?>
                            <?php if ($isAdvanceComplete || $isFullyPaid): ?><i class="fa-solid fa-check-circle"></i><?php endif; ?>
                        </span>
                    </div>

                    <div class="price-row">
                        <span>Remaining Online Advance</span>
                        <span style="color: <?php echo $isAdvanceComplete ? '#10b981' : '#ef4444'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($remainingAdvance, 2); ?>
                        </span>
                    </div>

                    <div class="price-row">
                        <span>Balance (50% Cash on Event Day)</span>
                        <span style="color: <?php echo $isFullyPaid ? '#10b981' : '#f59e0b'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($balance, 2); ?>
                            <?php if ($isFullyPaid): ?><i class="fa-solid fa-check-circle"></i><?php endif; ?>
                        </span>
                    </div>

                    <div class="price-row total <?php echo ($isPartiallyPaid || $isFullyPaid) ? 'paid' : 'pending'; ?>" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                        <span>Current Status</span>
                        <span>
                            <?php
                            if ($isFullyPaid) echo 'FULLY PAID';
                            elseif ($isAdvanceComplete) echo 'ADVANCE COMPLETE';
                            elseif ($hasAnyAdvancePaid) echo 'ADVANCE PARTIALLY PAID';
                            else echo 'PAYMENT PENDING';
                            ?>
                        </span>
                    </div>

                    <?php if (!$isFullyPaid && !$isAdvanceComplete): ?>
                        <div style="margin-top: 20px;">
                            <a href="/EventManagementSystem/public/client/payment/checkout?booking_id=<?php echo $booking['id']; ?>"
                                class="btn-primary"
                                style="display: block; text-align: center; background: #246A55; color: white;">
                                <i class="fa-solid fa-credit-card"></i> Pay Next Installment (Rs. <?php echo number_format($nextInstallmentAmount, 2); ?>)
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="policy-note" style="margin-top:15px; padding:12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                        <span style="font-size:12px; color:#0369a1; display:flex; gap:8px; line-height:1.4;">
                            <i class="fa-solid fa-circle-info" style="margin-top:2px;"></i>
                            <span><b>Payment Policy:</b> Advanced payments are non-refundable. Remaining 50% balance must be settled in cash with the organizer on the day of the event.</span>
                        </span>
                    </div>

                    <?php
                    $isLocked = ($displayStatus === 'confirmed' && $payStatus !== 'unpaid');
                    $canCancel = ($displayStatus === 'pending' || ($displayStatus === 'confirmed' && $payStatus === 'unpaid'));
                    ?>

                    <?php if ($canCancel): ?>
                        <div style="margin-top: 15px;">
                            <form action="/EventManagementSystem/public/client/bookings/cancel" method="POST"
                                style="margin:0;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                                    <i class="fa-solid fa-xmark"></i> Cancel Reservation
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <a href="/EventManagementSystem/public/client/events#my-bookings" class="btn-primary"
                        style="margin-top: 15px; color:#1a1e23; background:#ffc241;">
                        <i class="fa-solid fa-arrow-left"></i> Back to My Bookings
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-left">
            <div class="footer-logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                    style="height: 28px; width: auto; object-fit: contain;"></div>
            <div class="copyright">&copy; 2026 e.plan Architectural Event Curation. All rights reserved.</div>
        </div>
        <div class="footer-links">
            <a href="#">PRIVACY POLICY</a>
            <a href="#">TERMS OF SERVICE</a>
            <a href="#">CONTACT SUPPORT</a>
        </div>
    </footer>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>

</html>
