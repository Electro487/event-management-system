<?php
// $notifications is passed from NotificationController@allNotifications

$typeCounts = [];
foreach ($notifications as $n) {
    $t = $n['type'] ?: 'info';
    if ($t === 'feedback_reply') {
        $t = 'feedback';
    }
    $typeCounts[$t] = ($typeCounts[$t] ?? 0) + 1;
}
$totalCount = count($notifications);
$messageCount = ($typeCounts['message'] ?? 0);
$approvedCount = ($typeCounts['booking_approve'] ?? 0);
$cancelledCount = ($typeCounts['booking_cancel'] ?? 0);
$creationCount = ($typeCounts['event'] ?? 0);
$updateCount = ($typeCounts['event_update'] ?? 0);
$paymentCount = ($typeCounts['payment'] ?? 0) + ($typeCounts['payment_alert'] ?? 0);
$feedbackCount = ($typeCounts['feedback'] ?? 0);

$activeFilter = $_GET['type'] ?? 'all';

$title = 'All Notifications - e.PLAN';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/all-notifications.css?v=' . time() . '">
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
';
include 'partials/header.php';
?>


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
                    <button class="np-unread-all-btn" id="mark-all-read-btn" data-action="mark-all-read">
                        <i class="fa-solid fa-check-double"></i> Mark all as read
                    </button>
                    <button class="np-unread-all-btn" id="mark-all-unread-btn" data-action="mark-all-unread">
                        <i class="fa-solid fa-envelope-open"></i> Mark all as unread
                    </button>
                    <button class="np-clear-all-btn" id="clear-all-btn" data-action="clear-all">
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
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-star"></i></div>
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
            <a href="/EventManagementSystem/public/notifications/all?type=feedback"
                class="np-filter-tab <?php echo ($activeFilter === 'feedback') ? 'active' : ''; ?>">
                <i class="fa-solid fa-star"></i> Feedback
                <span class="np-filter-count" id="count-feedback"><?php echo $feedbackCount; ?></span>
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
                            'info' => 'fa-circle-info',
                            'feedback' => 'fa-star',
                            'feedback_reply' => 'fa-reply'
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
                            'info' => 'Info',
                            'feedback' => 'Feedback',
                            'feedback_reply' => 'Feedback Reply'
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

                                <?php if (!empty($n['booking_id']) && $typeClass !== 'feedback' && $typeClass !== 'feedback_reply'): ?>
                                    <br>
                                    <a href="/EventManagementSystem/public/client/bookings/view?id=<?php echo htmlspecialchars($n['booking_id']); ?>"
                                        style="color:var(--brand); font-weight:700; font-size:12px; margin-top:6px; display:inline-block;">
                                        View Booking <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($typeClass === 'feedback' || $typeClass === 'feedback_reply'): ?>
                                    <br>
                                    <a href="/EventManagementSystem/public/client/feedback"
                                        style="color:var(--brand); font-weight:700; font-size:12px; margin-top:6px; display:inline-block;">
                                        View Feedback <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
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

    <!-- Client-side Pagination -->
    <div class="pagination-container" id="clientPaginationWrapper" style="display:none; margin-top: 10px;">
        <div class="pagination" id="clientPaginationControls"></div>
    </div>


    <script>
    /* ---- Client Notification Pagination (10 per page) ---- */
    document.addEventListener('DOMContentLoaded', function () {
        const ITEMS_PER_PAGE = 10;
        let currentPage = 1;

        const list = document.getElementById('np-list');
        if (!list) return;

        const allChildren = Array.from(list.children);
        const items = allChildren.filter(el => el.classList.contains('np-item'));
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);

        const wrapper = document.getElementById('clientPaginationWrapper');
        const controls = document.getElementById('clientPaginationControls');

        if (totalPages <= 1) return; // No pagination needed if 10 or fewer

        wrapper.style.display = 'flex';

        function getGroupForItem(item) {
            let prev = item.previousElementSibling;
            while (prev) {
                if (prev.classList.contains('np-date-group')) return prev;
                prev = prev.previousElementSibling;
            }
            return null;
        }

        function renderPage(page) {
            currentPage = page;
            const start = (page - 1) * ITEMS_PER_PAGE;
            const end   = start + ITEMS_PER_PAGE;
            const pageItems = new Set(items.slice(start, end));

            const visibleGroups = new Set();
            pageItems.forEach(item => {
                const g = getGroupForItem(item);
                if (g) visibleGroups.add(g);
            });

            allChildren.forEach(el => {
                if (el.classList.contains('np-item')) {
                    el.style.display = pageItems.has(el) ? '' : 'none';
                } else if (el.classList.contains('np-date-group')) {
                    el.style.display = visibleGroups.has(el) ? '' : 'none';
                }
            });

            renderControls();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function renderControls() {
            let html = '';

            html += `<button class="pg-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="clientChangePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="clientChangePage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="pg-dots">...</span>`;
                }
            }

            html += `<button class="pg-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="clientChangePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;

            controls.innerHTML = html;
        }

        window.clientChangePage = function (page) {
            if (page < 1 || page > totalPages) return;
            renderPage(page);
        };

        renderPage(1);
    });
    </script>
<?php include 'partials/footer.php'; ?>

