<?php
/** @var array $payments @var float $totalSpent @var int $confirmedBookingsCount @var int $pendingPaymentsCount */
$initials = '';
$fullName = trim($_SESSION['user_fullname'] ?? 'User');
$nameParts = explode(' ', $fullName);
foreach ($nameParts as $p) {
    if (!empty($p))
        $initials .= strtoupper(substr($p, 0, 1));
}
if (strlen($initials) > 2)
    $initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payment History - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
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
            margin-bottom: 32px;
        }

        .header-section h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 8px;
        }

        .header-section p {
            color: var(--text-muted);
            font-size: 15px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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
            border-bottom: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-card.total {
            border-color: var(--primary-green);
        }

        .stat-card.confirmed {
            border-color: #F59E0B;
        }

        .stat-card.pending {
            border-color: #DC2626;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--text-muted);
        }

        .stat-info .label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
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
            display: flex;
            gap: 12px;
            margin-bottom: 40px;
            align-items: center;
            background: #eff2f5;
            /* Light gray-green tint from image */
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .search-box {
            flex: 1;
            /* Matches the longer search in the image */
            max-width: 680px;
            /* But capped for better looks on wide screens */
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 15px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 42px;
            border-radius: 6px;
            border: 1px solid transparent;
            background: var(--white);
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.02);
        }

        .filter-btn {
            background: var(--white);
            padding: 10px 18px;
            border-radius: 6px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            margin-left: auto;
            /* Pushes items to the far right */
        }

        .filter-btn i {
            font-size: 13px;
            color: #64748b;
        }

        .filter-btn:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        /* Custom Premium Dropdown */
        .custom-dropdown {
            position: relative;
            min-width: 160px;
            user-select: none;
        }

        .cd-trigger {
            background: var(--white);
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .cd-trigger span {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .cd-trigger i {
            font-size: 12px;
            color: #64748b;
            transition: transform 0.2s;
        }

        .custom-dropdown.open .cd-trigger {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(36, 106, 85, 0.08);
        }

        .custom-dropdown.open .cd-trigger i {
            transform: rotate(180deg);
        }

        .cd-options {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 100%;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-dropdown.open .cd-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .cd-option {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cd-option:hover {
            background: #f8fafc;
            color: var(--primary-green);
        }

        .cd-option.active {
            background: #f0fdf4;
            /* Light green background from image */
            color: var(--primary-green);
            font-weight: 700;
        }

        /* Payment List */
        .payment-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .payment-item {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 24px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .payment-item:hover {
            box-shadow: var(--shadow-md);
            transform: scale(1.01);
        }

        .event-img-container {
            width: 140px;
            height: 140px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .event-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .payment-item:hover .event-img {
            transform: scale(1.1);
        }

        .payment-details {
            flex: 1;
        }

        .event-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .event-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            transition: color 0.2s;
        }

        .event-title:hover {
            color: var(--primary-green);
        }

        .tier-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .tier-premium {
            background: #FEF3C7;
            color: #92400E;
        }

        .tier-standard {
            background: #E0F2FE;
            color: #075985;
        }

        .tier-basic {
            background: #F3F4F6;
            color: #374151;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        .meta-col .label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            display: block;
        }

        .meta-col .val {
            font-size: 14px;
            color: var(--text-main);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-col i {
            color: var(--text-muted);
            font-size: 14px;
        }

        .amount-col {
            text-align: right;
            min-width: 180px;
        }

        .amount-val {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            display: block;
            margin-bottom: 8px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-paid {
            background: #DCFCE7;
            color: #166534;
        }

        .status-partial {
            background: #FFEDD5;
            color: #9A3412;
        }

        .status-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .cancelled-badge {
            background: #FEE2E2;
            color: #991B1B;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ticket-badge {
            background: #ECFDF5;
            color: #065F46;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .actions-col {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-action {
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .btn-receipt {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            background: transparent;
        }

        .btn-receipt:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        .btn-cancel {
            border: 2px solid #DC2626;
            color: #DC2626;
            background: transparent;
        }

        .btn-cancel:hover {
            background: #DC2626;
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 100px 20px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }

        .empty-state i {
            font-size: 64px;
            color: #e2e8f0;
            margin-bottom: 24px;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        /* Skeleton Loading */
        .skeleton {
            background: #e2e8f0;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-text {
            height: 20px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
            padding-bottom: 40px;
        }

        .page-btn {
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: var(--white);
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-btn:hover:not(:disabled) {
            border-color: var(--primary-green);
            color: var(--primary-green);
            background: #f0fdf4;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .page-btn.active {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        .page-info {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0 12px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/client/home" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/bookings">My Bookings</a>
            <a href="/EventManagementSystem/public/client/tickets">My Tickets</a>
            <a href="/EventManagementSystem/public/client/payments" class="active">Payment History</a>
        </nav>
        <div class="nav-icons">
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" id="header-avatar">
                        <?php else: ?>
                            <span id="header-initials"><?php echo htmlspecialchars($initials); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Dropdown Modal -->
                    <div id="profile-dropdown" class="profile-dropdown">
                        <div class="pd-top">
                            <div class="pd-avatar-container">
                                <div class="pd-avatar">
                                    <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">
                                    <?php else: ?>
                                        <span id="dropdown-initials"><?php echo htmlspecialchars($initials); ?></span>
                                    <?php endif; ?>
                                </div>
                                <label for="profile_picture_upload" class="pd-edit-icon" title="Change Photo">
                                    <i class="fa-solid fa-pen"></i>
                                </label>
                                <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                    <div class="pd-delete-icon" onclick="deleteProfilePicture()" title="Remove Photo">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="profile_picture_upload" accept="image/*" style="display: none;"
                                    onchange="uploadProfilePicture(this)">
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
                            <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            <span
                                class="pd-role"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Client')); ?></span>
                        </div>
                        <div class="pd-bottom">
                            <a href="/EventManagementSystem/public/client/feedback" class="pd-rating-btn">
                                <i class="fa-solid fa-star"></i> Rating &amp; Feedback
                            </a>
                            <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

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

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-left">
            <div class="footer-logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                    style="height: 28px; width: auto; object-fit: contain;"></div>
            <p class="copyright">&copy; 2026 e.plan Architectural Event Curation. All rights reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Support</a>
        </div>
    </footer>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
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
</body>

</html>