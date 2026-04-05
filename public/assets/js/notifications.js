/* =============================================
   Notification System - Global Helper Functions
   Defined OUTSIDE DOMContentLoaded so PHP-rendered
   onclick="window.markAsRead(id)" always works.
   ============================================= */

// ---- Shared State ----
let _seenIds = new Set();
let _isFirstLoad = true;

// ---- Global API calls ----
window.markAsRead = function (id) {
    if (!id) return;

    // --- Optimistic UI ---
    const item = document.getElementById(`np-item-${id}`);
    if (item) {
        item.classList.remove('unread');
        const dot = item.querySelector('.np-unread-dot');
        if (dot) dot.remove();
        const badge = item.querySelector('.np-new-badge');
        if (badge) badge.remove();

        // Add unread toggle if it doesnt exist
        if (!item.querySelector('.np-unread-toggle')) {
            const toggle = document.createElement('button');
            toggle.className = 'np-unread-toggle';
            toggle.setAttribute('data-id', id);
            toggle.setAttribute('data-action', 'unread');
            toggle.title = 'Mark as unread';
            toggle.innerHTML = '<i class="fa-solid fa-envelope-open"></i>';
            const del = item.querySelector('.np-delete-btn');
            if (del) item.insertBefore(toggle, del);
            else item.appendChild(toggle);
        }
    }

    fetch('/EventManagementSystem/public/notifications/read?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.fetchNotifications();
                window.fetchNotificationCounts(); // Sync counters too
            }
        })
        .catch(err => console.error('markAsRead error:', err));
};

window.markAsUnread = function (id) {
    if (!id) return;

    // --- Optimistic UI ---
    const item = document.getElementById(`np-item-${id}`);
    if (item) {
        item.classList.add('unread');
        if (!item.querySelector('.np-unread-dot')) {
            const dot = document.createElement('div');
            dot.className = 'np-unread-dot';
            item.prepend(dot);
        }
        const toggle = item.querySelector('.np-unread-toggle');
        if (toggle) toggle.remove();
    }

    fetch('/EventManagementSystem/public/notifications/unread?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.fetchNotifications();
                window.fetchNotificationCounts();
            }
        })
        .catch(err => console.error('markAsUnread error:', err));
};

window.fetchNotifications = function () {
    fetch('/EventManagementSystem/public/notifications')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.notifications) return;

            if (_isFirstLoad) {
                data.notifications.forEach(n => {
                    _seenIds.add(n.id); // Remember all existing notifications
                });
                _isFirstLoad = false;
            } else {
                data.notifications.forEach(n => {
                    if (n.is_read == 0 && !_seenIds.has(n.id)) {
                        _showToast(n);
                        _seenIds.add(n.id);
                    }
                });
            }
            _updateUI(data);

            // If we're on the full notification page, separately fetch ALL notifications
            // so items beyond the dropdown's 3-item limit also get their state updated.
            const npList = document.getElementById('np-list');
            if (npList) {
                // Detect current filter from URL
                const urlParams = new URLSearchParams(window.location.search);
                const filterType = urlParams.get('type');
                const fetchUrl = '/EventManagementSystem/public/notifications/all-json' + (filterType ? '?type=' + filterType : '');

                fetch(fetchUrl)
                    .then(r => r.json())
                    .then(allData => {
                        if (allData && allData.notifications) {
                            _updatePageUI(allData);
                        }
                    })
                    .catch(() => { }); // Silent fail — page items still work via init-scan
            }
        })
        .catch(err => console.error('fetchNotifications error:', err));
};

/**
 * Periodically fetches notification counts for the stats cards on the All Notifications page.
 */
