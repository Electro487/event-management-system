<?php
/** @var array $payments @var float $totalSpent @var int $confirmedBookingsCount @var int $pendingPaymentsCount */

$title = 'My Payment History - e-Plan';
$activePage = 'payments';

ob_start();
?>
    <style>
        :root {
            --primary-green: #246A55;
            --bg-gray: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--bg-gray);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .history-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header-section {
            margin-bottom: 40px;
        }

        .header-section h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .header-section p {
            color: var(--text-muted);
            font-size: 16px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-card.total .stat-icon {
            background: #f0fdf4;
            color: var(--primary-green);
        }

        .stat-card.confirmed .stat-icon {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-card.pending .stat-icon {
            background: #fffbeb;
            color: #d97706;
        }

        .stat-info .label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            display: block;
        }

        .stat-info .value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
        }

        /* Filter Bar */
        .filter-bar {
            background: var(--white);
            padding: 16px 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-box input {
            width: 92%;
            padding: 12px 16px 12px 48px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            outline: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .search-box input:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(36, 106, 85, 0.08);
        }

        .filter-btn {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: var(--white);
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .filter-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* Custom Dropdown */
        .custom-dropdown {
            position: relative;
            min-width: 180px;
        }

        .cd-trigger {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: var(--white);
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cd-trigger i {
            font-size: 12px;
            color: #64748b;
        }

        .custom-dropdown.open .cd-trigger {
            border-color: var(--primary-green);
        }

        .cd-options {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 100%;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
        }

        .custom-dropdown.open .cd-options {
            opacity: 1;
            visibility: visible;
        }

        .cd-option {
            padding: 12px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .cd-option:hover { background: #f8fafc; }
        .cd-option.active { color: var(--primary-green); font-weight: 700; background: #f0fdf4; }

        /* Payment List & Items */
        .payment-list { display: flex; flex-direction: column; gap: 20px; }
        .payment-item { background: var(--white); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 24px; box-shadow: var(--shadow-sm); }
        .event-img-container { width: 140px; height: 140px; border-radius: 12px; overflow: hidden; }
        .event-img { width: 100%; height: 100%; object-fit: cover; }
        .payment-details { flex: 1; }
        .status-paid { background: #DCFCE7; color: #166534; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-partial { background: #E0F2FE; color: #0369A1; }

        .event-title { 
            font-size: 18px; 
            font-weight: 700; 
            color: var(--text-main); 
            text-decoration: none; 
            transition: color 0.2s;
            margin-bottom: 8px;
            display: inline-block;
        }
        .event-title:hover { color: var(--primary-green); }
        .event-title:visited { color: var(--text-main); }

        .event-title-row { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }

        .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .meta-col .label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; display: block; }
        .meta-col .val { font-size: 14px; font-weight: 500; }
        .amount-col { text-align: right; min-width: 180px; }
        .amount-val { font-size: 26px; font-weight: 800; display: block; margin-bottom: 8px; }

        .status-pill { display: inline-flex; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .status-paid { background: #DCFCE7; color: #166534; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-partial { background: #E0F2FE; color: #0369A1; }

        .tier-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .tier-premium { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
        .tier-standard { background: #F0FDF4; color: #15803D; border: 1px solid #DCFCE7; }
        .tier-basic { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }

        .ticket-badge {
            background: #EEF2FF;
            color: #4338CA;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            border: 1px solid #E0E7FF;
        }

        .actions-col { display: flex; flex-direction: column; gap: 10px; min-width: 120px; }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            text-align: center;
        }
        .btn-receipt {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }
        .btn-receipt:hover {
            background: #E2E8F0;
            color: #1E293B;
        }

        /* Pagination */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 40px; padding-bottom: 40px; }
        .page-btn { padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: var(--white); cursor: pointer; }
        .page-info { font-size: 14px; color: var(--text-muted); margin: 0 12px; }
    </style>
<?php
$extra_head = ob_get_clean();
include 'partials/header.php';
?>

    <div class="history-wrapper">
        <div class="header-section">
            <h1>My Payment History</h1>
            <p>All your bookings and payment records in one place.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid" id="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <span class="label">Total Spent</span>
                    <span class="value" id="stat-total-spent">Rs. 0</span>
                </div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-info">
                    <span class="label">Confirmed Bookings</span>
                    <span class="value" id="stat-confirmed">0</span>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="stat-info">
                    <span class="label">Pending Amount</span>
                    <span class="value" id="stat-pending">Rs. 0</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" placeholder="Search by event name...">
            </div>
            <div class="filter-btn" id="date-btn-trigger" style="margin-left: auto;">
                <i class="fa-regular fa-calendar"></i> <span id="selected-date-label">Select Date</span>
                <input type="date" id="date-filter"
                    style="position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px;">
            </div>
            <div class="custom-dropdown" id="status-dropdown">
                <div class="cd-trigger">
                    <span id="selected-status">All Status</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="cd-options">
                    <div class="cd-option active" data-value="all">All Status</div>
                    <div class="cd-option" data-value="paid">Fully Paid</div>
                    <div class="cd-option" data-value="partial">Half Paid</div>
                    <div class="cd-option" data-value="pending">Pending</div>
                </div>
            </div>
        </div>

        <!-- Payment List -->
        <div class="payment-list" id="payment-list">
            <!-- Skeleton items -->
            <div class="payment-item">
                <div class="skeleton-img skeleton"></div>
                <div style="flex: 1;">
                    <div class="skeleton-text skeleton" style="width: 60%;"></div>
                    <div class="skeleton-text skeleton" style="width: 40%;"></div>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination" class="pagination"></div>
    </div>

<script>
        let allPayments = [];
        let currentStatus = 'all';
        let currentPage = 1;
        const itemsPerPage = 10;

        document.addEventListener('DOMContentLoaded', () => {
            // Custom Dropdown Logic
            const dropdown = document.getElementById('status-dropdown');
            const trigger = dropdown.querySelector('.cd-trigger');
            const options = dropdown.querySelectorAll('.cd-option');
            const selectedLabel = document.getElementById('selected-status');

            trigger.addEventListener('click', () => {
                dropdown.classList.toggle('open');
            });

            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    const val = opt.getAttribute('data-value');
                    currentStatus = val;
                    selectedLabel.innerText = opt.innerText;

                    options.forEach(o => o.classList.remove('active'));
                    opt.classList.add('active');

                    dropdown.classList.remove('open');
                    applyFilters();
                });
            });

            // Close on click outside
            window.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });

            // Search Input Logic
            document.getElementById('search-input').addEventListener('input', applyFilters);

            // Date Filter Logic
            const dateBtn = document.getElementById('date-btn-trigger');
            const dateInput = document.getElementById('date-filter');

            dateBtn.addEventListener('click', () => {
                try {
                    // Modern browsers
                    dateInput.showPicker();
                } catch (e) {
                    // Fallback for older browsers
                    dateInput.click();
                }
            });

            dateInput.addEventListener('change', function (e) {
                const val = e.target.value;
                const label = document.getElementById('selected-date-label');
                if (val) {
                    const date = new Date(val);
                    label.innerText = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                } else {
                    label.innerText = 'Select Date';
                }
                applyFilters();
            });

            loadPaymentHistory();
        });

        async function loadPaymentHistory() {
            try {
                const response = await window.emsApi.apiFetch('/api/v1/payments/full-history');
                if (response.success) {
                    allPayments = response.data.payments || [];
                    filteredPayments = allPayments; // Initialize filtered list
                    renderStats(response.data.stats);
                    renderPayments(allPayments);
                }
            } catch (error) {
                console.error('Error loading history:', error);
                document.getElementById('payment-list').innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-circle-exclamation" style="color: #DC2626;"></i>
                        <h3>Error loading data</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        function renderStats(stats) {
            document.getElementById('stat-total-spent').innerText = 'Rs. ' + (stats.total_spent || 0).toLocaleString();
            document.getElementById('stat-confirmed').innerText = stats.confirmed_bookings || 0;
            document.getElementById('stat-pending').innerText = 'Rs. ' + (stats.pending_amount || 0).toLocaleString();
        }

        let filteredPayments = [];

        function applyFilters() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const status = currentStatus;
            const selectedDate = document.getElementById('date-filter').value; // YYYY-MM-DD

            filteredPayments = allPayments.filter(p => {
                const eSnap = p.event_snapshot ? JSON.parse(p.event_snapshot) : {};
                const title = (eSnap.title || p.live_title || '').toLowerCase();
                const matchesQuery = title.includes(query);
                const matchesStatus = status === 'all' || p.ui_status === status;

                // Date match logic
                let matchesDate = true;
                if (selectedDate) {
                    const eventDate = p.booking_event_date || eSnap.event_date || p.live_date;
                    // match YYYY-MM-DD
                    if (eventDate) {
                        matchesDate = eventDate.startsWith(selectedDate);
                    } else {
                        matchesDate = false;
                    }
                }

                return matchesQuery && matchesStatus && matchesDate;
            });

            currentPage = 1; // Reset to first page on filter change
            renderPayments(filteredPayments);
        }

        function renderPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const paginationContainer = document.getElementById('pagination');

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `
                <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class="fa-solid fa-chevron-left"></i> Previous
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `
                        <button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">
                            ${i}
                        </button>
                    `;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="page-info">...</span>`;
                }
            }

            html += `
                <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    Next <i class="fa-solid fa-chevron-right"></i>
                </button>
            `;

            paginationContainer.innerHTML = html;
        }

        window.changePage = (page) => {
            currentPage = page;
            renderPayments(filteredPayments);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        function renderPayments(payments) {
            const list = document.getElementById('payment-list');
            const totalItems = payments.length;

            if (totalItems === 0) {
                list.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-credit-card"></i>
                        <h3>No payment history found</h3>
                        <p>No results match your current filters.</p>
                    </div>
                `;
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const paginatedPayments = payments.slice(startIndex, startIndex + itemsPerPage);

            renderPagination(totalItems);

            list.innerHTML = paginatedPayments.map(p => {
                const eSnap = p.event_snapshot ? JSON.parse(p.event_snapshot) : {};
                const category = (p.event_category || eSnap.category || '').toLowerCase();
                const isConcert = category === 'concert';
                
                const eventTitle = eSnap.title || p.live_title || 'Event';
                const eventDateRaw = eSnap.event_date || p.live_date || p.booking_event_date;
                const eventDate = eventDateRaw ? formatDate(eventDateRaw) : 'TBA';
                const location = eSnap.venue_name || p.live_venue || 'TBA';
                const tier = (eSnap.package_tier || p.booking_tier || 'Standard').toLowerCase();

                let imgUrl = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                let rawImg = eSnap.image_path || p.live_image || '';
                if (rawImg) {
                    if (rawImg.startsWith('[')) {
                        try { const imgs = JSON.parse(rawImg); rawImg = imgs[0] || ''; } catch (e) { }
                    }
                    imgUrl = rawImg.startsWith('/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
                }

                const bookingDate = formatDateShort(p.created_at);
                const paidDate = p.status === 'succeeded' ? formatDateShort(p.paid_at || p.created_at) : 'Unpaid';
                const paidDateColor = p.status === 'succeeded' ? '' : 'color: #DC2626; font-weight: 700;';

                const isCancelled = (p.booking_status || '').toLowerCase() === 'cancelled';

                return `
                    <div class="payment-item" style="${isCancelled ? 'opacity: 0.85;' : ''}">
                        <div class="event-img-container">
                            <img src="${imgUrl}" alt="Event" class="event-img">
                        </div>
                        <div class="payment-details">
                            <div class="event-title-row">
                                <a href="/EventManagementSystem/public/client/bookings/view?id=${p.booking_id}" class="event-title">${escapeHtml(eventTitle)}</a>
                                <span class="tier-badge tier-${tier}">${tier}</span>
                                ${isConcert ? `<span class="ticket-badge"><i class="fa-solid fa-ticket"></i> Ticket</span>` : ''}
                                ${isCancelled ? `<span class="cancelled-badge"><i class="fa-solid fa-ban"></i> Cancelled</span>` : ''}
                            </div>
                            <div class="meta-grid">
                                <div class="meta-col">
                                    <span class="label">Location</span>
                                    <span class="val"><i class="fa-solid fa-location-dot"></i> ${escapeHtml(location)}</span>
                                </div>
                                <div class="meta-col">
                                    <span class="label">Event Date</span>
                                    <span class="val"><i class="fa-regular fa-calendar"></i> ${eventDate}</span>
                                </div>
                                <div class="meta-col">
                                    <span class="label">Payment Details</span>
                                    <span class="val" style="font-size: 12px; line-height: 1.4; display: block;">
                                        Booking: ${bookingDate}<br>
                                        <span style="${paidDateColor}">Paid: ${paidDate}</span>
                                    </span>
                                    ${p.ticket_code ? `
                                        <div class="ticket-tag" style="margin-top: 5px; display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: #F0FDF4; color: #15803d; border-radius: 4px; font-size: 11px; font-weight: 600; border: 1px solid #BBF7D0;">
                                            <i class="fa-solid fa-ticket"></i> ${p.ticket_code}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="amount-col">
                            <span class="amount-val">Rs. ${parseFloat(p.paid_amount || 0).toLocaleString()} <small style="font-size: 11px; opacity: 0.7; display: block; font-weight: 400;">of Rs. ${parseFloat(p.amount).toLocaleString()}</small></span>
                            <span class="status-pill status-${p.ui_status}">
                                <i class="fa-solid ${p.ui_status === 'paid' ? 'fa-circle-check' : (p.ui_status === 'partial' ? 'fa-circle-half-stroke' : 'fa-circle-exclamation')}"></i>
                                ${p.ui_label}
                            </span>
                        </div>
                        <div class="actions-col">
                            <a href="/EventManagementSystem/public/client/bookings/view?id=${p.booking_id}" class="btn-action btn-receipt">View Details</a>
                            ${p.ui_status === 'pending' ? `
                                <a href="/EventManagementSystem/public/client/bookings/view?id=${p.booking_id}" class="btn-action btn-pay" style="background: var(--primary-green); color: white; border: none; padding: 10px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;">Pay Now</a>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function formatDateShort(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        async function cancelPayment(paymentId, bookingId) {
            if (!confirm('Are you sure you want to cancel this booking and its pending payment?')) return;
            try {
                const response = await window.emsApi.apiFetch(`/api/v1/bookings/${bookingId}/cancel`, {
                    method: 'PATCH'
                });
                if (response.success) {
                    alert('Booking cancelled successfully.');
                    loadPaymentHistory(); // Reload data
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', function (event) {
            const container = document.getElementById('profile-container');
            if (container && !container.contains(event.target)) {
                document.getElementById('profile-dropdown').classList.remove('show');
            }
        });
    </script>
<?php include 'partials/footer.php'; ?>