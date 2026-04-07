<?php
// $notifications is passed from NotificationController@allNotifications

$typeCounts = [];
foreach ($notifications as $n) {
    $t = $n['type'] ?: 'info';
    $typeCounts[$t] = ($typeCounts[$t] ?? 0) + 1;
}
$totalCount = count($notifications);
$messageCount = ($typeCounts['message'] ?? 0);
$approvedCount = ($typeCounts['booking_approve'] ?? 0);
$cancelledCount = ($typeCounts['booking_cancel'] ?? 0);
$creationCount = ($typeCounts['event'] ?? 0);
$updateCount = ($typeCounts['event_update'] ?? 0);
$paymentCount = ($typeCounts['payment'] ?? 0);

$activeFilter = $_GET['type'] ?? 'all';

// Build header initials
$initials = '';
$nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
foreach ($nameParts as $p) {
    $initials .= strtoupper(substr(trim($p), 0, 1));
}
if (strlen($initials) > 2)
    $initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications — e.PLAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- FIX: Adding booking.css to correctly hide and size the profile dropdown -->
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">

    <style>
        body {
            background: #f4f7f6;
            display: block;
        }

        .notif-page-client {
            padding-top: 30px;
            padding-bottom: 80px;
        }
    </style>
</head>

<body>

    <!-- Client Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo">
            <img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;">
        </a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/bookings">My Bookings</a>
        </nav>
        <div class="nav-icons">
            <!-- Profile Avatar & Hidden Dropdown -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" id="header-avatar"
                                style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <span id="header-initials" style="font-size:14px;"><?php echo htmlspecialchars($initials); ?></span>
                        <?php endif; ?>
                    </div>
                    <div id="profile-dropdown" class="profile-dropdown">
                        <div class="pd-top">
                            <div class="pd-avatar-container">
                                <div class="pd-avatar">
                                    <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>"
                                            style="width:100%;height:100%;object-fit:cover;" id="dropdown-avatar">
                                    <?php else: ?>
                                        <span id="dropdown-initials"
                                            style="font-size:28px;color:white;"><?php echo htmlspecialchars($initials); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
                            <span class="pd-role">Client</span>
                        </div>
                        <div class="pd-bottom">
                            <?php
                            $firstName = $nameParts[0] ?? '';
                            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                            ?>
                            <div class="pd-detail"><label>FIRST NAME</label>
                                <div><?php echo htmlspecialchars($firstName); ?></div>
                            </div>
                            <div class="pd-detail"><label>LAST NAME</label>
                                <div><?php echo htmlspecialchars($lastName); ?></div>
                            </div>
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
                    document.addEventListener('click', function (e) {
                        const c = document.getElementById('profile-container');
                        if (c && !c.contains(e.target)) {
                            const d = document.getElementById('profile-dropdown');
                            if (d) d.classList.remove('show');
                        }
                    });

                    function confirmMarkAllUnread() {
                        fetch('/EventManagementSystem/public/notifications/mark-all-unread', { method: 'POST' })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    // Instant UI update
                                    document.querySelectorAll('.np-item').forEach(item => {
                                        item.classList.add('unread');
                                        if (!item.querySelector('.np-unread-dot')) {
                                            const dot = document.createElement('div');
                                            dot.className = 'np-unread-dot';
                                            item.prepend(dot);
                                        }
                                        if (!item.querySelector('.np-new-badge')) {
                                            const title = item.querySelector('.np-item-title');
                                            const badge = document.createElement('span');
                                            badge.className = 'np-new-badge';
                                            badge.textContent = 'New';
                                            title.appendChild(badge);
                                        }
                                        const toggle = item.querySelector('.np-unread-toggle');
                                        if (toggle) toggle.remove();
                                    });
                                }
                            });
                    }
                </script>
            <?php endif; ?>
        </div>
    </header>

    <!-- Page Content -->
    <div class="notif-page-client">

        <!-- HERO -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/client/events" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Events
                </a>
                <h1>My Notifications</h1>
                <p>All updates about your bookings, event changes, and important announcements in one place.</p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-regular fa-bell"></i>
                    <span><?php echo $totalCount; ?> Notification<?php echo $totalCount !== 1 ? 's' : ''; ?></span>
                </div>
                <?php if ($totalCount > 0): ?>
                    <button class="np-unread-all-btn" onclick="confirmMarkAllUnread()">
                        <i class="fa-solid fa-envelope-open"></i> Mark all as unread
                    </button>
                    <button class="np-clear-all-btn" onclick="confirmClearAll()">
                        <i class="fa-solid fa-trash-can"></i> Clear All
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- STATS -->
        <div class="np-stats-row">
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-bell"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Total</div>
                    <div class="np-stat-value" id="stat-total"><?php echo $totalCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-message"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Messages</div>
                    <div class="np-stat-value" id="stat-message"><?php echo $messageCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Confirmations</div>
                    <div class="np-stat-value" id="stat-approve"><?php echo $approvedCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Cancellations</div>
                    <div class="np-stat-value" id="stat-cancel"><?php echo $cancelledCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-calendar-plus"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Creation</div>
                    <div class="np-stat-value" id="stat-creation"><?php echo $creationCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-pen-to-square"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Updates</div>
                    <div class="np-stat-value" id="stat-update"><?php echo $updateCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-credit-card"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Payments</div>
                    <div class="np-stat-value" id="stat-payment"><?php echo $paymentCount; ?></div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="np-filter-bar">
            <a href="/EventManagementSystem/public/notifications/all"
                class="np-filter-tab <?php echo ($activeFilter === 'all') ? 'active' : ''; ?>">
                <i class="fa-solid fa-border-all"></i> All
                <span class="np-filter-count" id="count-all"><?php echo $totalCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=message"
                class="np-filter-tab <?php echo ($activeFilter === 'message') ? 'active' : ''; ?>">
                <i class="fa-solid fa-message"></i> Messages
                <span class="np-filter-count" id="count-message"><?php echo $messageCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=booking_approve"
                class="np-filter-tab <?php echo ($activeFilter === 'booking_approve') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-check"></i> Confirmed
                <span class="np-filter-count" id="count-approve"><?php echo $approvedCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=booking_cancel"
                class="np-filter-tab <?php echo ($activeFilter === 'booking_cancel') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-xmark"></i> Cancelled
                <span class="np-filter-count" id="count-cancel"><?php echo $cancelledCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=event"
                class="np-filter-tab <?php echo ($activeFilter === 'event') ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-plus"></i> Event Creation
                <span class="np-filter-count" id="count-creation"><?php echo $creationCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=event_update"
                class="np-filter-tab <?php echo ($activeFilter === 'event_update') ? 'active' : ''; ?>">
                <i class="fa-solid fa-pen-to-square"></i> Event Updates
                <span class="np-filter-count" id="count-update"><?php echo $updateCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=payment"
                class="np-filter-tab <?php echo ($activeFilter === 'payment') ? 'active' : ''; ?>">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="np-filter-count" id="count-payment"><?php echo $paymentCount; ?></span>
            </a>
        </div>

        <!-- LIST -->
        <?php if (empty($notifications)): ?>
            <div class="np-empty-state">
                <div class="np-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <h3>You're All Caught Up!</h3>
                <p>No notifications found. When there's an update, it will appear here.</p>
            </div>
        <?php else: ?>
            <div class="np-list" id="np-list">
                <?php
                $currentGroup = null;
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));

                foreach ($notifications as $n):
                    $isUnread = ($n['is_read'] == 0);
                    $nDate = date('Y-m-d', strtotime($n['created_at']));

                    if ($nDate === $today) {
                        $groupLabel = 'Today';
                    } elseif ($nDate === $yesterday) {
                        $groupLabel = 'Yesterday';
                    } else {
                        $groupLabel = date('F j, Y', strtotime($n['created_at']));
                    }

                    if ($currentGroup !== $groupLabel):
                        $currentGroup = $groupLabel;
                        ?>
                        <div class="np-date-group"><?php echo $groupLabel; ?></div>
                    <?php endif; ?>

                    <div class="np-item <?php echo $isUnread ? 'unread' : ''; ?>" id="np-item-<?php echo $n['id']; ?>"
                        data-id="<?php echo $n['id']; ?>" data-action="read">
                        <?php if ($isUnread): ?>
                            <div class="np-unread-dot"></div>
                        <?php endif; ?>

                        <?php
                        // Determine visual styling based on type
                        $typeClass = $n['type'] ?: 'default';
                        $iconClass = 'fa-bell';

                        $icons = [
                            'booking' => 'fa-bookmark',
                            'booking_approve' => 'fa-circle-check',
                            'booking_cancel' => 'fa-circle-xmark',
                            'event' => 'fa-calendar-day',
                            'event_update' => 'fa-pen-to-square',
                            'payment' => 'fa-credit-card',
                            'payment_alert' => 'fa-credit-card',
                            'message' => 'fa-message',
                            'system' => 'fa-gear',
                            'info' => 'fa-circle-info'
                        ];
                        if (isset($icons[$typeClass])) {
                            $iconClass = $icons[$typeClass];
                        }

                        $typeLabels = [
                            'booking' => 'Booking Request',
                            'booking_approve' => 'Confirmed',
                            'booking_cancel' => 'Cancelled',
                            'event' => 'Event',
                            'event_update' => 'Event Update',
                            'message' => 'Message',
                            'system' => 'System',
                            'info' => 'Info'
                        ];
                        $labelInfo = $typeLabels[$typeClass] ?? 'Notification';
                        ?>

                        <div class="np-icon-bubble <?php echo htmlspecialchars($typeClass); ?>">
                            <i class="fa-solid <?php echo $iconClass; ?>"></i>
                        </div>

                        <div class="np-item-body">
                            <div class="np-item-title">
                                <?php echo htmlspecialchars($n['title']); ?>
                                <?php if ($isUnread): ?>
                                    <span class="np-new-badge">New</span>
                                <?php endif; ?>
                            </div>
                            <div class="np-item-msg">
                                <?php echo htmlspecialchars($n['message']); ?>

                                <?php if (!empty($n['booking_id'])): ?>
                                    <br>
                                    <a href="/EventManagementSystem/public/client/bookings/view?id=<?php echo htmlspecialchars($n['booking_id']); ?>"
                                        style="color:var(--brand); font-weight:700; font-size:12px; margin-top:6px; display:inline-block;">
                                        View Booking <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="np-item-footer">
                                <div class="np-time-tag">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo date('g:i A', strtotime($n['created_at'])); ?>
                                </div>
                                <div class="np-type-pill <?php echo htmlspecialchars($typeClass); ?>">
                                    <?php echo $labelInfo; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!$isUnread): ?>
                            <button class="np-unread-toggle" data-id="<?php echo $n['id']; ?>" data-action="unread"
                                title="Mark as unread">
                                <i class="fa-solid fa-envelope-open"></i>
                            </button>
                        <?php endif; ?>

                        <button class="np-delete-btn" data-id="<?php echo $n['id']; ?>" data-action="delete" title="Remove">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- External JS for Dropdown & Deletion Logic -->
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        window.deleteNotification = function(id) {
            if (!confirm("Remove this notification?")) return;

            fetch('/EventManagementSystem/public/notifications/delete?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById('np-item-' + id);
                        if (el) {
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(10px)';
                            setTimeout(() => {
                                el.remove();
                                // Optional: Check if we need to show empty state
                                const list = document.querySelector('.np-list');
                                if (list && list.children.length === 0) {
                                    window.location.reload();
                                }
                            }, 300);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function confirmClearAll() {
            if (!confirm("Are you sure you want to clear ALL your notifications? This cannot be undone.")) return;
            fetch('/EventManagementSystem/public/notifications/clear-all', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                });
        }

        // The markAllUnread functionality is now handled by the function at the top of this view.
    </script>
</body>

</html>
