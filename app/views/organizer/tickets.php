<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - <?php echo SITE_NAME; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
    <style>
        .tier-concert-av { background: #E0F2FE; color: #0369A1; }
        .pkg-badge.concert { background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; }
    </style>
</head>

<body>

    <?php
    $activePage = 'tickets';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header b-header">
            <div class="header-left b-header-left">
                <h1>Concert Tickets</h1>
                <p>Manage and review all concert ticket bookings</p>
            </div>

            <div class="header-right b-header-right">
                <div class="search-wrap top-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="topSearchInput" placeholder="Search tickets...">
                </div>
                <div class="header-actions">
                    <div class="notifications-wrapper">
                        <div class="notification-bell-btn" id="notification-bell">
                            <i class="fa-regular fa-bell"></i>
                            <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                        </div>
                        <!-- Notifications Dropdown -->
                        <div class="notifications-dropdown" id="notifications-dropdown">
                            <div class="nd-header">
                                <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 New</span></h3>
                                <a href="javascript:void(0)" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="nd-content" id="nd-list">
                                <div class="nd-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    <p>No new notifications</p>
                                </div>
                            </div>
                            <div class="nd-footer">
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="user-profile-info">
                        <div class="user-avatar-small">
                            <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-row">
            <div class="stat-box">
                <p class="stat-title">Total Tickets</p>
                <h2 class="stat-number dark" id="stat-total-bookings">0</h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Confirmed</p>
                <h2 class="stat-number green" id="stat-confirmed">0</h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Pending</p>
                <h2 class="stat-number orange" id="stat-pending">0</h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Cancelled</p>
                <h2 class="stat-number red" id="stat-cancelled">0</h2>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="filters-row">
            <div class="search-wrap bottom-search">
                <i class="fas fa-search"></i>
                <input type="text" id="filterSearchInput" placeholder="Filter by attendee or concert...">
            </div>

            <!-- Custom Tier Dropdown -->
            <div class="custom-premium-dropdown" id="packageDropdown">
                <div class="dropdown-trigger">
                    <span class="selected-val">All Tiers</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-item active" data-value="all">All Tiers</div>
                    <div class="dropdown-item" data-value="basic">Basic</div>
                    <div class="dropdown-item" data-value="standard">Standard</div>
                    <div class="dropdown-item" data-value="premium">Premium</div>
                </div>
            </div>

            <div class="custom-date">
                <i class="fa-regular fa-calendar"></i>
                <input type="text" placeholder="Select Date" id="dateFilter" onfocus="(this.type='date')"
                    onblur="if(!this.value)this.type='text'">
            </div>
        </div>

        <!-- Secondary Status Tabs -->
        <div class="status-tabs">
            <button class="status-tab active" data-status="all">All <span
                    class="badge" id="tab-badge-all">0</span></button>
            <button class="status-tab" data-status="pending">Pending <span
                    class="badge" id="tab-badge-pending">0</span></button>
            <button class="status-tab" data-status="confirmed">Confirmed <span
                    class="badge" id="tab-badge-confirmed">0</span></button>
            <button class="status-tab" data-status="completed">Completed <span
                    class="badge" id="tab-badge-completed">0</span></button>
            <button class="status-tab" data-status="cancelled">Cancelled <span
                    class="badge" id="tab-badge-cancelled">0</span></button>
        </div>

        <!-- Bookings List / Table -->
        <div class="bookings-list-container">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">ATTENDEE</th>
                        <th>CONCERT</th>
                        <th>TIER</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th style="text-align:right; padding-right:24px;">ACTION</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <tr>
                        <td colspan="7" class="no-data">Loading tickets...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-row">
                <div class="showing-text">
                    Showing <span id="visibleCount">0</span> of <span
                        id="totalBookingsSpan">0</span> tickets
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- Pagination buttons injected via JS -->
                </div>
            </div>
        </div>

    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const topSearch = document.getElementById('topSearchInput');
            const filterSearch = document.getElementById('filterSearchInput');
            const dateFilter = document.getElementById('dateFilter');
            const statusTabs = document.querySelectorAll('.status-tab');
            const visibleCountLabel = document.getElementById('visibleCount');
            const totalBookingsSpan = document.getElementById('totalBookingsSpan');
            const paginationControls = document.getElementById('paginationControls');
            const tbody = document.getElementById('bookingsTableBody');

            let currentStatus = 'all';
            let currentCategory = 'concert'; // Fixed to concert
            let currentPackage = 'all';
            let currentPage = 1;
            const itemsPerPage = 8;
            let allBookings = [];
            let filteredBookings = [];

            async function fetchBookings() {
                if (!window.emsApi) return;
                try {
                    const res = await window.emsApi.apiFetch('/api/v1/bookings');
                    allBookings = (res.data?.items || res.data || []).filter(b => {
                        const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
                        const cat = (eSnap?.category || b.event_category || '').toLowerCase().trim();
                        return cat === 'concert';
                    });
                    applyFilters();
                } catch (err) {
                    console.error('Failed to fetch tickets:', err);
                    tbody.innerHTML = `<tr><td colspan="7" class="no-data" style="color:red;">Error loading tickets: ${err.message}</td></tr>`;
                }
            }

            function applyFilters() {
                const searchQ = filterSearch.value.toLowerCase();
                const dtFilter = dateFilter.value;
                const today = new Date();
                today.setHours(0,0,0,0);

                let confirmedCount = 0, pendingCount = 0, cancelledCount = 0, completedCount = 0;

                filteredBookings = allBookings.filter(b => {
                    const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
                    const eventTitle = (eSnap?.title || b.event_title || '').toLowerCase();
                    const clientName = (b.full_name || b.client_user_name || '').toLowerCase();
                    const pkg = (b.package_tier || '').toLowerCase().trim();
                    const dateStr = (b.event_date || b.event_start_date || '').split(' ')[0];
                    
                    let status = (b.status || '').toLowerCase().trim();
                    const bDate = new Date(dateStr);
                    if (status === 'confirmed' && bDate < today) status = 'completed';

                    const matchSearch = clientName.includes(searchQ) || eventTitle.includes(searchQ);
                    const matchPkg = currentPackage === 'all' || pkg === currentPackage;
                    const matchDate = dtFilter === '' || dateStr === dtFilter;
                    const matchStatus = currentStatus === 'all' || status === currentStatus;

                    if (status === 'confirmed') confirmedCount++;
                    else if (status === 'pending') pendingCount++;
                    else if (status === 'cancelled') cancelledCount++;
                    else if (status === 'completed') completedCount++;

                    return matchSearch && matchPkg && matchDate && matchStatus;
                });

                document.getElementById('stat-total-bookings').textContent = allBookings.length.toLocaleString();
                document.getElementById('stat-confirmed').textContent = allBookings.filter(b => b.status === 'confirmed').length.toLocaleString();
                document.getElementById('stat-pending').textContent = allBookings.filter(b => b.status === 'pending').length.toLocaleString();
                document.getElementById('stat-cancelled').textContent = allBookings.filter(b => b.status === 'cancelled').length.toLocaleString();

                document.getElementById('tab-badge-all').textContent = allBookings.length.toLocaleString();
                document.getElementById('tab-badge-pending').textContent = pendingCount.toLocaleString();
                document.getElementById('tab-badge-confirmed').textContent = confirmedCount.toLocaleString();
                document.getElementById('tab-badge-completed').textContent = completedCount.toLocaleString();
                document.getElementById('tab-badge-cancelled').textContent = cancelledCount.toLocaleString();

                visibleCountLabel.textContent = filteredBookings.length;
                totalBookingsSpan.textContent = allBookings.length;
                
                currentPage = 1;
                renderTable();
            }

            function renderTable() {
                if (filteredBookings.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="no-data">No tickets found.</td></tr>`;
                    paginationControls.innerHTML = '';
                    return;
                }

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredBookings.slice(start, end);
                const today = new Date();
                today.setHours(0,0,0,0);

                tbody.innerHTML = pageItems.map(b => {
                    const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
                    const eventTitle = eSnap?.title || b.event_title || '';
                    const clientName = b.full_name || b.client_user_name || 'Attendee';
                    
                    let initials = clientName.substring(0,2).toUpperCase();
                    if (clientName.includes(' ')) {
                        const parts = clientName.split(' ');
                        initials = (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
                    }
                    
                    const dateObj = new Date(b.event_date || b.event_start_date);
                    const shortDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const year = dateObj.getFullYear();
                    
                    let status = (b.status || '').toLowerCase();
                    if (status === 'confirmed' && dateObj < today) status = 'completed';

                    const packageClass = (b.package_tier || '').toLowerCase();
                    const avatar = b.client_profile_pic 
                        ? `<img src="${b.client_profile_pic}" alt="Client" style="width: 100%; height: 100%; object-fit: cover;">`
                        : initials;
                    
                    const amt = parseFloat(b.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    let actionHtml = `<a href="/EventManagementSystem/public/organizer/bookings/view?id=${b.id}&source=tickets" class="btn-view">View</a>`;
                    if (status === 'pending') {
                        actionHtml = `
                            <a href="/EventManagementSystem/public/organizer/bookings/view?id=${b.id}&source=tickets" class="btn-view">View Details</a>
                        `;
                    }

                    return `
                        <tr class="booking-row">
                            <td style="padding-left:24px;">
                                <div class="client-cell" style="display: flex; align-items: center; gap: 12px;">
                                    <div class="client-avatar ${packageClass}-av" style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                                        ${avatar}
                                    </div>
                                    <span class="client-name" style="font-weight: 600; color: #111827;">${clientName}</span>
                                </div>
                            </td>
                            <td><div class="event-title-cell">${eventTitle}</div></td>
                            <td><span class="pkg-badge ${packageClass}">${(b.package_tier || '').toUpperCase()}</span></td>
                            <td><div class="date-cell"><span class="m-d">${shortDate}</span><span class="year">${year}</span></div></td>
                            <td><div class="amount-cell"><span class="curr">Rs.</span><span class="val">${amt}</span></div></td>
                            <td><div class="status-cell"><span class="dot ${status}"></span><span class="st-text ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></div></td>
                            <td style="text-align:right; padding-right:24px;"><div class="action-cell">${actionHtml}</div></td>
                        </tr>
                    `;
                }).join('');

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(filteredBookings.length / itemsPerPage);
                if (totalPages <= 1) { paginationControls.innerHTML = ''; return; }
                let html = `<button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
                for (let i = 1; i <= totalPages; i++) {
                    html += `<button class="p-btn num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                }
                html += `<button class="p-btn next ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
                paginationControls.innerHTML = html;
            }

            window.changePage = (p) => {
                const totalPages = Math.ceil(filteredBookings.length / itemsPerPage);
                if (p < 1 || p > totalPages) return;
                currentPage = p;
                renderTable();
            };

            window.approveBooking = async (id, btn) => {
                if (!confirm('Approve this ticket?')) return;
                try {
                    await window.emsApi.apiFetch(`/api/v1/bookings/${id}/approve`, { method: 'PATCH' });
                    fetchBookings();
                } catch (err) { alert('Error: ' + err.message); }
            };

            topSearch.addEventListener('input', function () { filterSearch.value = this.value; applyFilters(); });
            filterSearch.addEventListener('input', function () { topSearch.value = this.value; applyFilters(); });
            dateFilter.addEventListener('change', applyFilters);
            statusTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    statusTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentStatus = this.dataset.status;
                    applyFilters();
                });
            });

            document.querySelectorAll('#packageDropdown .dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    currentPackage = (this.dataset.value || 'all').toLowerCase().trim();
                    applyFilters();
                });
            });

            fetchBookings();
        });
    </script>
</body>

</html>
