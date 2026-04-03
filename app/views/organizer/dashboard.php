<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Using FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
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
                <input type="text" placeholder="Search for events, clients...">
            </div>
            <div class="header-icons">
                <i class="far fa-bell"></i>

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_fullname'] ?? 'User'); ?>&background=0D8ABC&color=fff" alt="Profile" style="border-radius:50%; object-fit:cover;">
            </div>
        </header>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div>
                <h2>Welcome back, <?php $firstName = explode(' ', trim($_SESSION['user_fullname']))[0]; echo htmlspecialchars($firstName); ?>! 👋</h2>
                <p>You have <?php echo number_format($pendingRequests ?? 0); ?> pending requests that need your attention today.</p>
            </div>
            <a href="/EventManagementSystem/public/organizer/events/create" class="btn-primary">
                <i class="fas fa-plus"></i> Create New Event
            </a>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="far fa-calendar"></i></div>
                    <span class="stat-trend up">+12% <i class="fas fa-arrow-trend-up"></i></span>
                </div>
                <p>Total Events</p>
                <h3><?php echo number_format($totalEvents); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-mobile-alt"></i></div>
                    <span class="stat-trend up">+5% <i class="fas fa-arrow-trend-up"></i></span>
                </div>
                <p>Total Bookings</p>
                <h3><?php echo number_format($totalBookings); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="far fa-clipboard"></i></div>
                    <span class="stat-trend high">High</span>
                </div>
                <p>Pending Requests</p>
                <h3><?php echo number_format($pendingRequests); ?></h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="far fa-money-bill-alt"></i></div>
                    <span class="stat-trend up">+24% <i class="fas fa-arrow-trend-up"></i></span>
                </div>
                <p>Revenue</p>
                <h3>Rs. <?php echo number_format($revenue); ?></h3>
            </div>
        </div>

        <!-- Bottom Grid Section -->
        <div class="bottom-grid">
            
            <!-- Left Column -->
            <div class="left-col">
                <!-- Recent Bookings Table -->
                <div class="recent-bookings" style="margin-bottom: 25px;">
                    <div class="section-header">
                        <h3>Recent Bookings</h3>
                        <a href="#">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Event</th>
                                <th>Package</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentBookings)): ?>
                                <tr><td colspan="6" style="text-align:center;">No recent bookings found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="client-info">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['client_name']); ?>&background=random" alt="<?php echo htmlspecialchars($booking['client_name']); ?>">
                                                <span><?php echo htmlspecialchars($booking['client_name']); ?></span>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-main); font-weight:500;"><?php echo htmlspecialchars($booking['event_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                        <td>
                                            <?php 
                                            // Format date
                                            $date = new DateTime($booking['created_at']);
                                            echo $date->format('M d, Y'); 
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo htmlspecialchars($booking['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td><button class="action-btn"><i class="fas fa-ellipsis-v"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Status Summary -->
                <div class="status-summary">
                    <?php 
                        $totalConfirmedAndPendingAndCancelled = array_sum($statusSummary);
                        $confirmedPct = $totalConfirmedAndPendingAndCancelled ? ($statusSummary['confirmed'] / $totalConfirmedAndPendingAndCancelled) * 100 : 0;
                        $pendingPct = $totalConfirmedAndPendingAndCancelled ? ($statusSummary['pending'] / $totalConfirmedAndPendingAndCancelled) * 100 : 0;
                        $cancelledPct = $totalConfirmedAndPendingAndCancelled ? ($statusSummary['cancelled'] / $totalConfirmedAndPendingAndCancelled) * 100 : 0;
                    ?>
                    
                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Confirmed</h4>
                            <span class="confirmed"><?php echo $statusSummary['confirmed']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill confirmed" style="width: <?php echo $confirmedPct; ?>%;"></div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Awaiting Approval</h4>
                            <span class="pending"><?php echo $statusSummary['pending']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill pending" style="width: <?php echo $pendingPct; ?>%;"></div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Cancelled</h4>
                            <span class="cancelled"><?php echo $statusSummary['cancelled']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill cancelled" style="width: <?php echo $cancelledPct; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="upcoming-events">
                    <div class="section-header" style="margin-bottom: 25px;">
                        <h3>Upcoming Events</h3>
                        <span style="background:#e6fcf0; color:#246A55; padding:4px 10px; border-radius:12px; font-weight:600; font-size:12px;">
                            <?php echo count($upcomingEvents); ?> Active
                        </span>
                    </div>
                    <div class="events-list">
                        <?php if (empty($upcomingEvents)): ?>
                            <p style="text-align:center; color:var(--text-muted); font-size:14px;">No upcoming events.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <?php
                                    $eventDate = new DateTime($event['event_date']);
                                    $now = new DateTime();
                                    $diff = $now->diff($eventDate);
                                    $daysLeft = $diff->days;
                                    $daysText = $diff->invert ? 'Past' : "in {$daysLeft} days";
                                ?>
                                <div class="event-item">
                                    <img src="<?php echo htmlspecialchars($event['image_path'] ?? '/EventManagementSystem/public/assets/images/default-event.jpg'); ?>" alt="Event Image" onerror="this.src='/EventManagementSystem/public/assets/images/default-event.jpg'">
                                    <div class="event-info">
                                        <h4><?php echo htmlspecialchars(strlen($event['title']) > 20 ? substr($event['title'],0,20).'...' : $event['title']); ?></h4>
                                        <div class="event-meta">
                                            <span class="category"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
                                            <span class="date"><?php echo $daysText; ?></span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
