/**
 * Admin Bookings Management JS
 */

let allBookings = [];
let filteredBookings = [];
let currentStatus = 'all';
let currentCategory = 'all';
let currentPackage = 'all';
let currentPage = 1;
const itemsPerPage = 8;

document.addEventListener('DOMContentLoaded', () => {
    // Check for search param in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    
    const globalSearch = document.getElementById('globalSearchInput');
    const filterSearch = document.getElementById('filterSearchInput');
    
    if (searchParam) {
        if (globalSearch) globalSearch.value = searchParam;
        if (filterSearch) filterSearch.value = searchParam;
    }

    loadBookings(searchParam || '');
    initFilters();
});

async function loadBookings(initialSearch = '') {
    if (!window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/bookings');
        if (res.success) {
            const rawItems = res.data.items || [];
            // Exclude concerts from regular bookings
            allBookings = rawItems.filter(b => {
                const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
                const cat = (eSnap?.category || b.event_category || '').toLowerCase().trim();
                return cat !== 'concert';
            });
            updateStats();
            applyFilters(initialSearch);
        }
    } catch (err) {
        console.error('Failed to load bookings:', err);
    }
}

function updateStats() {
    const total = allBookings.length;
    const pending = allBookings.filter(b => (b.display_status || b.status) === 'pending').length;
    const confirmed = allBookings.filter(b => (b.display_status || b.status) === 'confirmed').length;
    const completed = allBookings.filter(b => (b.display_status || b.status) === 'completed').length;
    const cancelled = allBookings.filter(b => (b.display_status || b.status) === 'cancelled').length;

    // Stats Cards
    const elTotal = document.getElementById('stat-total-bookings');
    const elPending = document.getElementById('stat-pending-count');
    const elConfirmed = document.getElementById('stat-confirmed-count');
    const elCancelled = document.getElementById('stat-cancelled-count');

    if (elTotal) elTotal.innerText = total.toLocaleString();
    if (elPending) elPending.innerText = pending.toLocaleString();
    if (elConfirmed) elConfirmed.innerText = confirmed.toLocaleString();
    if (elCancelled) elCancelled.innerText = cancelled.toLocaleString();

    // Tabs
    const tAll = document.getElementById('tab-all-count');
    const tPending = document.getElementById('tab-pending-count');
    const tConfirmed = document.getElementById('tab-confirmed-count');
    const tCompleted = document.getElementById('tab-completed-count');
    const tCancelled = document.getElementById('tab-cancelled-count');

    if (tAll) tAll.innerText = total;
    if (tPending) tPending.innerText = pending;
    if (tConfirmed) tConfirmed.innerText = confirmed;
    if (tCompleted) tCompleted.innerText = completed;
    if (tCancelled) tCancelled.innerText = cancelled;
}

function initFilters() {
    const globalSearch = document.getElementById('globalSearchInput');
    const filterSearch = document.getElementById('filterSearchInput');
    const dateFilter = document.getElementById('dateFilter');
    const statusTabs = document.querySelectorAll('.status-tab');

    if (globalSearch && filterSearch) {
        globalSearch.addEventListener('input', (e) => {
            filterSearch.value = e.target.value;
            currentPage = 1;
            applyFilters();
        });
        filterSearch.addEventListener('input', (e) => {
            globalSearch.value = e.target.value;
            currentPage = 1;
            applyFilters();
        });
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', () => {
            currentPage = 1;
            applyFilters();
        });
    }

    statusTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            statusTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.dataset.status;
            currentPage = 1;
            applyFilters();
        });
    });

    // Handle Custom Dropdown Changes
    const eventDD = document.getElementById('eventDropdown');
    if (eventDD) {
        eventDD.addEventListener('change', (e) => {
            currentCategory = e.detail.value;
            currentPage = 1;
            applyFilters();
        });
    }

    const packageDD = document.getElementById('packageDropdown');
    if (packageDD) {
        packageDD.addEventListener('change', (e) => {
            currentPackage = e.detail.value;
            currentPage = 1;
            applyFilters();
        });
    }
}

