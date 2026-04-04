<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - <?php echo SITE_NAME; ?></title>
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
</head>

<body>

    <?php
    $activePage = 'bookings';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header b-header">
            <div class="header-left b-header-left">
                <h1>Bookings</h1>
                <p>Manage and review all client bookings</p>
            </div>

            <div class="header-right b-header-right">
                <div class="search-wrap top-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="topSearchInput" placeholder="Search bookings...">
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
                <p class="stat-title">Total Bookings</p>
                <h2 class="stat-number dark"><?php echo number_format($totalBookings ?? 0); ?></h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Confirmed</p>
                <h2 class="stat-number green"><?php echo number_format($confirmedCount ?? 0); ?></h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Pending</p>
                <h2 class="stat-number orange"><?php echo number_format($pendingCount ?? 0); ?></h2>
            </div>
            <div class="stat-box">
                <p class="stat-title">Cancelled</p>
                <h2 class="stat-number red"><?php echo number_format($cancelledCount ?? 0); ?></h2>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="filters-row">
            <div class="search-wrap bottom-search">
                <i class="fas fa-search"></i>
                <input type="text" id="filterSearchInput" placeholder="Filter by client or event...">
            </div>

            <!-- Custom Event Dropdown -->
            <div class="custom-premium-dropdown" id="eventDropdown">
                <div class="dropdown-trigger">
                    <span class="selected-val">All Events</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-item active" data-value="all">All Events</div>
                    <div class="dropdown-item" data-value="weddings">Weddings</div>
                    <div class="dropdown-item" data-value="meetings">Meetings</div>
                    <div class="dropdown-item" data-value="cultural events">Cultural Events</div>
                    <div class="dropdown-item" data-value="family functions">Family Functions</div>
                    <div class="dropdown-item" data-value="other events and programs">Other Events and Programs</div>
                </div>
            </div>

            <!-- Custom Package Dropdown -->
            <div class="custom-premium-dropdown" id="packageDropdown">
                <div class="dropdown-trigger">
                    <span class="selected-val">All Packages</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-item active" data-value="all">All Packages</div>
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
                    class="badge"><?php echo number_format($totalBookings ?? 0); ?></span></button>
            <button class="status-tab" data-status="pending">Pending <span
                    class="badge"><?php echo number_format($pendingCount ?? 0); ?></span></button>
            <button class="status-tab" data-status="confirmed">Confirmed <span
                    class="badge"><?php echo number_format($confirmedCount ?? 0); ?></span></button>
            <button class="status-tab" data-status="completed">Completed <span
                    class="badge"><?php echo number_format($completedCount ?? 0); ?></span></button>
            <button class="status-tab" data-status="cancelled">Cancelled <span
                    class="badge"><?php echo number_format($cancelledCount ?? 0); ?></span></button>
        </div>

        <!-- Bookings List / Table -->
        <div class="bookings-list-container">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">CLIENT</th>
                        <th>EVENT</th>
                        <th>PACKAGE</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th style="text-align:right; padding-right:24px;">ACTION</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="no-data">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $idx => $b):
                            $clientName = $b['full_name'] ?: ($b['client_user_name'] ?: 'Unknown Client');
                            $initials = strtoupper(substr($clientName, 0, 2));
                            if (strpos($clientName, ' ') !== false) {
                                $parts = explode(' ', $clientName);
                                $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
                            }
                            $rowStatus = strtolower($b['display_status'] ?? $b['status']);

                            $dateObj = new DateTime($b['event_date'] ?: $b['event_start_date']);
                            $shortDate = $dateObj->format('M d');
                            $year = $dateObj->format('Y');

                            $packageClass = strtolower($b['package_tier']);
                            ?>
                            <tr class="booking-row" data-client="<?php echo strtolower(htmlspecialchars($clientName)); ?>"
                                data-event="<?php echo strtolower(htmlspecialchars($b['event_title'])); ?>"
                                data-category="<?php echo strtolower(htmlspecialchars($b['event_category'])); ?>"
                                data-package="<?php echo strtolower($b['package_tier']); ?>"
                                data-status="<?php echo $rowStatus; ?>" data-date="<?php echo $dateObj->format('Y-m-d'); ?>">

                                <td style="padding-left:24px;">
                                    <div class="client-cell" style="display: flex; align-items: center; gap: 12px;">
                                        <div class="client-avatar <?php echo $packageClass; ?>-av" style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                                            <?php if (!empty($b['client_profile_pic'])): ?>
                                                <img src="<?php echo htmlspecialchars($b['client_profile_pic']); ?>" alt="Client" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <?php echo $initials; ?>
                                            <?php endif; ?>
                                        </div>
                                        <span class="client-name" style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($clientName); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="event-title-cell"><?php echo htmlspecialchars($b['event_title']); ?></div>
                                </td>
                                <td>
                                    <span
                                        class="pkg-badge <?php echo $packageClass; ?>"><?php echo strtoupper($b['package_tier']); ?></span>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <span class="m-d"><?php echo $shortDate; ?></span>
                                        <span class="year"><?php echo $year; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-cell">
                                        <span class="curr">Rs.</span>
                                        <span class="val"><?php echo number_format($b['total_amount'], 2); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-cell">
                                        <span class="dot <?php echo $rowStatus; ?>"></span>
                                        <span
                                            class="st-text <?php echo $rowStatus; ?>"><?php echo ucfirst($rowStatus); ?></span>
                                    </div>
                                </td>
                                <td style="text-align:right; padding-right:24px;">
                                    <div class="action-cell">
                                        <?php if ($rowStatus == 'pending'): ?>
                                            <form action="/EventManagementSystem/public/organizer/bookings/approve" method="POST"
                                                style="margin:0; display:inline-block;">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <button type="submit" class="btn-approve"
                                                    onclick="return confirm('Approve this booking?')">Approve</button>
                                            </form>
                                            <a href="/EventManagementSystem/public/organizer/bookings/view?id=<?php echo $b['id']; ?>"
                                                class="btn-view secondary">View</a>
                                        <?php elseif ($rowStatus == 'cancelled'): ?>
                                            <a href="/EventManagementSystem/public/organizer/bookings/view?id=<?php echo $b['id']; ?>"
                                                class="btn-review">Review</a>
                                        <?php else: ?>
                                            <a href="/EventManagementSystem/public/organizer/bookings/view?id=<?php echo $b['id']; ?>"
                                                class="btn-view">View</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination-row">
                <div class="showing-text">
                    Showing <span id="visibleCount"><?php echo count($bookings); ?></span> of <span
                        id="totalBookingsSpan"><?php echo number_format($totalBookings ?? 0); ?></span> bookings
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- Pagination buttons injected via JS -->
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = Array.from(document.querySelectorAll('.booking-row'));
            const topSearch = document.getElementById('topSearchInput');
            const filterSearch = document.getElementById('filterSearchInput');
            const dateFilter = document.getElementById('dateFilter');
            const statusTabs = document.querySelectorAll('.status-tab');
            const visibleCountLabel = document.getElementById('visibleCount');
            const totalBookingsSpan = document.getElementById('totalBookingsSpan');
            const paginationControls = document.getElementById('paginationControls');

            let currentStatus = 'all';
            let currentCategory = 'all';
            let currentPackage = 'all';
            let currentPage = 1;
            const itemsPerPage = 5;

            // Custom Dropdown Handling
            function initCustomDropdown(id, callback) {
                const dropdown = document.getElementById(id);
                const trigger = dropdown.querySelector('.dropdown-trigger');
                const menu = dropdown.querySelector('.dropdown-menu');
                const items = dropdown.querySelectorAll('.dropdown-item');
                const selectedText = dropdown.querySelector('.selected-val');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Close other dropdowns
                    document.querySelectorAll('.custom-premium-dropdown').forEach(d => {
                        if (d !== dropdown) d.classList.remove('open');
                    });
                    dropdown.classList.toggle('open');
                });

                items.forEach(item => {
                    item.addEventListener('click', () => {
                        const val = item.dataset.value;
                        selectedText.textContent = item.textContent;
                        items.forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                        dropdown.classList.remove('open');
                        callback(val);
                    });
                });
            }

            // Close dropdowns on outside click
            // Removed redundant local listener blocks

            DropdownManager.onSelect('eventDropdown', (val) => {
                currentCategory = val;
                currentPage = 1;
                applyFilters();
            });
            DropdownManager.onSelect('packageDropdown', (val) => {
                currentPackage = val;
                currentPage = 1;
                applyFilters();
            });

            // Sync search inputs
            topSearch.addEventListener('input', function () {
                filterSearch.value = this.value;
                currentPage = 1;
                applyFilters();
            });
            filterSearch.addEventListener('input', function () {
                topSearch.value = this.value;
                currentPage = 1;
                applyFilters();
            });

            dateFilter.addEventListener('change', () => { currentPage = 1; applyFilters(); });

            statusTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    statusTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentStatus = this.dataset.status;
                    currentPage = 1;
                    applyFilters();
                });
            });

            function applyFilters() {
                const searchQ = filterSearch.value.toLowerCase();
                const dtFilter = dateFilter.value;

                let visibleRows = [];

                rows.forEach(row => {
                    const client = row.dataset.client || '';
                    const event = row.dataset.event || '';
                    const category = row.dataset.category || '';
                    const pkg = row.dataset.package || '';
                    const status = row.dataset.status || '';
                    const date = row.dataset.date || '';

                    let matchSearch = client.includes(searchQ) || event.includes(searchQ);
                    let matchCategory = currentCategory === 'all' || category === currentCategory;
                    let matchPkg = currentPackage === 'all' || pkg === currentPackage;
                    let matchDate = dtFilter === '' || date === dtFilter;
                    let matchStatus = currentStatus === 'all' || status === currentStatus;

                    if (matchSearch && matchCategory && matchPkg && matchDate && matchStatus) {
                        visibleRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                visibleCountLabel.textContent = visibleRows.length;
                renderPagination(visibleRows);
            }

            function renderPagination(visibleRows) {
                const totalVisible = visibleRows.length;
                const totalPages = Math.ceil(totalVisible / itemsPerPage);

                // Hide all rows first
                rows.forEach(row => row.style.display = 'none');

                if (totalPages === 0) {
                    paginationControls.innerHTML = '';
                    return;
                }

                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                // Show visible rows for current page
                const startIdx = (currentPage - 1) * itemsPerPage;
                const endIdx = startIdx + itemsPerPage;
                for (let i = startIdx; i < endIdx && i < totalVisible; i++) {
                    visibleRows[i].style.display = 'table-row';
                }

                // Render buttons
                let html = '';

                html += `<button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}"><i class="fa-solid fa-angle-left"></i></button>`;

                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i > 1 && i < totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 && currentPage > 3) html += `<span class="dots">...</span>`;
                            if (i === totalPages - 1 && currentPage < totalPages - 2) html += `<span class="dots">...</span>`;
                            continue;
                        }
                    }
                    html += `<button class="p-btn num ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
                }

                html += `<button class="p-btn next ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}"><i class="fa-solid fa-angle-right"></i></button>`;

                paginationControls.innerHTML = html;

                // Re-bind click events
                paginationControls.querySelectorAll('.p-btn:not(.disabled)').forEach(btn => {
                    btn.addEventListener('click', function () {
                        currentPage = parseInt(this.dataset.page);
                        renderPagination(visibleRows);
                    });
                });
            }

            // Init
            applyFilters();
        });
    </script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>

</html>
