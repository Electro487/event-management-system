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
    <style>
        .admin-badge { background: #e6fcf0; color: #246A55; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    </style>
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
            <form action="/EventManagementSystem/public/admin/events" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search system-wide..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" style="display:none;"></button>
            </form>
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
                <p>The system is currently hosting <?php echo number_format($totalUsers ?? 0); ?> users and <?php echo number_format($totalEvents ?? 0); ?> active event campaigns.</p>
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
                <h3><?php echo number_format($totalUsers ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e6fcf0; color: #246A55;"><i class="far fa-calendar-alt"></i></div>
                </div>
                <p>Total Events</p>
                <h3><?php echo number_format($totalEvents ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f0fdf4; color: #246A55;"><i class="fa-solid fa-bookmark"></i></div>
                </div>
                <p>Total Bookings</p>
                <h3><?php echo number_format($totalBookings ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e6fcf0; color: #246A55;"><i class="fas fa-exclamation-circle"></i></div>
                </div>
                <p>Pending Requests</p>
                <h3><?php echo number_format($pendingRequests ?? 0); ?></h3>
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
                        <tbody>
                            <?php if (empty($recentBookings)): ?>
                                <tr><td colspan="5" style="text-align:center;">No recent bookings found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="client-info" style="display: flex; align-items: center; gap: 12px;">
                                                <?php if (!empty($booking['client_profile_pic'])): ?>
                                                    <img src="<?php echo htmlspecialchars($booking['client_profile_pic']); ?>" alt="Client" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                                <?php else: ?>
                                                    <div style="width: 32px; height: 32px; background: #f0f7f3; color: #246A55; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                                                        <?php 
                                                            $nameArr = explode(' ', $booking['client_name']);
                                                            $initArr = array_filter($nameArr);
                                                            $init = '';
                                                            foreach(array_slice($initArr, 0, 2) as $n) $init .= strtoupper(substr($n, 0, 1));
                                                            echo $init ?: '??';
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span style="font-weight: 500; font-size: 13.5px; color: var(--text-main);"><?php echo htmlspecialchars($booking['client_name']); ?></span>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-main); font-weight:500;"><?php echo htmlspecialchars($booking['event_title']); ?></td>
                                        <td><span class="admin-badge"><?php echo htmlspecialchars($booking['organizer_name'] ?? 'System'); ?></span></td>
                                        <td>
                                            <span class="badge <?php echo htmlspecialchars($booking['display_status'] ?? $booking['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['display_status'] ?? $booking['status'])); ?>
                                            </span>
                                        </td>
                                         <td>
                                            <a href="/EventManagementSystem/public/admin/bookings/view?id=<?php echo $booking['id']; ?>" style="color: #246A55; font-weight: 700; text-decoration: none; font-size: 13px;">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                            <span style="background:#e6fcf0; color:#246A55; padding:4px 10px; border-radius:12px; font-weight:600; font-size:12px; white-space: nowrap;">
                                <?php echo count($upcomingEvents); ?> Active
                            </span>
                        </h3>
                    </div>
                    <div class="events-list">
                        <?php if (empty($upcomingEvents)): ?>
                            <p style="text-align:center; color:var(--text-muted); font-size:14px;">No upcoming events.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <?php
                                    if (empty($event['event_date'])) {
                                        $daysText = "Ongoing";
                                    } else {
                                        $eventDate = new DateTime($event['event_date']);
                                        $now = new DateTime();
                                        $diff = $now->diff($eventDate);
                                        $daysLeft = $diff->days;
                                        
                                        if ($eventDate->format('Y-m-d') === $now->format('Y-m-d')) {
                                            $daysText = "Today";
                                        } elseif ($diff->invert) {
                                            $daysText = "Ongoing";
                                        } else {
                                            $daysText = "in {$daysLeft} days";
                                        }
                                    }
                                ?>
                                <a href="/EventManagementSystem/public/admin/events/view?id=<?php echo $event['id']; ?>" class="event-item" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px;">
                                    <?php 
                                        $eventImg = !empty($event['image_path']) ? $event['image_path'] : '/EventManagementSystem/public/assets/images/placeholder.jpg';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($eventImg); ?>" alt="Event Image" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg'" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                                    <div class="event-info" style="flex: 1; min-width: 0;">
                                        <h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($event['title']); ?>
                                        </h4>
                                        <div class="event-meta" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <span class="category" style="white-space: nowrap; background: #f4f7f6; padding: 3px 8px; border-radius: 4px; font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
                                            <span class="date" style="white-space: nowrap; color: #e74c3c; font-size: 12px; font-weight: 600; margin-left: auto;"><?php echo $daysText; ?></span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: var(--text-muted); font-size: 14px; flex-shrink: 0;"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
