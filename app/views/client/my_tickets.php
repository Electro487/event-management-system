<?php 
// $tickets variable is passed from the controller
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-tickets.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>
    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN" style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/bookings">My Bookings</a>
            <a href="/EventManagementSystem/public/client/tickets" class="active">My Tickets</a>
        </nav>
        <div class="nav-icons">
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
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    $initials = '';
                    $nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
                    foreach($nameParts as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                    }
                    if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if(!empty($_SESSION['user_profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" id="header-avatar">
                        <?php else: ?>
                            <span id="header-initials"><?php echo htmlspecialchars($initials); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div id="profile-dropdown" class="profile-dropdown">
                        <div class="pd-top">
                            <div class="pd-avatar-container">
                                <div class="pd-avatar">
                                    <?php if(!empty($_SESSION['user_profile_pic'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span><?php echo htmlspecialchars($initials); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
                            <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            <span class="pd-role"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Client')); ?></span>
                        </div>
                        <div class="pd-bottom">
                            <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
                <script>
                    function toggleProfileDropdown() {
                        document.getElementById('profile-dropdown').classList.toggle('show');
                    }
                    document.addEventListener('click', function(e) {
                        if (!document.getElementById('profile-container').contains(e.target)) {
                            document.getElementById('profile-dropdown').classList.remove('show');
                        }
                    });
                </script>
            <?php endif; ?>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="page-header-row clearfix" style="margin-bottom: 30px;">
            <div class="headings">
                <h1 class="page-header-title">MY TICKETS</h1>
                <p class="page-header-desc">Access your concert entry passes, track payment statuses, and print tickets for seamless venue entry.</p>
            </div>
        </div>

        <div class="ticket-list">
            <?php if (empty($tickets)): ?>
                <div class="empty-state" style="grid-column: span 3; text-align: center; padding: 80px 20px; background: white; border-radius: 20px; border: 2px dashed #cbd5e1;">
                    <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-ticket" style="font-size: 32px; color: #94a3b8;"></i>
                    </div>
                    <h3 style="font-size: 20px; color: #1e293b; margin-bottom: 10px;">No Tickets Found</h3>
                    <p style="color: #64748b; margin-bottom: 25px;">You haven't reserved any concert tickets yet. Explore upcoming concerts to get started.</p>
                    <a href="/EventManagementSystem/public/client/events" class="btn-browse-more" style="float:none;">Browse Concerts</a>
                </div>
            <?php else: ?>
                <?php foreach ($tickets as $t): 
                    $eSnap = !empty($t['event_snapshot']) ? json_decode($t['event_snapshot'], true) : null;
                    $title = $eSnap['title'] ?? $t['event_title'];
                    $rawImg = $eSnap['image_path'] ?? $t['event_image'] ?? '';
                    $imgUrl = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                    if ($rawImg) {
                        $imgUrl = ($rawImg[0] === '/') ? $rawImg : '/EventManagementSystem/public/assets/images/events/' . $rawImg;
                    }
                    $status = strtolower($t['status']);
                    $payStatus = strtolower($t['payment_status'] ?? 'unpaid');
                ?>
                    <div class="ticket-card">
                        <div class="ticket-banner">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="Event" class="ticket-img">
                            <div class="ticket-overlay"></div>
                            <span class="ticket-id">#EPLN-<?php echo str_pad($t['id'], 5, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        
                        <div class="ticket-info">
                            <div class="ticket-title-row">
                                <h3 class="ticket-title"><?php echo htmlspecialchars($title); ?></h3>
                                <span class="ticket-status status-<?php echo $status; ?>"><?php echo $status; ?></span>
                            </div>

                            <div class="ticket-meta-grid">
                                <div class="meta-item">
                                    <span class="meta-label">Date & Time</span>
                                    <span class="meta-value"><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($t['event_date'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Quantity</span>
                                    <span class="meta-value"><i class="fa-solid fa-user-group"></i> <?php echo $t['guest_count']; ?> Person(s)</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Tier</span>
                                    <span class="meta-value"><i class="fa-solid fa-tag"></i> <?php echo ucfirst($t['package_tier']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Venue</span>
                                    <span class="meta-value" style="font-size: 12px;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($t['venue_name'] ?: 'Venue TBD'); ?></span>
                                </div>
                            </div>

                            <div class="ticket-pricing">
                                <div class="price-box">
                                    <span class="price-label">TOTAL AMOUNT</span>
                                    <span class="price-value">Rs. <?php echo number_format($t['total_amount'], 2); ?></span>
                                </div>
                                <span class="payment-badge pay-<?php echo $payStatus; ?>">
                                    <?php echo strtoupper(str_replace('_', ' ', $payStatus)); ?>
                                </span>
                            </div>

                            <div class="ticket-actions">
                                <?php if ($payStatus === 'unpaid'): ?>
                                    <a href="/EventManagementSystem/public/client/payment/checkout?booking_id=<?php echo $t['id']; ?>" class="btn-primary-ticket">
                                        Complete Payment
                                    </a>
                                <?php elseif ($status === 'confirmed' || $status === 'completed'): ?>
                                    <a href="/EventManagementSystem/public/client/ticket?id=<?php echo $t['id']; ?>" class="btn-print-ticket" target="_blank">
                                        <i class="fa-solid fa-print"></i> Print QR Ticket
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/EventManagementSystem/public/client/bookings/view?id=<?php echo $t['id']; ?>" class="btn-secondary-ticket-full">
                                    View Ticket Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
