<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Sidebar -->
    <?php
    $activePage = 'dashboard';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="globalSearchInput" placeholder="Search system-wide events..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="header-icons">
                <div class="notifications-wrapper">
                    <div class="notification-bell-btn" id="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                    </div>
                    <div class="notifications-dropdown" id="notifications-dropdown">
                        <div class="nd-header">
                            <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 UNREAD</span></h3>
                            <a href="#" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                        </div>
                        <div class="nd-content" id="nd-list">
                            <div class="nd-empty">
                                <i class="fa-regular fa-bell-slash"></i>
                                Loading notifications...
                            </div>
                        </div>
                        <div class="nd-footer">
                            <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <!-- Welcome Banner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, #246A55 0%, #067453 100%);">
            <div>
                <h2>System Overview: Hello, Admin! 👋</h2>
                <p>The system is currently hosting <span id="welcome-total-users">...</span> users and <span id="welcome-total-events">...</span> active event campaigns.</p>
            </div>
            <div class="header-actions">
                <span class="admin-badge" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">Root Access</span>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e6fcf0; color: #246A55;"><i class="fas fa-users"></i></div>
                </div>
                <p>Total Users</p>
                <h3 id="stat-total-users">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e6fcf0; color: #246A55;"><i class="far fa-calendar-alt"></i></div>
                </div>
                <p>Total Events</p>
                <h3 id="stat-total-events">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f0fdf4; color: #246A55;"><i class="fa-solid fa-bookmark"></i></div>
                </div>
                <p>Total Bookings</p>
                <h3 id="stat-total-bookings">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e6fcf0; color: #246A55;"><i class="far fa-money-bill-alt"></i></div>
                </div>
                <p>Revenue</p>
                <h3 id="stat-revenue">Rs. 0.00</h3>
            </div>
        </div>

        <!-- Bottom Grid Section -->
        <div class="bottom-grid">

            <!-- Left Column -->
            <div class="left-col">
                <!-- Recent Bookings Table -->
                <div class="recent-bookings" style="margin-bottom: 25px;">
                    <div class="section-header">
                        <h3>System-wide Recent Bookings</h3>
                        <a href="/EventManagementSystem/public/admin/bookings">Manage All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Event</th>
                                <th>Organizer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="recent-bookings-body">
                            <tr>
                                <td colspan="5" style="text-align:center;">Loading recent bookings...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="upcoming-events">
                    <div class="section-header" style="margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
                        <h3 style="display: flex; align-items: center; gap: 10px; margin: 0; white-space: nowrap;">
                            Upcoming Events
                            <span id="upcoming-events-count" style="background:#e6fcf0; color:#246A55; padding:4px 10px; border-radius:12px; font-weight:600; font-size:12px; white-space: nowrap;">
                                ... Active
                            </span>
                        </h3>
                    </div>
                    <div class="events-list" id="upcoming-events-list">
                        <p style="text-align:center; color:var(--text-muted); font-size:14px;">Loading upcoming events...</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>
