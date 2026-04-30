<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php
    $activePage = 'notifications';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <!-- Minimal header -->
        <header class="header">
            <form action="/EventManagementSystem/public/admin/events" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search system-wide...">
                <button type="submit" style="display:none;"></button>
            </form>
            <div class="header-icons">
                <div class="header-actions" style="margin-right: 15px;">
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
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>

        </header>

        <!-- HERO -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/admin/dashboard" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1>Notification Centre</h1>
                <p>Stay on top of all system activity - new bookings, event campaigns, and administrative actions.</p>
            </div>
            <div class="np-hero-right" id="hero-actions-container">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span id="total-count-label">0 Total Alerts</span>
                </div>
                <button class="np-unread-all-btn" onclick="markAllRead()">
                    <i class="fa-solid fa-check-double"></i> Mark all as read
                </button>
                <button class="np-unread-all-btn" onclick="markAllUnread()">
                    <i class="fa-solid fa-envelope-open"></i> Mark all as unread
                </button>
                <button class="np-clear-all-btn" onclick="clearAllNotifications()">
                    <i class="fa-solid fa-trash-can"></i> Clear All
                </button>
            </div>
        </div>

        <!-- STATS -->
        <div class="np-stats-row">
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-bell"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Total</div>
                    <div class="np-stat-value" id="stat-total">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-bookmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Booking Requests</div>
                    <div class="np-stat-value" id="stat-booking">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-regular fa-calendar-alt"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Creation</div>
                    <div class="np-stat-value" id="stat-creation">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Cancellations</div>
                    <div class="np-stat-value" id="stat-cancel">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-message"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Messages</div>
                    <div class="np-stat-value" id="stat-message">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-pen-to-square"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Updates</div>
                    <div class="np-stat-value" id="stat-update">0</div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-credit-card"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Payments</div>
                    <div class="np-stat-value" id="stat-payment">0</div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="np-filter-bar">
            <button class="np-filter-tab active" data-filter-type="all">
                <i class="fa-solid fa-border-all"></i> All
                <span class="np-filter-count" id="count-all">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="booking">
                <i class="fa-solid fa-bookmark"></i> Booking Requests
                <span class="np-filter-count" id="count-booking">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="event">
                <i class="fa-regular fa-calendar-alt"></i> Event Creation
                <span class="np-filter-count" id="count-creation">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="event_updates">
                <i class="fa-solid fa-bolt"></i> Event Updates
                <span class="np-filter-count" id="count-update">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="message">
                <i class="fa-solid fa-message"></i> Messages
                <span class="np-filter-count" id="count-message">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="booking_cancel">
                <i class="fa-solid fa-circle-xmark"></i> Cancellations
                <span class="np-filter-count" id="count-cancel">0</span>
            </button>
            <button class="np-filter-tab" data-filter-type="payment_alert">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="np-filter-count" id="count-payment">0</span>
            </button>
        </div>

        <div id="notifications-container">
            <div class="np-empty-state">
                <div class="np-empty-icon"><i class="fa-regular fa-bell"></i></div>
                <h3>Loading Notifications...</h3>
            </div>
        </div>
        <div id="pagination-container" class="pagination-container" style="margin-top: 25px;"></div>

    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/notifications.js?v=<?php echo time(); ?>"></script>
</body>

</html>