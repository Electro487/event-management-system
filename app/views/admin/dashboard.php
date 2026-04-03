<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <style>
        .admin-badge { background: #eef2ff; color: #4338ca; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
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
                <i class="far fa-bell"></i>

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
                    <div class="stat-icon" style="background: #eef2ff; color: #4338ca;"><i class="fas fa-users"></i></div>
                </div>
                <p>Total Users</p>
                <h3><?php echo number_format($totalUsers ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #fff7ed; color: #c2410c;"><i class="far fa-calendar-alt"></i></div>
                </div>
                <p>Total Events</p>
                <h3><?php echo number_format($totalEvents ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f0fdf4; color: #246A55;"><i class="fas fa-check-double"></i></div>
                </div>
                <p>Total Bookings</p>
                <h3><?php echo number_format($totalBookings ?? 0); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #fef2f2; color: #b91c1c;"><i class="fas fa-exclamation-circle"></i></div>
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
                                            <div class="client-info">
                                                <span><?php echo htmlspecialchars($booking['client_name']); ?></span>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-main); font-weight:500;"><?php echo htmlspecialchars($booking['event_title']); ?></td>
                                        <td><span class="admin-badge"><?php echo htmlspecialchars($booking['organizer_name'] ?? 'System'); ?></span></td>
                                        <td>
                                            <span class="badge <?php echo htmlspecialchars($booking['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                         <td>
                                            <a href="/EventManagementSystem/public/admin/bookings/view?id=<?php echo $booking['id']; ?>" style="color: #4338ca; font-weight: 600; text-decoration: none; font-size: 13px;">
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
                    <div class="section-header" style="margin-bottom: 25px;">
                        <h3>Upcoming Events</h3>
                        <a href="/EventManagementSystem/public/admin/events" style="font-size: 12px; font-weight: 600; color: var(--primary-color);">Manage All</a>
                    </div>
                    <div class="events-list">
                        <?php if (empty($upcomingEvents)): ?>
                            <p style="text-align:center; color:var(--text-muted); font-size:14px;">No upcoming events.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <a href="/EventManagementSystem/public/admin/events/view?id=<?php echo $event['id']; ?>" class="event-item" style="text-decoration: none; color: inherit; display: flex;">
                                    <img src="<?php echo htmlspecialchars($event['image_path'] ?? '/EventManagementSystem/public/assets/images/default-event.jpg'); ?>" alt="Event Image" onerror="this.src='/EventManagementSystem/public/assets/images/default-event.jpg'">
                                    <div class="event-info">
                                        <h4><?php echo htmlspecialchars(strlen($event['title']) > 25 ? substr($event['title'],0,25).'...' : $event['title']); ?></h4>
                                        <div class="event-meta">
                                            <span class="category"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
                                            <span class="organizer" style="font-size: 10px; color: #888;">by <?php echo htmlspecialchars($event['organizer_name'] ?? 'System'); ?></span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