window.fetchNotificationCounts = function () {
    const statTotal = document.getElementById('stat-total');
    if (!statTotal) return; // Only run if on the notifications page

    fetch('/EventManagementSystem/public/notifications/counts')
        .then(r => r.json())
        .then(data => {
            if (!data || data.error) return;

            // Update Stat Cards (Top row)
            const statsMap = {
                'stat-total': data.all,
                'stat-booking': data.booking,
                'stat-approve': data.booking_approve,
                'stat-cancel': data.booking_cancel,
                'stat-update': data.event_update,
                'stat-message': data.message,
                'stat-creation': data.event_creation
            };
            for (const [id, val] of Object.entries(statsMap)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }

            // Update Filter Tab Pills
            const filterMap = {
                'count-all': data.all,
                'count-booking': data.booking,
                'count-approve': data.booking_approve,
                'count-cancel': data.booking_cancel,
                'count-update': data.event_update,
                'count-message': data.message,
                'count-creation': data.event_creation
            };
            for (const [id, val] of Object.entries(filterMap)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }

            // Update Hero Badge Count
            const heroBadge = document.querySelector('.np-hero-badge span');
            if (heroBadge) {
                heroBadge.textContent = data.all + ' Notification' + (data.all != 1 ? 's' : '');
            }
        })
        .catch(err => console.error('fetchNotificationCounts error:', err));
};

// ---- Toast ----
function _showToast(n) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'notif-toast showing';

    const icons = {
        'booking': 'fa-solid fa-bookmark',
        'booking_approve': 'fa-solid fa-circle-check',
        'booking_cancel': 'fa-solid fa-circle-xmark',
        'event': 'fa-regular fa-calendar-plus',
        'event_update': 'fa-solid fa-pen-to-square',
        'event_delete': 'fa-solid fa-trash-can',
        'message': 'fa-solid fa-message',
        'system': 'fa-solid fa-gear',
        'info': 'fa-solid fa-circle-info'
    };
    const iconClass = icons[n.type] || 'fa-solid fa-bell';

    toast.innerHTML = `
        <div class="toast-icon"><i class="${iconClass}"></i></div>
        <div class="toast-body">
            <div class="toast-header">
                <h4>${_escapeHtml(n.title)}</h4>
                <button class="toast-close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <p class="toast-msg">${_escapeHtml(n.message)}</p>
        </div>
        <div class="toast-progress"></div>
    `;

    container.appendChild(toast);

    toast.addEventListener('click', (e) => {
        if (e.target.closest('.toast-close')) return;
        window.markAsRead(n.id);
        window.location.href = '/EventManagementSystem/public/notifications/all';
    });
    toast.querySelector('.toast-close').onclick = (e) => {
        e.stopPropagation();
        _dismissToast(toast);
    };
    setTimeout(() => _dismissToast(toast), 8000);
}

function _dismissToast(toast) {
    if (!toast.parentNode) return;
    toast.classList.remove('showing');
    toast.classList.add('hide');
    setTimeout(() => toast.remove(), 400);
}

// ---- Update dropdown UI ----
function _updateUI(data) {
    const badge = document.getElementById('unread-badge');
    const unreadTag = document.getElementById('nd-unread-status');
    const list = document.getElementById('nd-list');

    const unreadCount = data.unreadCount || 0;
    const notifications = data.notifications || [];

    if (unreadCount > 0) {
        if (badge) { badge.textContent = unreadCount; badge.style.display = 'flex'; }
        if (unreadTag) { unreadTag.textContent = unreadCount + ' UNREAD'; unreadTag.style.display = 'inline-block'; }
    } else {
        if (badge) badge.style.display = 'none';
        if (unreadTag) unreadTag.style.display = 'none';
    }

    if (list) {
        if (notifications.length === 0) {
            list.innerHTML = '<div class="nd-empty"><i class="fa-regular fa-bell-slash"></i>No new notifications</div>';
        } else {
            list.innerHTML = '';
            notifications.forEach(n => {
                const item = document.createElement('div');
                item.className = 'nd-item' + (n.is_read == 0 ? ' unread' : '');
                item.setAttribute('data-id', n.id);
                item.setAttribute('data-action', 'read');
                item.innerHTML = `
                    <div class="nd-body">
                        <h4 class="nd-title">${_escapeHtml(n.title)}</h4>
                        <p class="nd-message">${_escapeHtml(n.message).replace(/\n/g, '<br>')}</p>
                        <span class="nd-time">${_timeAgo(n.created_at)}</span>
                    </div>
                    ${n.is_read == 0 ? '<div class="nd-dot"></div>' : ''}
                `;
                list.appendChild(item);
            });
        }
    }

    _updatePageUI(data);
}

