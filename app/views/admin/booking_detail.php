<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Data Preparation
$id = $_GET['id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #BK-<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?> | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking-detail.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
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
                    <a href="/EventManagementSystem/public/admin/bookings/view?id=<?php echo $id; ?>"
                        class="bc-link current">Booking Detail</a>
                </div>
                <div class="title-section">
                    <h1>Booking <span id="booking-id-pad">...</span> <span class="badge-status"
                            id="booking-status-badge">...</span></h1>
                    <p class="sub-title"><span id="event-title-display">...</span> - <span
                            id="package-tier-display">...</span> Package</p>
                </div>
            </div>

            <div class="header-right-actions">
                <a href="/EventManagementSystem/public/admin/bookings" class="back-link"><i
                        class="fa-solid fa-arrow-left"></i> Back to Bookings</a>
                <div class="header-icons-center">
                    <div class="notifications-wrapper">
                        <div class="notification-bell-btn" id="notification-bell">
                            <i class="fa-regular fa-bell"></i>
                            <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                        </div>
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
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All
                                    Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
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
                        <div class="client-avatar-large" id="client-avatar-display">
                            --
                        </div>
                        <div class="client-details">
                            <h3 id="client-name-display">...</h3>
                            <div class="contact-row">
                                <span><i class="fa-solid fa-phone"></i> <span
                                        id="client-phone-display">...</span></span>
                                <span><i class="fa-solid fa-envelope"></i> <span
                                        id="client-email-display">...</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Overview Card -->
                <div class="card event-overview-card">
                    <div class="event-hero">
                        <img src="/EventManagementSystem/public/assets/images/placeholder.jpg" alt="Event"
                            id="event-hero-img">
                        <div class="event-hero-overlay">
                            <span class="cat-chip" id="event-category-display">...</span>
                            <h2 id="event-hero-title-display">...</h2>
                            <p class="event-hero-desc" id="event-desc-display">...</p>
                        </div>
                    </div>

                    <div class="quick-stats">
                        <div class="stat-item">
                            <label>Date</label>
                            <span id="event-date-display">...</span>
                        </div>
                        <div class="stat-item">
                            <label>Guests</label>
                            <span id="guest-count-display">...</span>
                        </div>
                        <div class="stat-item">
                            <label>Venue</label>
                            <span id="venue-name-display">...</span>
                        </div>
                        <div class="stat-item">
                            <label>Location</label>
                            <span id="venue-location-display">...</span>
                        </div>
                    </div>
                </div>

                <!-- Package Details Card -->
                <div class="card package-card">
                    <div class="pkg-header">
                        <div class="pkg-title">
                            <h3 id="pkg-tier-name">... Package</h3>
                            <p id="pkg-desc">...</p>
                        </div>
                        <div class="pkg-price">
                            <span class="lbl">Price</span>
                            <span class="amt" id="pkg-price-display">Rs. 0</span>
                        </div>
                    </div>

                    <div class="features-grid" id="pkg-features-list">
                        <div class="feature-box"><span>Loading features...</span></div>
                    </div>
                </div>
            </div>

            <div class="grid-right-col">
                <!-- Booking Journey -->
                <div class="card">
                    <div class="card-header-small">
                        <h4>Booking Journey</h4>
                    </div>
                    <div class="timeline" id="booking-timeline">
                        <div class="timeline-item">
                            <div class="tl-content">
                                <p>Loading timeline...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="manage-status-card">
                    <div class="card-header-small">
                        <h4>Manage Status</h4>
                        <span class="badge-status" id="manage-status-badge">...</span>
                    </div>
                    <div id="status-actions-container" style="padding-top: 15px;">
                        <!-- Actions injected via JS -->
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="card">
                    <div class="card-header-small">
                        <h4>Financial Summary</h4>
                    </div>
                    <div class="finance-row">
                        <span>Total Amount</span>
                        <span id="finance-total-amount">Rs. 0</span>
                    </div>

                    <div class="finance-row">
                        <span>Advance (50% Online)</span>
                        <span id="finance-advance-display">
                            Rs. 0
                        </span>
                    </div>

                    <div class="finance-row">
                        <span>Remaining (50% Cash)</span>
                        <span id="finance-remaining-display">
                            Rs. 0
                        </span>
                    </div>

                    <div class="payment-status">
                        <span class="lbl">Current Status</span>
                        <span class="val" id="finance-status-label">...</span>
                    </div>
                </div>

                <button class="btn-manage btn-message" disabled><i class="fa-regular fa-paper-plane"></i> Send Message
                    to Client</button>
            </div>
        </div>
    </main>

    <script>
        window.BOOKING_ID = <?php echo (int) ($id ?? 0); ?>;
    </script>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/booking_detail.js?v=<?php echo time(); ?>"></script>
</body>

</html>