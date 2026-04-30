/**
 * Admin Notifications Management JS
 */

let allNotifications = [];
let filteredNotifications = [];
let currentFilter = 'all';
let currentPage = 1;
const itemsPerPage = 10;

document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    initFilters();
});

async function loadNotifications() {
    if (!window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/notifications');
        if (res.success) {
            allNotifications = res.data.notifications || [];
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Failed to load notifications:', err);
    }
}

function initFilters() {
    const tabs = document.querySelectorAll('.np-filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filterType;
            currentPage = 1;
            applyFilters();
        });
    });
}

function updateStats() {
    const counts = {
        all: allNotifications.length,
        booking: 0,
        event: 0,
        event_updates: 0,
        message: 0,
        booking_cancel: 0,
        payment_alert: 0
    };

    allNotifications.forEach(n => {
        const t = n.type || 'info';
        if (t === 'booking') counts.booking++;
        if (t === 'event') counts.event++;
        if (t === 'event_update' || t === 'event_delete') counts.event_updates++;
        if (t === 'message') counts.message++;
        if (t === 'booking_cancel') counts.booking_cancel++;
        if (t === 'payment_alert') counts.payment_alert++;
    });

    // Update Header Badge
    const totalLabel = document.getElementById('total-count-label');
    if (totalLabel) totalLabel.innerText = `${counts.all} Total Alerts`;

    // Update Stats Cards
    document.getElementById('stat-total').innerText = counts.all;
    document.getElementById('stat-booking').innerText = counts.booking;
    document.getElementById('stat-creation').innerText = counts.event;
    document.getElementById('stat-cancel').innerText = counts.booking_cancel;
    document.getElementById('stat-message').innerText = counts.message;
    document.getElementById('stat-update').innerText = counts.event_updates;
    document.getElementById('stat-payment').innerText = counts.payment_alert;

    // Update Filter Counts
    document.getElementById('count-all').innerText = counts.all;
    document.getElementById('count-booking').innerText = counts.booking;
    document.getElementById('count-creation').innerText = counts.event;
    document.getElementById('count-update').innerText = counts.event_updates;
    document.getElementById('count-message').innerText = counts.message;
    document.getElementById('count-cancel').innerText = counts.booking_cancel;
    document.getElementById('count-payment').innerText = counts.payment_alert;
}

function applyFilters() {
    filteredNotifications = allNotifications.filter(n => {
        if (currentFilter === 'all') return true;
        if (currentFilter === 'event_updates') return n.type === 'event_update' || n.type === 'event_delete';
        return n.type === currentFilter;
    });

    renderList(filteredNotifications);
    renderPagination();
}

function renderList(items) {
    const container = document.getElementById('notifications-container');
    
    if (!items.length) {
        container.innerHTML = `
            <div class="np-empty-state">
                <div class="np-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <h3>No Notifications</h3>
                <p>You're all clear! No ${currentFilter !== 'all' ? currentFilter.replace('_', ' ') : ''} alerts found.</p>
            </div>
        `;
        return;
    }

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = items.slice(start, end);

    let html = '<div class="np-list">';
    let prevDate = null;

    pageItems.forEach(n => {
        const dateObj = new Date(n.created_at);
        const groupLabel = getGroupLabel(dateObj);

        if (groupLabel !== prevDate) {
            html += `<div class="np-date-group">${groupLabel}</div>`;
            prevDate = groupLabel;
        }

        const isUnread = !n.is_read;
        const type = n.type || 'info';
        const iconClass = getIconForType(type);

        html += `
            <div class="np-item ${isUnread ? 'unread' : ''}" id="np-item-${n.id}">
                ${isUnread ? '<div class="np-unread-dot"></div>' : ''}
                <div class="np-icon-bubble ${type}">
                    <i class="${iconClass}"></i>
                </div>
                <div class="np-item-body" onclick="markAsRead(${n.id})">
                    <div class="np-item-title">
                        ${escapeHtml(n.title)}
                        ${isUnread ? '<span class="np-new-badge">New</span>' : ''}
                    </div>
                    <div class="np-item-msg">${escapeHtml(n.message).replace(/\n/g, '<br>')}</div>
                    <div class="np-item-footer">
                        <span class="np-time-tag">
                            <i class="fa-regular fa-clock"></i>
                            ${dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true })}
                        </span>
                        <span class="np-type-pill ${type}">
                            ${type.replace('_', ' ').charAt(0).toUpperCase() + type.replace('_', ' ').slice(1)}
                        </span>
                    </div>
                </div>
                ${!isUnread ? `
                    <button class="np-unread-toggle" onclick="toggleUnread(${n.id})" title="Mark as unread">
                        <i class="fa-solid fa-envelope-open"></i>
                    </button>
                ` : ''}
                <button class="np-delete-btn" onclick="deleteSingleNotification(${n.id})" title="Dismiss">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

function renderPagination() {
    const container = document.getElementById('pagination-container');
    const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `
        <div class="pagination">
            <button class="pg-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fa-solid fa-angle-left"></i>
            </button>
    `;

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="pg-dots">...</span>`;
        }
    }

    html += `
            <button class="pg-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                <i class="fa-solid fa-angle-right"></i>
            </button>
        </div>
    `;

    container.innerHTML = html;
}

