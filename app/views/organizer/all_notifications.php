<?php
// $notifications and $role are passed from NotificationController@allNotifications

$typeCounts = [];
foreach ($notifications as $n) {
    $t = $n['type'] ?: 'info';
    $typeCounts[$t] = ($typeCounts[$t] ?? 0) + 1;
}
$totalCount = count($notifications);
$bookingCount = ($typeCounts['booking'] ?? 0) + ($typeCounts['booking_cancel'] ?? 0);
$eventCount = ($typeCounts['event'] ?? 0) + ($typeCounts['event_update'] ?? 0);
$messageCount = ($typeCounts['message'] ?? 0);
$approvedCount = ($typeCounts['booking_approve'] ?? 0);
$cancelledCount = ($typeCounts['booking_cancel'] ?? 0);
$paymentCount = ($typeCounts['payment_alert'] ?? 0);

$activeFilter = $_GET['type'] ?? 'all';

// Organizer sidebar requires $activePage
$activePage = 'notifications';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications — Organizer Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main-content">
        <!-- Minimal Header -->
        <header class="header">
            <form action="/EventManagementSystem/public/organizer/events" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search your events...">
                <button type="submit" style="display:none;"></button>
            </form>
            <div class="header-icons">
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <!-- HERO -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/organizer/dashboard" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1>My Notifications</h1>
                <p>Track all booking requests, client actions, and event updates relevant to your events.</p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-calendar-check"></i>
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
                <div class="np-stat-icon green"><i class="fa-solid fa-bookmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Booking Requests</div>
                    <div class="np-stat-value" id="stat-booking"><?php echo ($typeCounts['booking'] ?? 0); ?></div>
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
                <div class="np-stat-icon green"><i class="fa-regular fa-calendar-alt"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Updates</div>
                    <div class="np-stat-value" id="stat-update"><?php echo $eventCount; ?></div>
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
            <a href="/EventManagementSystem/public/notifications/all?type=booking"
                class="np-filter-tab <?php echo ($activeFilter === 'booking') ? 'active' : ''; ?>">
                <i class="fa-solid fa-bookmark"></i> Booking Requests
                <span class="np-filter-count" id="count-booking"><?php echo ($typeCounts['booking'] ?? 0); ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=booking_cancel"
                class="np-filter-tab <?php echo ($activeFilter === 'booking_cancel') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-xmark"></i> Cancellations
                <span class="np-filter-count" id="count-cancel"><?php echo $cancelledCount; ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=event_update"
                class="np-filter-tab <?php echo ($activeFilter === 'event_update') ? 'active' : ''; ?>">
                <i class="fa-solid fa-pen-to-square"></i> Event Updates
                <span class="np-filter-count" id="count-update"><?php echo ($typeCounts['event_update'] ?? 0); ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=message"
                class="np-filter-tab <?php echo ($activeFilter === 'message') ? 'active' : ''; ?>">
                <i class="fa-solid fa-message"></i> Messages
                <span class="np-filter-count" id="count-message"><?php echo ($typeCounts['message'] ?? 0); ?></span>
            </a>
            <a href="/EventManagementSystem/public/notifications/all?type=payment_alert"
                class="np-filter-tab <?php echo ($activeFilter === 'payment_alert') ? 'active' : ''; ?>">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="np-filter-count" id="count-payment"><?php echo $paymentCount; ?></span>
            </a>
        </div>

        <!-- LIST -->
        <?php if (empty($notifications)): ?>
            <div class="np-empty-state">
                <div class="np-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <h3>No Notifications Yet</h3>
                <p>When clients book your events or make changes, you'll see alerts here.</p>
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
                                'system' => 'fa-solid fa-gear',
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
                                <?php if (!empty($n['related_id']) && $type === 'booking'): ?>
                                    <a href="/EventManagementSystem/public/organizer/bookings/view?id=<?php echo $n['related_id']; ?>"
                                        style="font-size:12px; color: var(--brand); font-weight:600; display:flex; align-items:center; gap:4px;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> View Booking
                                    </a>
                                <?php endif; ?>
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
                        if (data.success) location.reload();
                    });
            }
        }

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
</body>

</html>