// ---- Update full notification page UI ----
function _updatePageUI(data) {
    const npList = document.getElementById('np-list');
    if (!npList) return;

    const notifications = data.notifications || [];

    notifications.forEach(n => {
        let item = document.getElementById(`np-item-${n.id}`);
        const isRead = n.is_read == 1;

        if (item) {
            // --- Update existing item ---
            item.classList.toggle('unread', !isRead);
            item.setAttribute('data-id', n.id);
            item.setAttribute('data-action', 'read');

            const dot = item.querySelector('.np-unread-dot');
            if (!isRead && !dot) {
                const newDot = document.createElement('div');
                newDot.className = 'np-unread-dot';
                item.prepend(newDot);
            } else if (isRead && dot) {
                dot.remove();
            }

            const badgeSpan = item.querySelector('.np-new-badge');
            if (!isRead && !badgeSpan) {
                const titleEl = item.querySelector('.np-item-title');
                if (titleEl) titleEl.innerHTML += ' <span class="np-new-badge">New</span>';
            } else if (isRead && badgeSpan) {
                badgeSpan.remove();
            }

            // Unread toggle button
            let toggle = item.querySelector('.np-unread-toggle');
            if (isRead && !toggle) {
                toggle = document.createElement('button');
                toggle.className = 'np-unread-toggle';
                toggle.title = 'Mark as unread';
                toggle.innerHTML = '<i class="fa-solid fa-envelope-open"></i>';
                toggle.setAttribute('data-id', n.id);
                toggle.setAttribute('data-action', 'unread');

                // Insert before the delete button so layout stays correct
                const deleteBtn = item.querySelector('.np-delete-btn');
                if (deleteBtn) item.insertBefore(toggle, deleteBtn);
                else item.appendChild(toggle);
            } else if (!isRead && toggle) {
                toggle.remove();
            }

            return; // skip create block
        }

        // --- Create new item (not yet in DOM) ---
        item = document.createElement('div');
        item.className = 'np-item' + (!isRead ? ' unread' : '');
        item.id = `np-item-${n.id}`;

        const icons = {
            'booking': 'fa-solid fa-bookmark',
            'booking_approve': 'fa-solid fa-circle-check',
            'booking_cancel': 'fa-solid fa-circle-xmark',
            'event': 'fa-regular fa-calendar-plus',
            'event_update': 'fa-solid fa-pen-to-square',
            'event_delete': 'fa-solid fa-trash-can',
            'message': 'fa-solid fa-message',
            'system': 'fa-solid fa-circle-info',
            'info': 'fa-solid fa-circle-info'
        };
        const iconClass = icons[n.type] || 'fa-regular fa-bell';

        item.innerHTML = `
            ${!isRead ? '<div class="np-unread-dot"></div>' : ''}
            <div class="np-icon-bubble ${n.type}">
                <i class="${iconClass}"></i>
            </div>
            <div class="np-item-body">
                <div class="np-item-title">
                    ${_escapeHtml(n.title)}
                    ${!isRead ? '<span class="np-new-badge">New</span>' : ''}
                </div>
                <div class="np-item-msg">${_escapeHtml(n.message).replace(/\n/g, '<br>')}</div>
                <div class="np-item-time"><i class="fa-regular fa-clock"></i> ${_timeAgo(n.created_at)}</div>
            </div>
            ${isRead ? '<button class="np-unread-toggle" title="Mark as unread"><i class="fa-solid fa-envelope-open"></i></button>' : ''}
            <button class="np-delete-btn" title="Dismiss"><i class="fa-solid fa-xmark"></i></button>
        `;

        // Wire up data attributes for delegation
        item.setAttribute('data-id', n.id);
        item.setAttribute('data-action', 'read');

        const unreadToggle = item.querySelector('.np-unread-toggle');
        if (unreadToggle) {
            unreadToggle.setAttribute('data-id', n.id);
            unreadToggle.setAttribute('data-action', 'unread');
        }

        const deleteBtn = item.querySelector('.np-delete-btn');
        if (deleteBtn) {
            deleteBtn.setAttribute('data-id', n.id);
            deleteBtn.setAttribute('data-action', 'delete');
        }

        // Place into the correct date group
        const groupLabel = _getGroupLabel(n.created_at);
        let dateGroup = Array.from(npList.querySelectorAll('.np-date-group'))
            .find(el => el.textContent.trim() === groupLabel);
        if (!dateGroup) {
            dateGroup = document.createElement('div');
            dateGroup.className = 'np-date-group';
            dateGroup.textContent = groupLabel;
            npList.prepend(dateGroup);
        }
        dateGroup.after(item);

        const emptyState = document.querySelector('.np-empty-state');
        if (emptyState) emptyState.remove();
    });
}