window.changePage = (page) => {
    currentPage = page;
    renderList(filteredNotifications);
    renderPagination();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

function getGroupLabel(date) {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date >= today) return 'Today';
    if (date >= yesterday) return 'Yesterday';
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function getIconForType(type) {
    const icons = {
        'booking': 'fa-solid fa-bookmark',
        'booking_approve': 'fa-solid fa-circle-check',
        'booking_cancel': 'fa-solid fa-circle-xmark',
        'event': 'fa-regular fa-calendar-plus',
        'event_update': 'fa-solid fa-pen-to-square',
        'event_delete': 'fa-solid fa-trash-can',
        'message': 'fa-solid fa-message',
        'payment': 'fa-solid fa-credit-card',
        'payment_alert': 'fa-solid fa-credit-card',
        'system': 'fa-solid fa-shield-halved',
        'info': 'fa-solid fa-circle-info'
    };
    return icons[type] || 'fa-regular fa-bell';
}

async function markAsRead(id) {
    const item = allNotifications.find(n => n.id === id);
    if (!item || item.is_read) return;

    try {
        const res = await window.emsApi.apiFetch(`/api/v1/notifications/${id}/read`, { method: 'PATCH' });
        if (res.success) {
            item.is_read = 1;
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error marking as read:', err);
    }
}

async function toggleUnread(id) {
    try {
        const res = await window.emsApi.apiFetch(`/api/v1/notifications/${id}/unread`, { method: 'PATCH' });
        if (res.success) {
            const item = allNotifications.find(n => n.id === id);
            if (item) item.is_read = 0;
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error toggling unread:', err);
    }
}

async function deleteSingleNotification(id) {
    if (!confirm("Dismiss this notification?")) return;

    try {
        const res = await window.emsApi.apiFetch(`/api/v1/notifications/${id}`, { method: 'DELETE' });
        if (res.success) {
            allNotifications = allNotifications.filter(n => n.id !== id);
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error deleting notification:', err);
    }
}

async function clearAllNotifications() {
    if (!confirm('Clear all notifications? This action cannot be undone.')) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/notifications', { method: 'DELETE' });
        if (res.success) {
            allNotifications = [];
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error clearing notifications:', err);
    }
}

async function markAllUnread() {
    try {
        const res = await window.emsApi.apiFetch('/api/v1/notifications/mark-all-unread', { method: 'PATCH' });
        if (res.success) {
            allNotifications.forEach(n => n.is_read = 0);
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error marking all unread:', err);
    }
}

async function markAllRead() {
    try {
        const res = await window.emsApi.apiFetch('/api/v1/notifications/mark-all-read', { method: 'PATCH' });
        if (res.success) {
            allNotifications.forEach(n => n.is_read = 1);
            updateStats();
            applyFilters();
        }
    } catch (err) {
        console.error('Error marking all read:', err);
    }
}

function escapeHtml(unsafe) {
    return (unsafe || "").toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
