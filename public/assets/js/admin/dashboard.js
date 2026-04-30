/**
 * Admin Dashboard API Integration
 */

document.addEventListener('DOMContentLoaded', () => {
    loadDashboardData();

    // Global Search
    const globalSearch = document.getElementById('globalSearchInput');
    if (globalSearch) {
        globalSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                window.location.href = `/EventManagementSystem/public/admin/events?search=${encodeURIComponent(globalSearch.value)}`;
            }
        });
    }
});

async function loadDashboardData() {
    if (!window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/dashboard/admin');
        if (res.success) {
            populateDashboard(res.data);
        }
    } catch (err) {
        console.error('Failed to load admin dashboard:', err);
    }
}

function populateDashboard(data) {
    // Stats
    document.getElementById('stat-total-users').innerText = (data.total_users || 0).toLocaleString();
    document.getElementById('stat-total-events').innerText = (data.total_events || 0).toLocaleString();
    document.getElementById('stat-total-bookings').innerText = (data.total_bookings || 0).toLocaleString();
    document.getElementById('stat-revenue').innerText = 'Rs. ' + parseFloat(data.revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });

    // Welcome Banner
    document.getElementById('welcome-total-users').innerText = (data.total_users || 0).toLocaleString();
    document.getElementById('welcome-total-events').innerText = (data.total_events || 0).toLocaleString();

    // Recent Bookings
    renderRecentBookings(data.recent_bookings || []);

    // Upcoming Events
    const upcomingCountEl = document.getElementById('upcoming-events-count');
    if (upcomingCountEl) {
        upcomingCountEl.innerText = `${data.upcoming_events?.length || 0} Active`;
    }
    renderUpcomingEvents(data.upcoming_events || []);
}

function renderRecentBookings(bookings) {
    const tbody = document.getElementById('recent-bookings-body');
    if (!bookings.length) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No recent bookings found.</td></tr>';
        return;
    }

    tbody.innerHTML = bookings.map(b => {
        const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
        const dispTitle = eSnap?.title || b.event_title || 'Event';

        const clientName = b.client_name || 'User';
        const initials = clientName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        
        const avatarHtml = b.client_profile_pic 
            ? `<img src="${b.client_profile_pic}" alt="Client" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">`
            : `<div style="width: 32px; height: 32px; background: #f0f7f3; color: #246A55; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">${initials}</div>`;

        const status = (b.display_status || b.status || 'pending').toLowerCase();
        
        return `
            <tr>
                <td>
                    <div class="client-info" style="display: flex; align-items: center; gap: 12px;">
                        ${avatarHtml}
                        <span style="font-weight: 500; font-size: 13.5px; color: var(--text-main);">${clientName}</span>
                    </div>
                </td>
                <td style="color:var(--text-main); font-weight:500;">${dispTitle}</td>
                <td><span class="admin-badge">${b.organizer_name || 'System'}</span></td>
                <td>
                    <span class="badge ${status}">
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </td>
                <td>
                    <a href="/EventManagementSystem/public/admin/bookings/view?id=${b.id}" style="color: #246A55; font-weight: 700; text-decoration: none; font-size: 13px;">
                        View
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

function renderUpcomingEvents(events) {
    const container = document.getElementById('upcoming-events-list');
    if (!events.length) {
        container.innerHTML = '<p style="text-align:center; color:var(--text-muted); font-size:14px;">No upcoming events.</p>';
        return;
    }

    const today = new Date();
    today.setHours(0,0,0,0);

    container.innerHTML = events.map(e => {
        const eventImg = e.image_path || '/EventManagementSystem/public/assets/images/placeholder.jpg';
        
        let daysText = "Ongoing";
        if (e.event_date) {
            const eDate = new Date(e.event_date);
            eDate.setHours(0,0,0,0);
            const diffTime = eDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) daysText = "Today";
            else if (diffDays > 0) daysText = `in ${diffDays} days`;
        }

        return `
            <a href="/EventManagementSystem/public/admin/events/view?id=${e.id}" class="event-item" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px;">
                <img src="${eventImg}" alt="Event Image" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg'" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                <div class="event-info" style="flex: 1; min-width: 0;">
                    <h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${e.title || 'Event'}
                    </h4>
                    <div class="event-meta" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span class="category" style="white-space: nowrap; background: #f4f7f6; padding: 3px 8px; border-radius: 4px; font-size: 11px; color: var(--text-muted);">${e.category || 'General'}</span>
                        <span class="date" style="white-space: nowrap; color: #e74c3c; font-size: 12px; font-weight: 600; margin-left: auto;">${daysText}</span>
                    </div>
                </div>
                <i class="fas fa-chevron-right" style="color: var(--text-muted); font-size: 14px; flex-shrink: 0;"></i>
            </a>
        `;
    }).join('');
}