function applyFilters(searchQ = null) {
    if (searchQ === null) {
        searchQ = (document.getElementById('filterSearchInput')?.value || '').toLowerCase();
    } else {
        searchQ = searchQ.toLowerCase();
    }
    const dtFilter = document.getElementById('dateFilter')?.value || '';

    filteredBookings = allBookings.filter(b => {
        const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
        const eTitle = eSnap?.title || b.event_title || '';
        const eCat = eSnap?.category || b.event_category || '';

        const client = (b.full_name || b.client_user_name || '').toLowerCase();
        const event = (eTitle).toLowerCase();
        const organizer = (b.organizer_name || '').toLowerCase();
        const category = (eCat).toLowerCase();
        const pkg = (b.package_tier || '').toLowerCase();
        const status = (b.display_status || b.status || '').toLowerCase();
        const bDate = (b.event_date || b.event_start_date || '').split(' ')[0];

        const matchSearch = client.includes(searchQ) || event.includes(searchQ) || organizer.includes(searchQ);
        const matchCategory = currentCategory === 'all' || category === currentCategory.toLowerCase();
        const matchPkg = currentPackage === 'all' || pkg === currentPackage.toLowerCase();
        const matchDate = dtFilter === '' || bDate === dtFilter;
        const matchStatus = currentStatus === 'all' || status === currentStatus;

        return matchSearch && matchCategory && matchPkg && matchDate && matchStatus;
    });

    renderTable();
}

function renderTable() {
    const tbody = document.getElementById('bookingsTableBody');
    const totalSpan = document.getElementById('totalBookingsSpan');
    const visibleCount = document.getElementById('visibleCount');

    if (totalSpan) totalSpan.innerText = allBookings.length.toLocaleString();
    if (visibleCount) visibleCount.innerText = filteredBookings.length.toLocaleString();

    if (!filteredBookings.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data">No bookings found matching your criteria.</td></tr>';
        renderPagination(0);
        return;
    }

    const totalPages = Math.ceil(filteredBookings.length / itemsPerPage);
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = filteredBookings.slice(start, end);

    tbody.innerHTML = pageItems.map(b => {
        const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
        const dispTitle = eSnap?.title || b.event_title || 'Untitled Event';

        const clientName = b.full_name || b.client_user_name || 'User';
        const initials = clientName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        const pkgClass = (b.package_tier || 'basic').toLowerCase();
        const status = (b.display_status || b.status || 'pending').toLowerCase();
        const bDate = new Date(b.event_date || b.event_start_date);
        
        const avatarHtml = b.client_profile_pic 
            ? `<img src="${b.client_profile_pic}" alt="Client" class="client-avatar-img" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-right: 12px;">`
            : `<div class="client-avatar ${pkgClass}-av" style="margin-right: 12px;">${initials}</div>`;

        const pStat = (b.payment_status || 'unpaid').toLowerCase();
        const canApprove = (status === 'pending' && (pStat === 'paid' || pStat === 'partially_paid'));

        return `
            <tr class="booking-row">
                <td style="padding-left:24px;">
                    <div class="client-cell">
                        ${avatarHtml}
                        <span class="client-name">${clientName}</span>
                    </div>
                </td>
                <td>
                    <div class="event-title-cell">${dispTitle}</div>
                    <span class="organizer-info"><i class="fas fa-user-tie"></i> ${b.organizer_name || 'System'}</span>
                </td>
                <td>
                    <span class="pkg-badge ${pkgClass}">${(b.package_tier || 'BASIC').toUpperCase()}</span>
                </td>
                <td>
                    <div class="date-cell">
                        <span class="m-d">${bDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</span>
                        <span class="year">${bDate.getFullYear()}</span>
                    </div>
                </td>
                <td>
                    <div class="amount-cell">
                        <span class="curr">Rs.</span>
                        <span class="val">${parseFloat(b.total_amount || 0).toLocaleString()}</span>
                    </div>
                </td>
                <td>
                    <div class="status-cell">
                        <span class="dot ${status}"></span>
                        <span class="st-text ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                    </div>
                </td>
                <td style="text-align:right; padding-right:24px;">
                    <div class="action-cell">
                        ${status === 'pending' ? `
                            <button type="button" class="btn-approve" 
                                ${!canApprove ? 'disabled style="opacity: 0.5; cursor: not-allowed;" title="Wait for 50% advance payment"' : `onclick="approveBooking(${b.id})"`}>
                                Approve
                            </button>
                            <a href="/EventManagementSystem/public/admin/bookings/view?id=${b.id}" class="btn-view secondary">View</a>
                        ` : `
                            <a href="/EventManagementSystem/public/admin/bookings/view?id=${b.id}" class="btn-view">View</a>
                        `}
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const container = document.getElementById('paginationControls');
    if (!container) return;

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `<button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;

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
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    renderTable();
}

async function approveBooking(id) {
    if (!confirm('Approve this booking?')) return;
    try {
        const res = await window.emsApi.apiFetch(`/api/v1/bookings/${id}/approve`, { method: 'PATCH' });
        if (res.success) {
            loadBookings(); // Refresh
        } else {
            alert(res.message || 'Failed to approve booking.');
        }
    } catch (err) {
        console.error('Error approving booking:', err);
    }
}
