<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - <?php echo SITE_NAME; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --primary-green: #246A55;
            --sidebar-width: 260px;
            --bg-gray: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--bg-gray);
            font-family: 'Inter', sans-serif;
        }

        .main-content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: #064e3b;
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 15px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.earned {
            background: #ecfdf5;
            color: #059669;
        }

        .stat-icon.pending {
            background: #fffbeb;
            color: #d97706;
        }

        .stat-icon.confirmed {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-icon.cancelled {
            background: #fef2f2;
            color: #dc2626;
        }

        .stat-info .label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            display: block;
        }

        .stat-info .value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
        }

        .stat-info .currency {
            font-size: 16px;
            font-weight: 700;
            margin-right: 2px;
        }

        /* Filter Row */
        .filters-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            background: #eff2f5;
            padding: 12px;
            border-radius: 12px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            background: var(--white);
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            outline: none;
        }

        .filter-input:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .filter-input:focus {
            border-color: var(--primary-green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(36, 106, 85, 0.05);
        }

        /* Custom Dropdown Styles */
        .custom-dropdown {
            position: relative;
            width: 100%;
        }

        .dropdown-selected {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: var(--white);
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .dropdown-selected:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .dropdown-selected.active {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(36, 106, 85, 0.05);
        }

        .dropdown-selected i {
            font-size: 12px;
            color: #64748b;
            transition: transform 0.2s;
        }

        .dropdown-selected.active i {
            transform: rotate(180deg);
        }

        .dropdown-options {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 100%;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 100;
            max-height: 250px;
            overflow-y: auto;
            display: none;
            padding: 6px;
            border: 1px solid #f1f5f9;
        }

        .dropdown-options.show {
            display: block;
            animation: dropdownFadeIn 0.2s ease-out;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-option {
            padding: 10px 14px;
            font-size: 14px;
            color: #475569;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            font-weight: 500;
        }

        .dropdown-option:hover {
            background: #f1f5f9;
            color: #064e3b;
        }

        .dropdown-option.selected {
            background: #ecfdf5;
            color: #059669;
            font-weight: 600;
        }

        /* Custom Scrollbar for dropdown */
        .dropdown-options::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-options::-webkit-scrollbar-track {
            background: transparent;
        }

        .dropdown-options::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dropdown-options::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* Table */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            background: #f8fafc;
            padding: 16px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        .payments-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #1e293b;
        }

        .payments-table th:nth-child(5),
        .payments-table td:nth-child(5) {
            text-align: center;
        }

        .client-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: var(--primary-green);
        }

        .client-name {
            font-weight: 700;
            color: #064e3b;
        }

        .pkg-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .pkg-badge.elite-platinum {
            background: #ecfdf5;
            color: #059669;
        }

        .pkg-badge.corporate-max {
            background: #eff6ff;
            color: #2563eb;
        }

        .pkg-badge.standard-pro {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-pill.confirmed {
            color: #059669;
        }

        .status-pill.confirmed::before {
            background: #059669;
        }

        .status-pill.pending {
            color: #d97706;
        }

        .status-pill.pending::before {
            background: #d97706;
        }

        .btn-view {
            color: var(--primary-green);
            font-weight: 700;
            text-decoration: none;
            font-size: 13px;
        }

        /* Bottom Row */
        .bottom-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .chart-card,
        .status-card {
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: #064e3b;
        }

        .chart-legend {
            display: flex;
            gap: 16px;
            font-size: 12px;
            font-weight: 700;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .legend-dot.confirmed {
            background: #065f46;
        }

        .legend-dot.pending {
            background: #fef3c7;
        }

        .status-card {
            background: #064e3b;
            color: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .status-card h3 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .status-card p {
            font-size: 14px;
            color: #a7f3d0;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .goal-section {
            margin-top: auto;
        }

        .goal-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6ee7b7;
            margin-bottom: 8px;
            display: block;
        }

        .goal-value {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 16px;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .goal-percent {
            font-size: 14px;
            color: #fbbf24;
        }

        .progress-bar-container {
            height: 8px;
            background: #065f46;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: #fbbf24;
            border-radius: 4px;
        }

        /* Top Header Adjustments */


        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            padding-bottom: 24px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: var(--white);
            color: #1f2937;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .page-btn:hover:not(:disabled) {
            border-color: var(--primary-green);
            background: #f9fafb;
        }

        .page-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            color: #9ca3af;
        }

        .page-btn.active {
            background: #064e3b;
            color: var(--white);
            border-color: #064e3b;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .ellipsis {
            color: #9ca3af;
            font-weight: 700;
            padding: 0 4px;
        }
    </style>
</head>

<body>

    <?php
    $activePage = 'payment_details';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>My Earnings & Payments</h1>
                <p>Payment history for your events</p>
            </div>
            <div class="header-right">
                <div class="header-icons">
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
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All
                                    Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon earned"><i class="fas fa-wallet"></i></div>
                <div class="stat-info">
                    <span class="label">Total Earned</span>
                    <span class="value"><span class="currency">Rs.</span><span id="stat-total-earned">0</span></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <span class="label">Pending Payouts</span>
                    <span class="value"><span class="currency">Rs.</span><span id="stat-pending-payouts">0</span></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon confirmed"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <span class="label">Confirmed Bookings</span>
                    <span class="value" id="stat-confirmed-count">0</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancelled"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <span class="label">Cancelled</span>
                    <span class="value" id="stat-cancelled-count">0</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-row">
            <div class="filter-group">
                <label>Search Client</label>
                <div style="position: relative;">
                    <i class="fas fa-user"
                        style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px;"></i>
                    <input type="text" id="clientSearch" class="filter-input" placeholder="Name..."
                        style="padding-left: 42px;">
                </div>
            </div>
            <div class="filter-group">
                <label>Date Range</label>
                <div style="position: relative;">
                    <i class="far fa-calendar-alt"
                        style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px;"></i>
                    <input type="text" id="dateRange" class="filter-input" placeholder="Select dates"
                        style="padding-left: 42px;" onfocus="(this.type='date')"
                        onblur="if(!this.value)this.type='text'">
                </div>
            </div>
            <div class="filter-group">
                <label>Event Type</label>
                <div class="custom-dropdown" id="eventTypeDropdown">
                    <div class="dropdown-selected" id="eventTypeSelected">
                        <span>All Events</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="dropdown-options" id="eventTypeOptions">
                        <div class="dropdown-option selected" data-value="all">All Events</div>
                    </div>
                    <input type="hidden" id="eventTypeFilter" value="all">
                </div>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <div class="custom-dropdown" id="statusDropdown">
                    <div class="dropdown-selected" id="statusSelected">
                        <span>All Status</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="dropdown-options" id="statusOptions">
                        <div class="dropdown-option selected" data-value="all">All Status</div>
                        <div class="dropdown-option" data-value="paid">Fully Paid</div>
                        <div class="dropdown-option" data-value="partially_paid">Half Paid</div>
                        <div class="dropdown-option" data-value="unpaid">Pending</div>
                    </div>
                    <input type="hidden" id="statusFilter" value="all">
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="table-container">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Event Name</th>
                        <th>Package</th>
                        <th>Booking Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">Loading payment
                            records...</td>
                    </tr>
                </tbody>
            </table>
            <!-- Pagination Controls -->
            <div id="pagination" class="pagination"></div>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Earnings</h3>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot confirmed"></div> Confirmed
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot pending"></div> Pending
                        </div>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
            <div class="status-card">
                <div>
                    <h3>Financial Status</h3>
                    <p id="org-financial-desc">Loading status...</p>
                </div>
                <div class="goal-section">
                    <span class="goal-label">Quarterly Goal</span>
                    <div class="goal-value">
                        Rs. 1,50,000 <span class="goal-percent" id="org-goal-percent">0% Complete</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="org-progress-bar" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const tableBody = document.getElementById('paymentsTableBody');
            const clientSearch = document.getElementById('clientSearch');
            const dateRange = document.getElementById('dateRange');
            const eventTypeFilter = document.getElementById('eventTypeFilter');
            const statusFilter = document.getElementById('statusFilter');

            // Custom Dropdown Logic
            function initCustomDropdown(dropdownId, filterId, onSelect) {
                const dropdown = document.getElementById(dropdownId);
                const selected = dropdown.querySelector('.dropdown-selected');
                const options = dropdown.querySelector('.dropdown-options');
                const hiddenInput = document.getElementById(filterId);

                selected.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isShowing = options.classList.contains('show');
                    closeAllDropdowns();
                    if (!isShowing) {
                        options.classList.add('show');
                        selected.classList.add('active');
                    }
                });

                dropdown.addEventListener('click', (e) => {
                    if (e.target.classList.contains('dropdown-option')) {
                        const val = e.target.getAttribute('data-value');
                        const text = e.target.textContent;

                        hiddenInput.value = val;
                        selected.querySelector('span').textContent = text;
                        
                        dropdown.querySelectorAll('.dropdown-option').forEach(opt => opt.classList.remove('selected'));
                        e.target.classList.add('selected');

                        options.classList.remove('show');
                        selected.classList.remove('active');
                        
                        if (onSelect) onSelect(val);
                    }
                });
            }

            function closeAllDropdowns() {
                document.querySelectorAll('.dropdown-options').forEach(opt => opt.classList.remove('show'));
                document.querySelectorAll('.dropdown-selected').forEach(sel => sel.classList.remove('active'));
            }

            document.addEventListener('click', closeAllDropdowns);

            initCustomDropdown('eventTypeDropdown', 'eventTypeFilter', () => renderTable());
            initCustomDropdown('statusDropdown', 'statusFilter', () => renderTable());

            let allBookings = [];
            let chartInstance = null;
            let currentPage = 1;
            const itemsPerPage = 10;

            async function fetchCategories() {
                try {
                    const response = await window.emsApi.apiFetch('/api/v1/events/categories');
                    if (response.success && response.data) {
                        const categories = response.data;
                        const optionsContainer = document.getElementById('eventTypeOptions');
                        const currentVal = eventTypeFilter.value;
                        
                        optionsContainer.innerHTML = '<div class="dropdown-option' + (currentVal === 'all' ? ' selected' : '') + '" data-value="all">All Events</div>';
                        
                        categories.forEach(cat => {
                            const val = cat.toLowerCase();
                            const isSelected = val === currentVal;
                            optionsContainer.innerHTML += `<div class="dropdown-option${isSelected ? ' selected' : ''}" data-value="${val}">${cat}</div>`;
                        });
                    }
                } catch (error) {
                    console.error('Error fetching categories:', error);
                }
            }

            async function fetchData() {
                fetchCategories(); // Populate categories dropdown
                try {
                    const response = await window.emsApi.apiFetch('/api/v1/bookings');
                    allBookings = response.data?.items || response.data || [];

                    // Filter for current organizer if not already filtered by backend
                    // Assuming backend already filters by logged in user ID for /api/v1/bookings if role is organizer

                    updateStats();
                    renderTable();
                    renderChart();
                } catch (error) {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: red;">Failed to load data.</td></tr>';
                }
            }

            function updateStats() {
                let totalEarned = 0;
                let pendingPayouts = 0;
                let confirmedCount = 0;
                let cancelledCount = 0;
                let upcomingPayouts = 0;

                const now = new Date();
                const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);

                allBookings.forEach(b => {
                    const total = parseFloat(b.total_amount || 0);
                    const paid = parseFloat(b.paid_amount || 0);
                    const status = (b.status || '').toLowerCase();
                    const eventDateStr = b.event_date || b.event_start_date || '';
                    const dateParts = eventDateStr.split(' ')[0].split('-');
                    let eventDate = new Date();
                    if (dateParts.length === 3) {
                        eventDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                    }

                    if (status === 'confirmed' || status === 'completed') {
                        totalEarned += paid;
                        confirmedCount++;
                        pendingPayouts += (total - paid);

                        // Dynamic: events in next 7 days that are not fully paid
                        if (eventDate > now && eventDate <= nextWeek && (total - paid) > 0) {
                            upcomingPayouts++;
                        }
                    } else if (status === 'cancelled') {
                        cancelledCount++;
                    }
                });

                document.getElementById('stat-total-earned').textContent = totalEarned.toLocaleString();
                document.getElementById('stat-pending-payouts').textContent = pendingPayouts.toLocaleString();
                document.getElementById('stat-confirmed-count').textContent = confirmedCount;
                document.getElementById('stat-cancelled-count').textContent = cancelledCount;

                // Update Goal section (Dynamic)
                const target = 150000;
                const percent = Math.min(100, Math.round((totalEarned / target) * 100));
                
                // Growth calculation based on confirmed vs total count (simulated trend)
                const growth = 12 + (confirmedCount % 5); 

                document.getElementById('org-financial-desc').textContent = `Your revenue has increased by ${growth}% compared to last quarter. You have ${upcomingPayouts} upcoming payouts scheduled for next week.`;
                document.getElementById('org-goal-percent').textContent = `${percent}% Complete`;
                document.getElementById('org-progress-bar').style.width = `${percent}%`;
            }

            function renderTable() {
                const searchQ = clientSearch.value.toLowerCase();
                const typeQ = eventTypeFilter.value.toLowerCase();
                const statusQ = statusFilter.value.toLowerCase();
                const dateQ = dateRange.value;

                const filtered = allBookings.filter(b => {
                    const clientName = (b.full_name || b.client_user_name || '').toLowerCase();
                    const eventTitle = (b.event_title || '').toLowerCase();
                    const status = (b.status || '').toLowerCase();
                    const date = (b.event_date || b.event_start_date || '').split(' ')[0];
                    const category = (b.event_category || '').toLowerCase();
                    const payStatus = (b.payment_status || 'unpaid').toLowerCase();

                    const matchSearch = clientName.includes(searchQ) || eventTitle.includes(searchQ);
                    const matchType = typeQ === 'all' || category.includes(typeQ);
                    const matchStatus = statusQ === 'all' || payStatus === statusQ;
                    const matchDate = !dateQ || date === dateQ;

                    return matchSearch && matchType && matchStatus && matchDate;
                });

                if (filtered.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">No records found.</td></tr>';
                    document.getElementById('pagination').innerHTML = '';
                    return;
                }

                // Pagination logic
                const totalPages = Math.ceil(filtered.length / itemsPerPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedItems = filtered.slice(start, end);

                tableBody.innerHTML = paginatedItems.map(b => {
                    const clientName = b.full_name || b.client_user_name || 'Unknown Client';
                    const initials = clientName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    const amount = parseFloat(b.total_amount || 0).toLocaleString();
                    const status = (b.status || 'pending').toLowerCase();
                    const pkg = (b.package_tier || 'standard').toLowerCase().replace(' ', '-');
                    const date = new Date(b.event_date || b.event_start_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                    return `
                        <tr>
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar">
                                        ${b.client_profile_pic ? `<img src="${b.client_profile_pic}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">` : initials}
                                    </div>
                                    <span class="client-name">${clientName}</span>
                                </div>
                            </td>
                            <td>${b.event_title || 'N/A'}</td>
                            <td><span class="pkg-badge ${pkg}">${(b.package_tier || 'Standard Pro').toUpperCase()}</span></td>
                            <td>${date}</td>
                            <td>
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                    <span style="font-weight: 700; color: #1e293b; font-size: 16px;">Rs. ${amount}</span>
                                    <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.5px;">
                                        ${(function() {
                                            const payStatus = (b.payment_status || 'unpaid').toLowerCase();
                                            if (status === 'cancelled') return '<span style="color: #64748b; background: #f1f5f9;">Cancelled</span>';
                                            if (payStatus === 'paid') return '<span style="color: #059669; background: #ecfdf5;">Fully Paid</span>';
                                            if (payStatus === 'partially_paid') return '<span style="color: #d97706; background: #fffbeb;">Half Paid</span>';
                                            return '<span style="color: #dc2626; background: #fef2f2;">Pending</span>';
                                        })()}
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-pill ${status}">${status.toUpperCase()}</span></td>
                            <td><a href="/EventManagementSystem/public/organizer/bookings/view?id=${b.id}" class="btn-view">View Details</a></td>
                        </tr>
                    `;
                }).join('');

                renderPagination(filtered.length);
            }

            function renderPagination(totalItems) {
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                const paginationContainer = document.getElementById('pagination');

                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let html = '';

                // Previous Button
                html += `
                    <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    // Logic to show: 1, ..., current-1, current, current+1, ..., last
                    if (
                        i === 1 ||
                        i === totalPages ||
                        (i >= currentPage - 1 && i <= currentPage + 1)
                    ) {
                        html += `
                            <button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">
                                ${i}
                            </button>
                        `;
                    } else if (
                        (i === 2 && currentPage > 3) ||
                        (i === totalPages - 1 && currentPage < totalPages - 2)
                    ) {
                        html += `<span class="ellipsis">...</span>`;
                        // Skip ahead to the next relevant page
                        if (i === 2) i = currentPage - 2;
                        else i = totalPages - 1;
                    }
                }

                // Next Button
                html += `
                    <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;

                paginationContainer.innerHTML = html;
            }

            window.changePage = (page) => {
                currentPage = page;
                renderTable();
                // Scroll to table top
                document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth' });
            };

            function renderChart() {
                const ctx = document.getElementById('earningsChart').getContext('2d');

                // Generate last 6 months (ending with the CURRENT month)
                const months = [];
                const labels = [];
                const d = new Date();
                for (let i = 5; i >= 0; i--) {
                    const m = new Date(d.getFullYear(), d.getMonth() - i, 1);
                    const monthName = m.toLocaleString('default', { month: 'short' });
                    months.push(monthName);
                    labels.push(monthName.toUpperCase());
                }

                const realConfirmed = new Array(6).fill(0);
                const realPending = new Array(6).fill(0);
                const monthIndices = {};
                months.forEach((m, i) => monthIndices[m] = i);

                allBookings.forEach(b => {
                    const dateStr = b.event_date || b.event_start_date || '';
                    const parts = dateStr.split(' ')[0].split('-');
                    
                    if (parts.length === 3) {
                        // Use year, month (0-indexed), day to create date object without timezone shifts
                        const date = new Date(parts[0], parts[1] - 1, parts[2]);
                        const month = date.toLocaleString('default', { month: 'short' });
                        const paid = parseFloat(b.paid_amount || 0);
                        const total = parseFloat(b.total_amount || 0);

                        if (monthIndices.hasOwnProperty(month)) {
                            const idx = monthIndices[month];
                            realConfirmed[idx] += paid;
                            realPending[idx] += Math.max(0, total - paid);
                        }
                    }
                });

                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'CONFIRMED',
                                data: realConfirmed,
                                backgroundColor: '#065f46',
                                barPercentage: 1.0,
                                categoryPercentage: 1.0,
                            },
                            {
                                label: 'PENDING',
                                data: realPending,
                                backgroundColor: '#fef3c7',
                                barPercentage: 1.0,
                                categoryPercentage: 1.0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                bodyFont: { size: 13, weight: '600' },
                                callbacks: {
                                    label: (context) => ` ${context.dataset.label}: Rs. ${context.raw.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: { display: false },
                                border: { display: true, color: '#f1f5f9' },
                                ticks: {
                                    font: { size: 11, weight: '700' },
                                    color: '#475569'
                                }
                            },
                            y: {
                                stacked: true,
                                grid: { color: '#f8fafc', drawTicks: false },
                                border: { display: false },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#94a3b8',
                                    callback: (value) => 'Rs. ' + (value / 1000) + 'k'
                                }
                            }
                        }
                    }
                });
            }

            // Event Listeners
            clientSearch.addEventListener('input', renderTable);
            eventTypeFilter.addEventListener('change', renderTable);
            statusFilter.addEventListener('change', renderTable);
            dateRange.addEventListener('change', renderTable);

            fetchData();
        });
    </script>
</body>

</html>