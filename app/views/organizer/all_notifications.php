<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications - Organizer Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" class="loading-overlay">Loading notifications...</div>

    <?php
    $activePage = 'notifications';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content" id="mainContent" style="display: none;">
        <!-- Minimal Header -->
        <header class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search your events...">
            </div>
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
            <div class="np-hero-right" id="heroActions">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span id="heroCount">0 Notifications</span>
                </div>
                <button class="np-unread-all-btn" id="markAllReadBtn">
                    <i class="fa-solid fa-check-double"></i> Mark all as read
                </button>
                <button class="np-unread-all-btn" id="markAllUnreadBtn">
                    <i class="fa-solid fa-envelope-open"></i> Mark all as unread
                </button>
                <button class="np-clear-all-btn" id="clearAllBtn">
                    <i class="fa-solid fa-trash-can"></i> Clear All
                </button>
            </div>
        </div>

        <!-- STATS -->
        <div class="np-stats-row">
            <div class="np-stat-card" data-filter="all">
                <div class="np-stat-icon green"><i class="fa-solid fa-bell"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Total</div>
                    <div class="np-stat-value" id="stat-total">0</div>
                </div>
            </div>
            <div class="np-stat-card" data-filter="booking">
                <div class="np-stat-icon green"><i class="fa-solid fa-bookmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Booking Requests</div>
                    <div class="np-stat-value" id="stat-booking">0</div>
                </div>
            </div>
            <div class="np-stat-card" data-filter="booking_cancel">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Cancellations</div>
                    <div class="np-stat-value" id="stat-cancel">0</div>
                </div>
            </div>
            <div class="np-stat-card" data-filter="event_update">
                <div class="np-stat-icon green"><i class="fa-regular fa-calendar-alt"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Event Updates</div>
                    <div class="np-stat-value" id="stat-update">0</div>
                </div>
            </div>
            <div class="np-stat-card" data-filter="message">
                <div class="np-stat-icon green"><i class="fa-solid fa-message"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Messages</div>
                    <div class="np-stat-value" id="stat-message">0</div>
                </div>
            </div>
            <div class="np-stat-card" data-filter="payment_alert">
                <div class="np-stat-icon green"><i class="fa-solid fa-credit-card"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Payments</div>
                    <div class="np-stat-value" id="stat-payment">0</div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="np-filter-bar" id="filterBar">
            <button class="np-filter-tab active" data-filter="all">
                <i class="fa-solid fa-border-all"></i> All
                <span class="np-filter-count" id="count-all">0</span>
            </button>
            <button class="np-filter-tab" data-filter="booking">
                <i class="fa-solid fa-bookmark"></i> Booking Requests
                <span class="np-filter-count" id="count-booking">0</span>
            </button>
            <button class="np-filter-tab" data-filter="booking_cancel">
                <i class="fa-solid fa-circle-xmark"></i> Cancellations
                <span class="np-filter-count" id="count-cancel">0</span>
            </button>
            <button class="np-filter-tab" data-filter="event_update">
                <i class="fa-solid fa-pen-to-square"></i> Event Updates
                <span class="np-filter-count" id="count-update">0</span>
            </button>
            <button class="np-filter-tab" data-filter="message">
                <i class="fa-solid fa-message"></i> Messages
                <span class="np-filter-count" id="count-message">0</span>
            </button>
            <button class="np-filter-tab" data-filter="payment_alert">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="np-filter-count" id="count-payment">0</span>
            </button>
        </div>

        <!-- LIST -->
        <div id="notificationsContainer">
            <div class="np-empty-state" id="emptyState" style="display: none;">
                <div class="np-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <h3>No Notifications Yet</h3>
                <p>When clients book your events or make changes, you'll see alerts here.</p>
            </div>
            <div class="np-list" id="np-list">
                <!-- Notifications injected -->
            </div>
        </div>

        <div class="pagination-row" id="paginationWrapper" style="display: none;">
            <div class="showing-text" id="showingText">
                Showing <span id="visibleCount">0</span> of <span id="totalNotifSpan">0</span> alerts
            </div>
            <div class="pagination-controls" id="paginationControls">
                <!-- Pagination injected -->
            </div>
        </div>

    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const container = document.getElementById('np-list');
            const emptyState = document.getElementById('emptyState');
            const paginationControls = document.getElementById('paginationControls');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const visibleCountLabel = document.getElementById('visibleCount');
            const totalNotifSpan = document.getElementById('totalNotifSpan');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const mainContent = document.getElementById('mainContent');

            let allNotifications = [];
            let currentFilter = new URLSearchParams(window.location.search).get('type') || 'all';
            let currentPage = 1;
            const itemsPerPage = 10;
            let filteredNotifications = [];

            async function fetchNotifications() {
                try {
                    const res = await window.emsApi.apiFetch('/api/v1/notifications');
                    allNotifications = res.data?.notifications || [];
                    updateUI();
                    loadingOverlay.style.display = 'none';
                    mainContent.style.display = 'block';
                } catch (err) {
                    console.error(err);
                    loadingOverlay.innerHTML = `<p style="color:red;">Error loading notifications: ${err.message}</p>`;
                }
            }

            function updateUI() {
                // Update Counts
                const counts = {
                    all: allNotifications.length,
                    booking: 0,
                    booking_cancel: 0,
                    event_update: 0,
                    message: 0,
                    payment_alert: 0
                };

                allNotifications.forEach(n => {
                    if (counts[n.type] !== undefined) counts[n.type]++;
                    else if (n.type === 'booking_request') counts.booking++; // Alias handling if needed
                });

                document.getElementById('heroCount').textContent = `${counts.all} Notification${counts.all !== 1 ? 's' : ''}`;
                document.getElementById('stat-total').textContent = counts.all;
                document.getElementById('stat-booking').textContent = counts.booking;
                document.getElementById('stat-cancel').textContent = counts.booking_cancel;
                document.getElementById('stat-update').textContent = counts.event_update;
                document.getElementById('stat-message').textContent = counts.message;
                document.getElementById('stat-payment').textContent = counts.payment_alert;

                document.getElementById('count-all').textContent = counts.all;
                document.getElementById('count-booking').textContent = counts.booking;
                document.getElementById('count-cancel').textContent = counts.booking_cancel;
                document.getElementById('count-update').textContent = counts.event_update;
                document.getElementById('count-message').textContent = counts.message;
                document.getElementById('count-payment').textContent = counts.payment_alert;

                // Render List
                filteredNotifications = currentFilter === 'all'
                    ? allNotifications
                    : allNotifications.filter(n => n.type === currentFilter);

                if (filteredNotifications.length === 0) {
                    container.innerHTML = '';
                    emptyState.style.display = 'block';
                    if (paginationWrapper) paginationWrapper.style.display = 'none';
                } else {
                    emptyState.style.display = 'none';
                    if (paginationWrapper) paginationWrapper.style.display = 'flex';
                    let html = '';
                    let prevGroup = '';

                    const icons = {
                        booking: 'fa-solid fa-bookmark',
                        booking_approve: 'fa-solid fa-circle-check',
                        booking_cancel: 'fa-solid fa-circle-xmark',
                        event: 'fa-regular fa-calendar-plus',
                        event_update: 'fa-solid fa-pen-to-square',
                        message: 'fa-solid fa-message',
                        payment_alert: 'fa-solid fa-credit-card',
                        info: 'fa-solid fa-circle-info'
                    };

                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    const pageItems = filteredNotifications.slice(start, end);

                    pageItems.forEach(n => {
                        const date = new Date(n.created_at);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);

                        let group = '';
                        if (date >= today) group = 'Today';
                        else if (date >= yesterday) group = 'Yesterday';
                        else group = date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

                        if (group !== prevGroup) {
                            html += `<div class="np-date-group">${group}</div>`;
                            prevGroup = group;
                        }

                        const isUnread = !n.is_read;
                        const icon = icons[n.type] || 'fa-regular fa-bell';

                        html += `
                            <div class="np-item ${isUnread ? 'unread' : ''}" id="np-item-${n.id}">
                                ${isUnread ? '<div class="np-unread-dot"></div>' : ''}
                                <div class="np-icon-bubble ${n.type}">
                                    <i class="${icon}"></i>
                                </div>
                                <div class="np-item-body">
                                    <div class="np-item-title">
                                        ${n.title}
                                        ${isUnread ? '<span class="np-new-badge">New</span>' : ''}
                                    </div>
                                    <div class="np-item-msg">${n.message}</div>
                                    <div class="np-item-footer">
                                        <span class="np-time-tag">
                                            <i class="fa-regular fa-clock"></i>
                                            ${date.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })}
                                        </span>
                                        <span class="np-type-pill ${n.type}">
                                            ${n.type.replace(/_/g, ' ')}
                                        </span>
                                        ${n.related_id && n.type === 'booking' ? `
                                            <a href="/EventManagementSystem/public/organizer/bookings/view?id=${n.related_id}" style="font-size:12px; color: var(--brand); font-weight:600; display:flex; align-items:center; gap:4px;">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Booking
                                            </a>
                                        ` : ''}
                                    </div>
                                </div>
                                ${!isUnread ? `
                                    <button class="np-unread-toggle" onclick="markUnread(${n.id})" title="Mark as unread">
                                        <i class="fa-solid fa-envelope-open"></i>
                                    </button>
                                ` : `
                                    <button class="np-unread-toggle" onclick="markRead(${n.id})" title="Mark as read">
                                        <i class="fa-solid fa-envelope"></i>
                                    </button>
                                `}
                                <button class="np-delete-btn" onclick="deleteNotification(${n.id})" title="Dismiss">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    renderPagination();
                }

                if (visibleCountLabel) visibleCountLabel.textContent = filteredNotifications.length;
                if (totalNotifSpan) totalNotifSpan.textContent = allNotifications.length;

                // Update active tab
                document.querySelectorAll('.np-filter-tab').forEach(tab => {
                    tab.classList.toggle('active', tab.dataset.filter === currentFilter);
                });
            }

            function renderPagination() {
                const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
                if (totalPages <= 1) {
                    paginationControls.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;

                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i > 1 && i < totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 && currentPage > 3) html += `<span class="dots">...</span>`;
                            if (i === totalPages - 1 && currentPage < totalPages - 2) html += `<span class="dots">...</span>`;
                            continue;
                        }
                    }
                    html += `<button class="p-btn num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                }

                html += `<button class="p-btn next ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;

                paginationControls.innerHTML = html;
            }

            window.changePage = (p) => {
                const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
                if (p < 1 || p > totalPages) return;
                currentPage = p;
                updateUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            window.markRead = async (id) => {
                await window.emsApi.apiFetch(`/api/v1/notifications/${id}/read`, { method: 'PATCH' });
                fetchNotifications();
            };

            window.markUnread = async (id) => {
                await window.emsApi.apiFetch(`/api/v1/notifications/${id}/unread`, { method: 'PATCH' });
                fetchNotifications();
            };

            window.deleteNotification = async (id) => {
                await window.emsApi.apiFetch(`/api/v1/notifications/${id}`, { method: 'DELETE' });
                fetchNotifications();
            };

            document.getElementById('markAllReadBtn').addEventListener('click', async () => {
                await window.emsApi.apiFetch('/api/v1/notifications/mark-all-read', { method: 'PATCH' });
                fetchNotifications();
            });

            document.getElementById('markAllUnreadBtn').addEventListener('click', async () => {
                await window.emsApi.apiFetch('/api/v1/notifications/mark-all-unread', { method: 'PATCH' });
                fetchNotifications();
            });

            document.getElementById('clearAllBtn').addEventListener('click', async () => {
                if (!confirm('Clear all notifications?')) return;
                await window.emsApi.apiFetch('/api/v1/notifications', { method: 'DELETE' });
                fetchNotifications();
            });

            document.querySelectorAll('.np-filter-tab, .np-stat-card').forEach(el => {
                el.addEventListener('click', () => {
                    currentFilter = el.dataset.filter;
                    currentPage = 1; // Reset to page 1
                    updateUI();
                });
            });

            fetchNotifications();
        });
    </script>
</body>

</html>