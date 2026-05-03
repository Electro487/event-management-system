<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Ticket Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        .tier-concert-av { background: #E0F2FE; color: #0369A1; }
        .pkg-badge.concert { background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; }
    </style>
</head>
<body>

    <?php
    $activePage = 'tickets';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header b-header">
            <div class="header-left b-header-left">
                <h1>Global Ticket Management</h1>
                <p>Oversight of all concert ticket reservations across the system</p>
            </div>

            <div class="header-right b-header-right">
                <div class="search-wrap top-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="globalSearchInput" placeholder="Search attendees or concerts...">
                </div>
                <div class="header-actions">
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
                                <div class="nd-empty"><i class="fa-regular fa-bell-slash"></i><p>No new notifications</p></div>
                            </div>
                            <div class="nd-footer">
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="user-avatar-small"><?php include_once __DIR__ . '/partials/header_profile.php'; ?></div>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-row">
            <div class="stat-box"><p class="stat-title">Total Tickets</p><h2 class="stat-number dark" id="stat-total-bookings">0</h2></div>
            <div class="stat-box"><p class="stat-title">Confirmed</p><h2 class="stat-number green" id="stat-confirmed-count">0</h2></div>
            <div class="stat-box"><p class="stat-title">Pending</p><h2 class="stat-number orange" id="stat-pending-count">0</h2></div>
            <div class="stat-box"><p class="stat-title">Cancelled</p><h2 class="stat-number red" id="stat-cancelled-count">0</h2></div>
        </div>

        <!-- Filter Row -->
        <div class="filters-row">
            <div class="search-wrap bottom-search">
                <i class="fas fa-search"></i>
                <input type="text" id="filterSearchInput" placeholder="Filter by attendee, concert or organizer...">
            </div>

            <div class="custom-premium-dropdown" id="packageDropdown">
                <div class="dropdown-trigger"><span class="selected-val">All Tiers</span><i class="fa-solid fa-angle-down"></i></div>
                <div class="dropdown-menu">
                    <div class="dropdown-item active" data-value="all">All Tiers</div>
                    <div class="dropdown-item" data-value="basic">Basic</div>
                    <div class="dropdown-item" data-value="standard">Standard</div>
                    <div class="dropdown-item" data-value="premium">Premium</div>
                </div>
            </div>

            <div class="custom-date"><i class="fa-regular fa-calendar"></i><input type="text" placeholder="Select Date" id="dateFilter" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'"></div>
        </div>

        <div class="status-tabs">
            <button class="status-tab active" data-status="all">All <span class="badge" id="tab-all-count">0</span></button>
            <button class="status-tab" data-status="pending">Pending <span class="badge" id="tab-pending-count">0</span></button>
            <button class="status-tab" data-status="confirmed">Confirmed <span class="badge" id="tab-confirmed-count">0</span></button>
            <button class="status-tab" data-status="completed">Completed <span class="badge" id="tab-completed-count">0</span></button>
            <button class="status-tab" data-status="cancelled">Cancelled <span class="badge" id="tab-cancelled-count">0</span></button>
        </div>

        <div class="bookings-list-container">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">ATTENDEE</th>
                        <th>CONCERT & ORGANIZER</th>
                        <th>TIER</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th style="text-align:right; padding-right:24px;">ACTION</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody"><tr><td colspan="7" class="no-data">Loading tickets...</td></tr></tbody>
            </table>
            <div class="pagination-row">
                <div class="showing-text">Showing <span id="visibleCount">0</span> of <span id="totalBookingsSpan">0</span> tickets</div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/tickets.js?v=<?php echo time(); ?>"></script>
</body>
</html>
