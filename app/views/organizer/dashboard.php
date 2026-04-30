<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php
    $activePage = 'dashboard';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <span class="current">Dashboard</span>
                </div>
            </div>

            <div class="header-right">
                <div class="header-actions">
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
                </div>

                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div>
                <h2>Welcome back, <?php 
                    $fullName = trim($_SESSION['user_fullname'] ?? '');
                    if (!$fullName) $fullName = 'Organizer';
                    $nameParts = explode(' ', $fullName);
                    $firstName = $nameParts[0];
                    echo htmlspecialchars($firstName); 
                ?>! 👋</h2>
                <p>You have <span id="pending-requests-count">0</span> pending requests that need your attention today.</p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="far fa-calendar"></i></div>
                </div>
                <p>Total Events</p>
                <h3 id="stat-total-events">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-mobile-alt"></i></div>
                </div>
                <p>Total Bookings</p>
                <h3 id="stat-total-bookings">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="far fa-clipboard"></i></div>
                </div>
                <p>Pending Requests</p>
                <h3 id="stat-pending-requests">0</h3>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <p>Confirmed Bookings</p>
                <h3 id="stat-confirmed-bookings">0</h3>
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
                        <a href="/EventManagementSystem/public/organizer/bookings">View All</a>
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
                        <tbody id="recent-bookings-tbody">
                            <tr>
                                <td colspan="6" style="text-align:center; color: var(--text-muted);">Loading recent bookings...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Status Summary -->
                <div class="status-summary">
                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Confirmed</h4>
                            <span class="confirmed" id="stat-sum-confirmed">0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill confirmed" id="stat-bar-confirmed" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Pending</h4>
                            <span class="pending" id="stat-sum-pending">0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill pending" id="stat-bar-pending" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-card-header">
                            <h4>Cancelled</h4>
                            <span class="cancelled" id="stat-sum-cancelled">0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="fill cancelled" id="stat-bar-cancelled" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <!-- Upcoming Events (mini-list) -->
                <div class="upcoming-events-mini">
                    <div class="section-header">
                        <h3>Upcoming Events</h3>
                        <span class="count-badge" id="active-events-count">0 Active</span>
                    </div>
                    <div class="events-list" id="upcoming-events-list">
                        <p style="text-align:center; color:var(--text-muted); font-size:14px; padding:20px;">Loading upcoming events...</p>
                    </div>
                    <a href="/EventManagementSystem/public/organizer/events" class="view-all-events">View All Events <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Create Event CTA -->
                <div class="create-event-cta">
                    <i class="fas fa-plus-circle"></i>
                    <h4>New Event?</h4>
                    <p>Start curating your next premium event experience.</p>
                    <a href="/EventManagementSystem/public/organizer/events/create" class="btn-create">Create Event</a>
                </div>
            </div>

        </div>

    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboard();

            function updateDashboard() {
                if (!window.emsApi) return;

                window.emsApi.apiFetch('/api/v1/dashboard/organizer')
                    .then(res => {
                        const data = res.data;
                        if (!data) return;

                        // Update Stats
                        document.getElementById('stat-total-events').textContent = data.total_events || 0;
                        document.getElementById('stat-total-bookings').textContent = data.total_bookings || 0;
                        document.getElementById('stat-pending-requests').textContent = data.pending_requests || 0;
                        document.getElementById('pending-requests-count').textContent = data.pending_requests || 0;
                        document.getElementById('stat-confirmed-bookings').textContent = (data.status_summary && data.status_summary.confirmed) || 0;

                        // Update Status Summary Bars
                        const sum = data.status_summary || { confirmed: 0, pending: 0, cancelled: 0 };
                        const total = (sum.confirmed || 0) + (sum.pending || 0) + (sum.cancelled || 0);
                        
                        document.getElementById('stat-sum-confirmed').textContent = sum.confirmed || 0;
                        document.getElementById('stat-sum-pending').textContent = sum.pending || 0;
                        document.getElementById('stat-sum-cancelled').textContent = sum.cancelled || 0;

                        if (total > 0) {
                            document.getElementById('stat-bar-confirmed').style.width = ((sum.confirmed || 0) / total * 100) + '%';
                            document.getElementById('stat-bar-pending').style.width = ((sum.pending || 0) / total * 100) + '%';
                            document.getElementById('stat-bar-cancelled').style.width = ((sum.cancelled || 0) / total * 100) + '%';
                        }

                        // Update Recent Bookings Table
                        const tbody = document.getElementById('recent-bookings-tbody');
                        const recent = data.recent_bookings || [];
                        if (recent.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: var(--text-muted);">No recent bookings.</td></tr>`;
                        } else {
                            tbody.innerHTML = recent.map(b => {
                                const statusDisp = b.display_status || b.status || 'pending';
                                const dateStr = b.event_date ? new Date(b.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD';
                                
                                const initials = (b.client_name || 'U').substring(0, 2).toUpperCase();
                                const avatar = b.client_profile_pic 
                                    ? `<img src="${b.client_profile_pic}" alt="" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">`
                                    : `<div class="avatar-circle" style="width: 28px; height: 28px; border-radius: 50%; background: #f0fdf4; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; border: 1px solid #dcfce7;">${initials}</div>`;

                                return `<tr>
                                    <td>
                                        <div class="client-info" style="display: flex; align-items: center; gap: 12px;">
                                            ${avatar}
                                            <span style="font-weight: 500; font-size: 13.5px; color: var(--text-main);">${b.client_name || ''}</span>
                                        </div>
                                    </td>
                                    <td style="font-weight:500;">
                                        <a href="/EventManagementSystem/public/organizer/events/view?id=${b.event_id}" style="color:var(--text-main); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#246A55'" onmouseout="this.style.color='var(--text-main)'">
                                            ${b.event_name || ''}
                                        </a>
                                    </td>
                                    <td>${b.package_name || ''}</td>
                                    <td>${dateStr}</td>
                                    <td>
                                        <span class="badge ${statusDisp}">${statusDisp.charAt(0).toUpperCase() + statusDisp.slice(1)}</span>
                                    </td>
                                    <td><a href="/EventManagementSystem/public/organizer/bookings/view?id=${b.id}" style="color: #246A55; font-weight: 600; text-decoration: none; font-size: 13px;">View</a></td>
                                </tr>`;
                            }).join('');
                        }

                        // Update Upcoming Events List
                        const eventsList = document.getElementById('upcoming-events-list');
                        const events = data.upcoming_events || [];
                        document.getElementById('active-events-count').textContent = events.length + ' Active';
                        
                        if (events.length === 0) {
                            eventsList.innerHTML = `<p style="text-align:center; color:var(--text-muted); font-size:14px; padding:20px;">No upcoming events.</p>`;
                        } else {
                            const now = new Date();
                            now.setHours(0,0,0,0);
                            
                            // Only show top 2 events
                            const topEvents = events.slice(0, 2);
                            
                            eventsList.innerHTML = topEvents.map(e => {
                                let daysText = "Ongoing";
                                if (e.event_date) {
                                    const eDate = new Date(e.event_date);
                                    eDate.setHours(0,0,0,0);
                                    const diffTime = eDate - now;
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                    
                                    if (diffDays === 0) daysText = "Today";
                                    else if (diffDays < 0) daysText = "Ongoing";
                                    else daysText = "in " + diffDays + " days";
                                }
                                
                                const img = e.image_path || '/EventManagementSystem/public/assets/images/placeholder.jpg';
                                
                                return `<a href="/EventManagementSystem/public/organizer/events/view?id=${e.id}" class="event-item" data-title="${e.title?.toLowerCase() || ''}" style="display: flex; align-items: center; gap: 15px; text-decoration: none; color: inherit; padding: 10px; border-radius: 10px; transition: all 0.2s ease;">
                                    <img src="${img}" alt="Event Image" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg'" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                                    <div class="event-info" style="flex: 1; min-width: 0;">
                                        <h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${e.title || ''}</h4>
                                        <div class="event-meta" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <span class="category" style="white-space: nowrap; background: #f4f7f6; padding: 3px 8px; border-radius: 4px; font-size: 11px; color: var(--text-muted);">${e.category || 'Event'}</span>
                                            <span class="date" style="white-space: nowrap; color: #e74c3c; font-size: 12px; font-weight: 600; margin-left: auto;">${daysText}</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right" style="color: var(--text-muted); font-size: 14px; flex-shrink: 0;"></i>
                                </a>`;
                            }).join('');
                        }
                    })
                    .catch(err => console.error('Dashboard API failed:', err));
            }
        });
    </script>
</body>

</html>