// ---- Utilities ----
function _getGroupLabel(dateStr) {
    const date = new Date(dateStr.replace(' ', 'T'));
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);
    if (date.toDateString() === today.toDateString()) return 'Today';
    if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function _escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function _timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date.replace(' ', 'T'))) / 1000);
    if (isNaN(seconds)) return 'some time ago';
    let interval;
    interval = seconds / 31536000; if (interval > 1) return Math.floor(interval) + ' years ago';
    interval = seconds / 2592000; if (interval > 1) return Math.floor(interval) + ' months ago';
    interval = seconds / 86400; if (interval > 1) return Math.floor(interval) + ' days ago';
    interval = seconds / 3600; if (interval > 1) return Math.floor(interval) + ' hours ago';
    interval = seconds / 60; if (interval > 1) return Math.floor(interval) + ' minutes ago';
    return Math.floor(seconds) + ' seconds ago';
}

/* =============================================
   Dropdown-specific setup (requires bell button)
   ============================================= */
document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notifications-dropdown');
    const list = document.getElementById('nd-list');
    const markAllBtn = document.getElementById('mark-all-read');

    // Start polling immediately — works on EVERY page (notification page + others)
    window.fetchNotifications();
    setInterval(window.fetchNotifications, 10000); // 10s for dropdown/toasts

    // Also poll counts if on the notifications page
    if (document.getElementById('stat-total')) {
        window.fetchNotificationCounts();
        setInterval(window.fetchNotificationCounts, 10000); // 10s for stats
    }

    // ---- EVENT DELEGATION: Handle all notification clicks in one place ----
    document.addEventListener('click', function (e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;

        const id = target.getAttribute('data-id');
        const action = target.getAttribute('data-action');
        if (!id || !action) return;

        e.stopPropagation();

        if (action === 'read') {
            const isUnread = target.classList.contains('unread');
            if (isUnread) window.markAsRead(id);
        } else if (action === 'unread') {
            window.markAsUnread(id);
        } else if (action === 'delete') {
            window.deleteNotification && window.deleteNotification(id);
        }
    });

    // The rest only applies to pages that have the bell/dropdown
    if (!bellBtn || !dropdown) return;

    bellBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        if (dropdown.classList.contains('show')) window.fetchNotifications();
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    if (markAllBtn) {
        markAllBtn.textContent = 'Mark all as unread';
        markAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            fetch('/EventManagementSystem/public/notifications/mark-all-unread', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.fetchNotifications();
                        window.fetchNotificationCounts && window.fetchNotificationCounts();
                    }
                });
        });
    }
});
