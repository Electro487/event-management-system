
// ---- Shared State ----
let _seenIds = new Set();
let _isFirstLoad = true;

function _useApi() {
    try {
        return (typeof window.API_MODE_CLIENT !== 'undefined' ? !!window.API_MODE_CLIENT : !!(window.emsApi && window.emsApi.getToken && window.emsApi.getToken()));
    } catch {
        return false;
    }
}

// ---- Global Action Handlers ----

window.markAllRead = function () {
    const successCallback = () => {
        window.fetchNotifications();
        window.fetchNotificationCounts && window.fetchNotificationCounts();
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch('/api/v1/notifications/mark-all-read', { method: 'PATCH' })
            .then(successCallback)
            .catch(err => console.error('markAllRead api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/mark-all-read', { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('markAllRead error:', err));
    }
};

window.markAllUnread = function () {
    const successCallback = () => {
        window.fetchNotifications();
        window.fetchNotificationCounts && window.fetchNotificationCounts();
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch('/api/v1/notifications/mark-all-unread', { method: 'PATCH' })
            .then(successCallback)
            .catch(err => console.error('markAllUnread api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/mark-all-unread', { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('markAllUnread error:', err));
    }
};

window.deleteAllNotifications = function () {
    if (!confirm('Clear all notifications? This cannot be undone.')) return;
    
    const successCallback = () => {
        window.fetchNotifications();
        window.fetchNotificationCounts && window.fetchNotificationCounts();
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch('/api/v1/notifications/clear-all', { method: 'DELETE' })
            .then(successCallback)
            .catch(err => console.error('deleteAll api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/clear-all', { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('deleteAll error:', err));
    }
};

window.deleteNotification = function (id) {
    if (!id || !confirm("Remove this notification?")) return;

    // Optimistic UI
    const item = document.getElementById(`np-item-${id}`);
    if (item) item.remove();

    const successCallback = () => {
        window.fetchNotificationCounts && window.fetchNotificationCounts();
        // If not on the full page, we might want to refresh the dropdown
        if (!item) window.fetchNotifications(); 
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch(`/api/v1/notifications/${id}`, { method: 'DELETE' })
            .then(successCallback)
            .catch(err => console.error('deleteNotification api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/delete?id=' + id, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('deleteNotification error:', err));
    }
};

window.markAsRead = function (id) {
    if (!id) return;

    // Optimistic UI for full page
    const item = document.getElementById(`np-item-${id}`);
    if (item) {
        item.classList.remove('unread');
        const dot = item.querySelector('.np-unread-dot');
        if (dot) dot.remove();
        const badge = item.querySelector('.np-new-badge');
        if (badge) badge.remove();
        
        // Add unread toggle if it doesn't exist
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

    const successCallback = () => {
        window.fetchNotifications();
        window.fetchNotificationCounts && window.fetchNotificationCounts();
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch(`/api/v1/notifications/${id}/read`, { method: 'PATCH' })
            .then(successCallback)
            .catch(err => console.error('markAsRead api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/read?id=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('markAsRead error:', err));
    }
};

window.markAsUnread = function (id) {
    if (!id) return;

    // Optimistic UI for full page
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

    const successCallback = () => {
        window.fetchNotifications();
        window.fetchNotificationCounts && window.fetchNotificationCounts();
    };

    if (_useApi() && window.emsApi) {
        window.emsApi.apiFetch(`/api/v1/notifications/${id}/unread`, { method: 'PATCH' })
            .then(successCallback)
            .catch(err => console.error('markAsUnread api error:', err));
    } else {
        fetch('/EventManagementSystem/public/notifications/unread?id=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.success) successCallback();
            })
            .catch(err => console.error('markAsUnread error:', err));
    }
};

// ---- Polling and UI Updates ----

window.fetchNotifications = function () {
    const fetchFn = (_useApi() && window.emsApi)
        ? () => window.emsApi.apiFetch('/api/v1/notifications/latest')
        : () => fetch('/EventManagementSystem/public/notifications').then(r => r.json());

    fetchFn()
        .then(data => {
            const actualData = data.data || data;
            if (!actualData || !actualData.notifications) return;

            // Handle Toast and Initial State
            if (_isFirstLoad) {
                actualData.notifications.forEach(n => _seenIds.add(n.id));
                _isFirstLoad = false;
            } else {
                actualData.notifications.forEach(n => {
                    if (n.is_read == 0 && !_seenIds.has(n.id)) {
                        _showToast(n);
                        _seenIds.add(n.id);
                    }
                });
            }

            // Update Dropdown UI
            _updateDropdownUI(actualData);

            // Update Full Page UI if present
            const npList = document.getElementById('np-list');
            if (npList) {
                const urlParams = new URLSearchParams(window.location.search);
                const filterType = urlParams.get('type');
                const pageFetchFn = (_useApi() && window.emsApi)
                    ? () => window.emsApi.apiFetch('/api/v1/notifications' + (filterType ? '?type=' + filterType : ''))
                    : () => fetch('/EventManagementSystem/public/notifications/all-json' + (filterType ? '?type=' + filterType : '')).then(r => r.json());

                pageFetchFn()
                    .then(allData => {
                        const actualAllData = allData.data || allData;
                        if (actualAllData && actualAllData.notifications) {
                            _updatePageUI(actualAllData);
                        }
                    })
                    .catch(() => { });
            }
        })
        .catch(err => console.error('fetchNotifications error:', err));
};

window.fetchNotificationCounts = function () {
    const statTotal = document.getElementById('stat-total');
    if (!statTotal) return;

    const fetchFn = (_useApi() && window.emsApi)
        ? () => window.emsApi.apiFetch('/api/v1/notifications/counts')
        : () => fetch('/EventManagementSystem/public/notifications/counts').then(r => r.json());

    fetchFn()
        .then(res => {
            const data = res.data || res;
            if (!data || data.error) return;

            const statsMap = {
                'stat-total': data.all,
                'stat-booking': data.booking,
                'stat-approve': data.booking_approve,
                'stat-cancel': data.booking_cancel,
                'stat-update': data.event_update,
                'stat-message': data.message,
                'stat-creation': data.event_creation,
                'stat-feedback': data.feedback
            };
            for (const [id, val] of Object.entries(statsMap)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }

            const filterMap = {
                'count-all': data.all,
                'count-booking': data.booking,
                'count-approve': data.booking_approve,
                'count-cancel': data.booking_cancel,
                'count-update': data.event_update,
                'count-message': data.message,
                'count-creation': data.event_creation,
                'count-feedback': data.feedback
            };
            for (const [id, val] of Object.entries(filterMap)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }

            const heroBadge = document.querySelector('.np-hero-badge span');
            if (heroBadge) {
                heroBadge.textContent = data.all + ' Notification' + (data.all != 1 ? 's' : '');
            }
        })
        .catch(err => console.error('fetchNotificationCounts error:', err));
};

// ---- Private Helpers ----

function _updateDropdownUI(data) {
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
}

function _updatePageUI(data) {
    const npList = document.getElementById('np-list');
    if (!npList) return;

    const notifications = data.notifications || [];

    notifications.forEach(n => {
        let item = document.getElementById(`np-item-${n.id}`);
        const isRead = n.is_read == 1;

        if (item) {
            // Update existing
            item.classList.toggle('unread', !isRead);
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

            let toggle = item.querySelector('.np-unread-toggle');
            if (isRead && !toggle) {
                toggle = document.createElement('button');
                toggle.className = 'np-unread-toggle';
                toggle.title = 'Mark as unread';
                toggle.innerHTML = '<i class="fa-solid fa-envelope-open"></i>';
                toggle.setAttribute('data-id', n.id);
                toggle.setAttribute('data-action', 'unread');
                const deleteBtn = item.querySelector('.np-delete-btn');
                if (deleteBtn) item.insertBefore(toggle, deleteBtn);
                else item.appendChild(toggle);
            } else if (!isRead && toggle) {
                toggle.remove();
            }
            return;
        }

        // Create new
        item = document.createElement('div');
        item.className = 'np-item' + (!isRead ? ' unread' : '');
        item.id = `np-item-${n.id}`;
        item.setAttribute('data-id', n.id);
        item.setAttribute('data-action', 'read');

        const icons = {
            'booking': 'fa-solid fa-bookmark',
            'booking_approve': 'fa-solid fa-circle-check',
            'booking_cancel': 'fa-solid fa-circle-xmark',
            'event': 'fa-regular fa-calendar-plus',
            'event_update': 'fa-solid fa-pen-to-square',
            'event_delete': 'fa-solid fa-trash-can',
            'message': 'fa-solid fa-message',
            'feedback': 'fa-solid fa-comment-dots',
            'feedback_reply': 'fa-solid fa-reply',
            'feedback_mention': 'fa-solid fa-at'
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
            ${isRead ? '<button class="np-unread-toggle" data-id="'+n.id+'" data-action="unread" title="Mark as unread"><i class="fa-solid fa-envelope-open"></i></button>' : ''}
            <button class="np-delete-btn" data-id="'+n.id+'" data-action="delete" title="Dismiss"><i class="fa-solid fa-xmark"></i></button>
        `;

        const groupLabel = _getGroupLabel(n.created_at);
        let dateGroup = Array.from(npList.querySelectorAll('.np-date-group')).find(el => el.textContent.trim() === groupLabel);
        if (!dateGroup) {
            dateGroup = document.createElement('div');
            dateGroup.className = 'np-date-group';
            dateGroup.textContent = groupLabel;
            npList.prepend(dateGroup);
        }
        dateGroup.after(item);
    });
}

function _showToast(n) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'notif-toast showing';
    const icons = { 'booking': 'fa-solid fa-bookmark', 'booking_approve': 'fa-solid fa-circle-check', 'booking_cancel': 'fa-solid fa-circle-xmark', 'event': 'fa-regular fa-calendar-plus', 'event_update': 'fa-solid fa-pen-to-square', 'message': 'fa-solid fa-message', 'feedback': 'fa-solid fa-comment-dots' };
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
    toast.onclick = (e) => {
        if (e.target.closest('.toast-close')) return;
        window.markAsRead(n.id);
        window.location.href = '/EventManagementSystem/public/notifications/all';
    };
    toast.querySelector('.toast-close').onclick = (e) => { e.stopPropagation(); _dismissToast(toast); };
    setTimeout(() => _dismissToast(toast), 8000);
}

function _dismissToast(toast) {
    if (!toast.parentNode) return;
    toast.classList.remove('showing');
    toast.classList.add('hide');
    setTimeout(() => toast.remove(), 400);
}

function _getGroupLabel(dateStr) {
    const date = new Date(dateStr.replace(' ', 'T'));
    const today = new Date();
    const yesterday = new Date(); yesterday.setDate(today.getDate() - 1);
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
    let interval = seconds / 31536000; if (interval > 1) return Math.floor(interval) + ' years ago';
    interval = seconds / 2592000; if (interval > 1) return Math.floor(interval) + ' months ago';
    interval = seconds / 86400; if (interval > 1) return Math.floor(interval) + ' days ago';
    interval = seconds / 3600; if (interval > 1) return Math.floor(interval) + ' hours ago';
    interval = seconds / 60; if (interval > 1) return Math.floor(interval) + ' minutes ago';
    return Math.floor(seconds) + ' seconds ago';
}

// ---- Initialization ----

document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notifications-dropdown');

    // Polling logic
    window.fetchNotifications();
    setInterval(window.fetchNotifications, 5000); // Poll every 5s for truly 'live' feel

    if (document.getElementById('stat-total')) {
        window.fetchNotificationCounts();
        setInterval(window.fetchNotificationCounts, 10000); // Stats can be slower
    }

    // Delegation
    document.addEventListener('click', function (e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;

        const id = target.getAttribute('data-id');
        const action = target.getAttribute('data-action');
        if (!action) return;

        e.stopPropagation();

        if (action === 'read') {
            const item = document.getElementById(`np-item-${id}`) || target.closest('.nd-item');
            if (item && item.classList.contains('unread')) window.markAsRead(id);
        } else if (action === 'unread') {
            window.markAsUnread(id);
        } else if (action === 'delete') {
            window.deleteNotification(id);
        } else if (action === 'mark-all-read') {
            window.markAllRead();
        } else if (action === 'mark-all-unread') {
            window.markAllUnread();
        } else if (action === 'clear-all') {
            window.deleteAllNotifications();
        }
    });

    if (bellBtn && dropdown) {
        bellBtn.onclick = (e) => { e.stopPropagation(); dropdown.classList.toggle('show'); };
        document.onclick = (e) => { if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) dropdown.classList.remove('show'); };
    }
});
