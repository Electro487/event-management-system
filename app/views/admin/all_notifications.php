<?php
// $notifications and $role are passed from NotificationController@allNotifications

// Build type-counts for the filter bar
$typeCounts = [];
foreach ($notifications as $n) {
    $t = $n['type'] ?: 'info';
    $typeCounts[$t] = ($typeCounts[$t] ?? 0) + 1;
}
$totalCount = count($notifications);

// Build stat buckets for admin:
// booking = new booking received
// event   = new event campaign created
// event_update / event_delete = event managed by admin/org
$bookingCount = ($typeCounts['booking'] ?? 0);
$creationCount = ($typeCounts['event'] ?? 0);
$updateCount = ($typeCounts['event_update'] ?? 0) + ($typeCounts['event_delete'] ?? 0);
$messageCount = ($typeCounts['message'] ?? 0);
$actionCount = ($typeCounts['booking_approve'] ?? 0) + ($typeCounts['booking_cancel'] ?? 0);
$paymentCount = ($typeCounts['payment_alert'] ?? 0);
$feedbackCount = ($typeCounts['feedback'] ?? 0) + ($typeCounts['feedback_reply'] ?? 0);

// Active type filter from URL
$activeFilter = $_GET['type'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications — Admin Panel</title>
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
            <div style="flex: 1;"></div>
            <div class="header-icons">
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
                <p>Stay on top of all system activity — new bookings, event campaigns, and administrative actions.</p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span><?php echo $totalCount; ?> Total Alerts</span>
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
                <div class="np-stat-icon green"><i class="fa-solid fa-bookmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Booking Requests</div>
                    <div class="np-stat-value" id="stat-booking"><?php echo $bookingCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-regular fa-calendar-alt"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Creation</div>
                    <div class="np-stat-value" id="stat-creation"><?php echo $creationCount; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Cancellations</div>
                    <div class="np-stat-value" id="stat-cancel"><?php echo ($typeCounts['booking_cancel'] ?? 0); ?>
                    </div>
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
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-comments"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Feedback</div>
                    <div class="np-stat-value" id="stat-feedback"><?php echo $feedbackCount; ?></div>
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
            <a href="/EventManagementSystem/public/notifications/all?type=booking"
                class="np-filter-tab <?php echo ($activeFilter === 'booking') ? 'active' : ''; ?>">
                <i class="fa-solid fa-bookmark"></i> Booking Requests
                <span class="np-filter-count" id="count-booking"><?php echo $bookingCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=event"
                class="np-filter-tab <?php echo ($activeFilter === 'event') ? 'active' : ''; ?>">
                <i class="fa-regular fa-calendar-alt"></i> Event Creation
                <span class="np-filter-count" id="count-creation"><?php echo $creationCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=event_updates"
                class="np-filter-tab <?php echo ($activeFilter === 'event_updates') ? 'active' : ''; ?>">
                <i class="fa-solid fa-bolt"></i> Event Updates
                <span class="np-filter-count" id="count-update"><?php echo $updateCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=message"
                class="np-filter-tab <?php echo ($activeFilter === 'message') ? 'active' : ''; ?>">
                <i class="fa-solid fa-message"></i> Messages
                <span class="np-filter-count" id="count-message"><?php echo $messageCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=booking_cancel"
                class="np-filter-tab <?php echo ($activeFilter === 'booking_cancel') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-xmark"></i> Cancellations
                <span class="np-filter-count"
                    id="count-cancel"><?php echo ($typeCounts['booking_cancel'] ?? 0); ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=payment_alert"
                class="np-filter-tab <?php echo ($activeFilter === 'payment_alert') ? 'active' : ''; ?>">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="np-filter-count" id="count-payment"><?php echo $paymentCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=feedback"
                class="np-filter-tab <?php echo ($activeFilter === 'feedback') ? 'active' : ''; ?>">
                <i class="fa-solid fa-comments"></i> Feedback
                <span class="np-filter-count" id="count-feedback"><?php echo $feedbackCount; ?></span>
            </a>
        </div>

        <!-- NOTIFICATIONS LIST -->
        <?php if (empty($notifications)): ?>
            <div class="np-empty-state">
                <div class="np-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <h3>No Notifications</h3>
                <p>You're all clear! Notifications will appear here when system activity happens.</p>
            </div>
        <?php else: ?>
            <div class="np-list" id="np-list">
                <?php
                $prevDate = null;
                foreach ($notifications as $n):
                    $type = $n['type'] ?: 'info';
                    $isUnread = !$n['is_read'];
                    $createdAt = new DateTime($n['created_at']);
                    $today = new DateTime('today');
                    $yesterday = new DateTime('yesterday');

                    if ($createdAt >= $today) {
                        $groupLabel = 'Today';
                    } elseif ($createdAt >= $yesterday) {
                        $groupLabel = 'Yesterday';
                    } else {
                        $groupLabel = $createdAt->format('F j, Y');
                    }

                    if ($groupLabel !== $prevDate):
                        $prevDate = $groupLabel;
                        ?>
                        <div class="np-date-group"><?php echo $groupLabel; ?></div>
                    <?php endif; ?>

                    <div class="np-item <?php echo $isUnread ? 'unread' : ''; ?>" id="np-item-<?php echo $n['id']; ?>"
                        data-id="<?php echo $n['id']; ?>" data-action="read">
                        <?php if ($isUnread): ?>
                            <div class="np-unread-dot"></div>
                        <?php endif; ?>

                        <div class="np-icon-bubble <?php echo htmlspecialchars($type); ?>">
                            <?php
                            $icons = [
                                'booking' => 'fa-solid fa-bookmark',
                                'booking_approve' => 'fa-solid fa-circle-check',
                                'booking_cancel' => 'fa-solid fa-circle-xmark',
                                'event' => 'fa-regular fa-calendar-plus',
                                'event_update' => 'fa-solid fa-pen-to-square',
                                'event_delete' => 'fa-solid fa-trash-can',
                                'message' => 'fa-solid fa-message',
                                'payment' => 'fa-solid fa-credit-card',
                                'payment_alert' => 'fa-solid fa-credit-card',
                                'feedback' => 'fa-solid fa-comments',
                                'feedback_reply' => 'fa-solid fa-comments',
                                'system' => 'fa-solid fa-shield-halved',
                                'info' => 'fa-solid fa-circle-info',
                            ];
                            $icon = $icons[$type] ?? 'fa-regular fa-bell';
                            ?>
                            <i class="<?php echo $icon; ?>"></i>
                        </div>

                        <div class="np-item-body">
                            <div class="np-item-title">
                                <?php echo htmlspecialchars($n['title']); ?>
                                <?php if ($isUnread): ?><span class="np-new-badge">New</span><?php endif; ?>
                            </div>
                            <div class="np-item-msg"><?php echo nl2br(htmlspecialchars($n['message'])); ?></div>
                            <div class="np-item-footer">
                                <span class="np-time-tag">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo $createdAt->format('M j, Y · g:i A'); ?>
                                </span>
                                <span class="np-type-pill <?php echo htmlspecialchars($type); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!$isUnread): ?>
                            <button class="np-unread-toggle" data-id="<?php echo $n['id']; ?>" data-action="unread"
                                title="Mark as unread">
                                <i class="fa-solid fa-envelope-open"></i>
                            </button>
                        <?php endif; ?>

                        <button class="np-delete-btn" data-id="<?php echo $n['id']; ?>" data-action="delete" title="Dismiss">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        window.deleteNotification = function(id) {
            if (!confirm("Dismiss this notification?")) return;

            fetch('/EventManagementSystem/public/notifications/delete?id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById('np-item-' + id);
                        if (el) {
                            el.style.opacity = '0';
                            el.style.transform = 'translateX(30px)';
                            el.style.transition = 'all .3s ease';
                            setTimeout(() => el.remove(), 300);
                        }
                    }
                });
        }

        function confirmClearAll() {
            if (confirm('Clear all notifications? This action cannot be undone.')) {
                fetch('/EventManagementSystem/public/notifications/clear-all', { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }

        function confirmMarkAllUnread() {
            fetch('/EventManagementSystem/public/notifications/mark-all-unread', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Instant UI update: mark everything as unread locally
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
                            // Also remove the "mark as unread" toggle button if it exists
                            const toggle = item.querySelector('.np-unread-toggle');
                            if (toggle) toggle.remove();
                        });
                    }
                });
        }
    </script>
</body>

</html>
