<?php
$categories = ['All', 'Weddings', 'Meetings', 'Concert', 'Cultural Events', 'Family Functions', 'Other Events and Programs'];
$currentCategory = $_GET['category'] ?? 'All';
$searchQuery = $_GET['search'] ?? '';

$title = 'Browse Events - e-Plan';
$activePage = 'events';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/browse-events.css?v=' . time() . '">
';
include 'partials/header.php';
?>




    <!-- VIEWS CONTAINER -->
    <div id="browse-events-view">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="container" style="margin-top: 20px;">
                <div
                    style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; border-left: 4px solid #b91c1c; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i>
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <section class="hero">
            <h1>Browse Events</h1>
            <p>Discover premium event planning services tailored for your most memorable milestones across Nepal.</p>

            <div class="search-bar">
                <div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <input type="text" id="event-search-input" class="search-input"
                    placeholder="Search for weddings, conferences, or festivals..."
                    value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="button" class="search-btn" onclick="triggerSearch()">Search</button>
            </div>
        </section>

        <div class="container">

            <!-- Category Filters -->
            <div class="category-filters">
                <?php foreach ($categories as $cat): ?>
                    <button type="button" 
                        onclick="filterByCategory('<?php echo htmlspecialchars($cat); ?>', this)"
                        class="filter-btn <?php echo $currentCategory === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Showing Count -->
            <div id="event-search-count" style="margin-bottom: 20px; color: var(--text-gray); font-size: 14px; font-weight: 500;">
                Showing <?php echo count($events); ?> of <?php echo $totalActiveEvents; ?> event campaigns
            </div>

            <!-- Event Grid -->
            <div class="event-grid" id="event-grid">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <div class="event-image-container">
                                <?php
                                if (!empty($event['image_path'])) {
                                    $image = ($event['image_path'][0] === '/') ? $event['image_path'] : '/EventManagementSystem/public/assets/images/events/' . $event['image_path'];
                                } else {
                                    $image = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                                <span
                                    class="event-category-tag"><?php echo htmlspecialchars($event['category'] ?: 'Event'); ?></span>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>

                                <div class="event-location">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?php echo htmlspecialchars($event['venue_location'] ?: 'Location TBD'); ?>
                                </div>

                                <?php
                                // Parse packages JSON to find the starting price
                                $packages = json_decode($event['packages'], true);
                                $startingPrice = 0;
                                if (is_array($packages) && count($packages) > 0) {
                                    $prices = array_column($packages, 'price');
                                    // Remove empty and non-numeric
                                    $prices = array_filter($prices, 'is_numeric');
                                    if (!empty($prices)) {
                                        $startingPrice = min($prices);
                                    }
                                }

                                // Default formatting if no explicit price
                                $displayPrice = $startingPrice > 0 ? number_format($startingPrice) : "10,000";
                                ?>
                                <div class="event-price"><?php echo (strtolower($event['category']) === 'concert') ? 'Tickets from' : 'Packages from'; ?> Rs. <?php echo $displayPrice; ?></div>

                                <a href="/EventManagementSystem/public/client/events/view?id=<?php echo $event['id']; ?>"
                                    class="btn-view-packages">
                                    <?php echo (strtolower($event['category']) === 'concert') ? 'View Ticket Packages &rarr;' : 'View Packages &rarr;'; ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-events">
                        <h3>No events found matching your criteria.</h3>
                        <p>Try selecting a different category or adjusting your search.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="event-pagination"></div>
        </div>
    </div> <!-- END browse-events-view -->

    <!-- MY BOOKINGS VIEW -->
    <?php if (isset($_SESSION['user_id']) && isset($bookings)): ?>
        <div id="my-bookings-view" style="display: none;">
            <div class="page-header-row clearfix">
                <a href="/EventManagementSystem/public/client/events" class="btn-browse-more"
                    onclick="showBrowseEvents(event)">Browse More Events <i class="fa-solid fa-arrow-right"></i></a>
                <div class="headings">
                    <h1 class="page-header-title">MY BOOKINGS</h1>
                    <p class="page-header-desc">Manage your upcoming and past event reservations. Keep track of invitations,
                        payments, and schedules in one place.</p>
                </div>
            </div>

            <!-- 4 Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <span class="stat-label">Total Bookings</span>
                    <span class="stat-value"><?php echo str_pad($totalBookings, 2, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="stat-card confirmed">
                    <span class="stat-label">Confirmed</span>
                    <span class="stat-value"><?php echo str_pad($confirmedCount, 2, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="stat-card pending">
                    <span class="stat-label">Pending</span>
                    <span class="stat-value"><?php echo str_pad($pendingCount, 2, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="stat-card completed">
                    <span class="stat-label">Completed</span>
                    <span class="stat-value"><?php echo str_pad($completedCount, 2, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <div class="filter-tab active-tab" onclick="filterBookings('all', this)">All
                    <span><?php echo $totalBookings; ?></span>
                </div>
                <div class="filter-tab" onclick="filterBookings('upcoming', this)">Upcoming
                    <span><?php echo $upcomingCount; ?></span>
                </div>
                <div class="filter-tab" onclick="filterBookings('completed', this)">Completed
                    <span><?php echo $completedCount; ?></span>
                </div>
                <div class="filter-tab" onclick="filterBookings('cancelled', this)">Cancelled
                    <span><?php echo $cancelledCount; ?></span>
                </div>
            </div>

            <div class="main-layout">
                <!-- Left: Bookings List -->
                <div class="booking-list" id="bookingsList">
                    <?php if (empty($bookings)): ?>
                        <div class="empty-state">No bookings found in this category.</div>
                    <?php else: ?>
                        <?php foreach ($bookings as $index => $booking):
                            $dispStatus = $booking['display_status'] ?? $booking['status'];
                            $isUpcoming = in_array($dispStatus, ['pending', 'confirmed']) ? 'true' : 'false';
                            $catStyle = $booking['event_category'] == 'Exhibition' || strtolower($booking['event_category']) == 'education' ? 'background: #e5e7eb; color: #4b5563;' : '';
                            if (strtolower($booking['event_category']) == 'music') {
                                $catStyle = 'background: #fef08a; color: #854d0e;';
                            }
                        ?>
                            <div class="b-item" data-status="<?php echo $dispStatus; ?>" data-upcoming="<?php echo $isUpcoming; ?>"
                                data-index="<?php echo $index; ?>" onclick="selectBooking(<?php echo $index; ?>, this)">

                                <?php 
                                $eSnapList = !empty($booking['event_snapshot']) ? json_decode($booking['event_snapshot'], true) : null;
                                $bListTitle = $eSnapList['title'] ?? $booking['event_title'];
                                $bListCat = $eSnapList['category'] ?? ($booking['event_category'] ?: 'Event');
                                $rawImg = !empty($eSnapList['image_path']) ? $eSnapList['image_path'] : (!empty($booking['event_image']) ? $booking['event_image'] : '/EventManagementSystem/public/assets/images/placeholder.jpg'); 
                                $image = ($rawImg[0] === '/') ? $rawImg : '/EventManagementSystem/public/assets/images/events/' . $rawImg;
                                ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Event Cover" class="b-img">

                                <div class="b-content">
                                    <div>
                                        <div class="b-top">
                                            <div class="b-title-wrap">
                                                <h3 class="b-title"><?php echo htmlspecialchars($bListTitle); ?></h3>
                                                <span class="b-cat-badge"
                                                    style="<?php echo $catStyle; ?>"><?php echo htmlspecialchars($bListCat); ?></span>
                                            </div>
                                            <span class="b-status-badge status-<?php echo htmlspecialchars($dispStatus); ?>">
                                                <?php echo strtoupper($dispStatus); ?>
                                            </span>
                                        </div>
                                        <div class="b-middle">
                                            <span><i class="fa-solid fa-address-card"></i>
                                                <?php echo ucfirst($booking['package_tier']); ?> Package</span>
                                            <span><i class="fa-solid fa-user-group"></i>
                                                <?php echo htmlspecialchars($booking['guest_count']); ?> Guests</span>
                                            <span><i class="fa-regular fa-calendar"></i>
                                                <?php echo date('M d, Y', strtotime($booking['event_date'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="b-bottom">
                                        <div class="b-date-booked">Booked on:
                                            <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></div>
                                        <div class="b-price-action">
                                            <span class="b-price">Rs.
                                                <?php echo number_format($booking['total_amount'], 2); ?></span>
                                            <a href="/EventManagementSystem/public/client/bookings/view?id=<?php echo $booking['id']; ?>"
                                                class="b-view-link">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div id="pagination-controls" class="pagination-controls"></div>
                </div>

                <!-- Right: Sidebar -->
                <div class="details-panel" id="sidebarPanel" style="<?php echo empty($bookings) ? 'display:none;' : ''; ?>">
                    <div class="dp-header">
                        <h2 class="dp-title">Booking Details</h2>
                        <span class="dp-id-badge" id="sb-id">BK-000</span>
                    </div>

                    <img src="/EventManagementSystem/public/assets/images/placeholder.jpg" id="sb-img" class="dp-img"
                        alt="Event Image">

                    <div class="dp-status-row">
                        <span class="b-status-badge status-confirmed" id="sb-status">CONFIRMED</span>
                        <span class="dp-event-name" id="sb-event-title">Event Title</span>
                    </div>

                    <div class="dp-package-box">
                        <div class="dp-pkg-top">
                            <span class="dp-pkg-label">SELECTED PACKAGE</span>
                            <span class="dp-pkg-price" id="sb-price">Rs. 0.00</span>
                        </div>
                        <h3 class="dp-pkg-name" id="sb-pkg-name">Premium Package</h3>
                        <p class="dp-pkg-desc" id="sb-pkg-desc">Includes full access features.</p>
                    </div>

                    <div class="dp-info-list">
                        <div class="dp-info-item">
                            <div class="dp-ii-icon"><i class="fa-regular fa-user"></i></div>
                            <div class="dp-ii-content">
                                <span class="dp-ii-label">ORGANIZER</span>
                                <span class="dp-ii-val" id="sb-org-name">Organizer Name</span>
                                <span class="dp-ii-sub">Event Manager</span>
                            </div>
                        </div>
                        <div class="dp-info-item">
                            <div class="dp-ii-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="dp-ii-content">
                                <span class="dp-ii-label">LOCATION</span>
                                <span class="dp-ii-val" id="sb-loc-name">Venue Name</span>
                                <span class="dp-ii-sub" id="sb-loc-address">Address</span>
                            </div>
                        </div>
                        <div class="dp-info-item">
                            <div class="dp-ii-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="dp-ii-content">
                                <span class="dp-ii-label">CHECK-IN TIME</span>
                                <span class="dp-ii-val" id="sb-time">08:00 AM</span>
                                <span class="dp-ii-sub">Local time zone</span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn-send-msg" type="button"><i class="fa-regular fa-message"></i> Send
                            Message</button>
                        <form id="cancel-booking-form" action="/EventManagementSystem/public/client/bookings/cancel"
                            method="POST" style="margin-top: 12px; display: none;">
                            <input type="hidden" name="booking_id" id="cancel-booking-id" value="">
                            <button class="btn-send-msg btn-cancel-reservation" type="submit" style="background: #fee2e2; color: #b91c1c;">
                                <i class="fa-solid fa-xmark"></i> Cancel Booking
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <!-- SPA and Dashboard Script -->
    <script>
        // SPA Toggle Logic
        function showBrowseEvents(e) {
            if (e) e.preventDefault();
            document.getElementById('browse-events-view').style.display = 'block';
            let bookingsView = document.getElementById('my-bookings-view');
            if (bookingsView) bookingsView.style.display = 'none';

            document.getElementById('nav-btn-browse').classList.add('active');
            let btnBookings = document.getElementById('nav-btn-bookings');
            if (btnBookings) btnBookings.classList.remove('active');

            // Clean up the URL hash without scrolling
            history.pushState(null, null, '/EventManagementSystem/public/client/events');
        }

        function showMyBookings(e) {
            if (e) e.preventDefault();
            const browseView = document.getElementById('browse-events-view');
            const bookingsView = document.getElementById('my-bookings-view');
            
            if (browseView) browseView.style.display = 'none';
            if (bookingsView) bookingsView.style.display = 'block';
            if (typeof updateBookingsUI === 'function') updateBookingsUI();

            let btnBrowse = document.getElementById('nav-btn-browse');
            if (btnBrowse) btnBrowse.classList.remove('active');
            let btnBookings = document.getElementById('nav-btn-bookings');
            if (btnBookings) btnBookings.classList.add('active');

            history.pushState(null, null, '/EventManagementSystem/public/client/events#my-bookings');
        }

        // Dashboard filtering and JS Logic
        const bookingsData = <?php echo isset($bookings) ? json_encode($bookings) : '[]'; ?>;

        function formatTime(dateStr) {
            if (!dateStr) return '08:00 AM';
            const d = new Date(dateStr);
            let hours = d.getHours();
            let minutes = d.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            return hours + ':' + minutes + ' ' + ampm;
        }

        function formatCheckinTime(timeStr) {
            if (!timeStr) return '10:00 AM';
            // If it's already in AM/PM format
            if (timeStr.includes('AM') || timeStr.includes('PM')) return timeStr;

            const [hours24, minutes] = timeStr.split(':');
            let hours = parseInt(hours24);
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            return hours + ':' + minutes + ' ' + ampm;
        }

        function selectBooking(index, element) {
            document.querySelectorAll('.b-item').forEach(el => el.classList.remove('active'));
            if (element) {
                element.classList.add('active');
            }

            const data = bookingsData[index];
            if (!data) return;

            // Snapshots
            const eSnap = data.event_snapshot ? JSON.parse(data.event_snapshot) : null;
            const pSnap = data.package_snapshot ? JSON.parse(data.package_snapshot) : null;

            document.getElementById('sb-id').innerText = 'BK-' + String(data.id).padStart(3, '0');
            
            let rawImg = eSnap?.image_path || data.event_image || '';
            let imgUrl = '/EventManagementSystem/public/assets/images/placeholder.jpg';
            if (rawImg) {
                imgUrl = (rawImg[0] === '/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
            }
            document.getElementById('sb-img').src = imgUrl;

            const dispStatus = data.display_status || data.status;
            const statusEl = document.getElementById('sb-status');
            statusEl.innerText = dispStatus.toUpperCase();
            statusEl.className = 'b-status-badge status-' + dispStatus;

            document.getElementById('sb-event-title').innerText = eSnap?.title || data.event_title;
            document.getElementById('sb-price').innerText = 'Rs. ' + parseFloat(data.total_amount).toLocaleString(undefined, {
                minimumFractionDigits: 2
            });

            let pName = data.package_tier.charAt(0).toUpperCase() + data.package_tier.slice(1) + ' Package';
            let pDesc = 'Includes selected access & features.';
            if (pSnap) {
                pDesc = pSnap.description || pDesc;
            } else if (data.packages_data && data.packages_data[data.package_tier] && data.packages_data[data.package_tier].description) {
                pDesc = data.packages_data[data.package_tier].description;
            }

            document.getElementById('sb-pkg-name').innerText = pName;
            document.getElementById('sb-pkg-desc').innerText = pDesc;
            document.getElementById('sb-org-name').innerText = data.organizer_name || 'Event Organizer';

            let locName = eSnap?.venue_name || data.venue_name || 'Convention Center';
            let locAddr = eSnap?.venue_location || data.venue_location || 'Address TBD';
            if (!locName && locAddr) {
                locName = locAddr;
                locAddr = "Local Venue";
            }
            document.getElementById('sb-loc-name').innerText = locName;
            document.getElementById('sb-loc-address').innerText = locAddr;
            document.getElementById('sb-time').innerText = formatCheckinTime(data.checkin_time);

            // Handle Cancel Button
            let cancelForm = document.getElementById('cancel-booking-form');
            if (cancelForm) {
                const bStatus = (data.status || '').toLowerCase();
                const payStatus = (data.payment_status || 'unpaid').toLowerCase();

                // Rule: HIDE only if (Confirmed AND (Partially Paid or Paid))
                // Also hide if already cancelled or completed
                const isLocked = (bStatus === 'confirmed' && payStatus !== 'unpaid');
                const isActive = (bStatus === 'pending' || bStatus === 'confirmed');

                if (isActive && !isLocked) {
                    document.getElementById('cancel-booking-id').value = data.id;
                    cancelForm.style.display = 'block';
                } else {
                    cancelForm.style.display = 'none';
                }
            }
        }

        let currentFilter = 'all';
        let currentPage = 1;
        const itemsPerPage = 5;

        function applyPagination() {
            const items = document.querySelectorAll('.b-item');
            let matchedItems = [];

            items.forEach(item => {
                const status = item.getAttribute('data-status');
                const upcoming = item.getAttribute('data-upcoming');

                let show = false;
                if (currentFilter === 'all') show = true;
                else if (currentFilter === 'upcoming' && upcoming === 'true') show = true;
                else if (status === currentFilter) show = true;

                if (show) matchedItems.push(item);
            });

            items.forEach(item => item.style.display = 'none');

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageItems = matchedItems.slice(startIndex, endIndex);

            pageItems.forEach(item => item.style.display = 'flex');

            let noData = document.querySelector('.empty-state');
            const sidebar = document.getElementById('sidebarPanel');
            let bList = document.getElementById('bookingsList');
            let paginationContainer = document.getElementById('pagination-controls');

            if (matchedItems.length === 0) {
                if (!noData && bList) {
                    noData = document.createElement('div');
                    noData.className = 'empty-state';
                    noData.innerText = 'No bookings found in this category.';
                    if (paginationContainer) bList.insertBefore(noData, paginationContainer);
                    else bList.appendChild(noData);
                } else if (noData) {
                    noData.style.display = 'block';
                }
                if (sidebar) sidebar.style.display = 'none';
                if (paginationContainer) paginationContainer.innerHTML = '';
            } else {
                if (noData) noData.style.display = 'none';
                if (sidebar) sidebar.style.display = 'block';
                if (pageItems.length > 0) {
                    const firstVisibleIdx = pageItems[0].getAttribute('data-index');
                    selectBooking(firstVisibleIdx, pageItems[0]);
                }
                renderPaginationControls(matchedItems.length);
            }
        }

        function renderPaginationControls(totalItems) {
            const container = document.getElementById('pagination-controls');
            if (!container) return;

            container.innerHTML = '';
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalPages <= 1) return;

            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    applyPagination();
                    window.scrollTo(0, 0);
                }
            };
            container.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.innerText = i;
                if (i === currentPage) pageBtn.classList.add('active');
                pageBtn.onclick = () => {
                    currentPage = i;
                    applyPagination();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                };
                container.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    applyPagination();
                    window.scrollTo(0, 0);
                }
            };
            container.appendChild(nextBtn);
        }

        function filterBookings(filterType, element) {
            if (element) {
                document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active-tab'));
                element.classList.add('active-tab');
            }
            currentFilter = filterType;
            currentPage = 1;
            applyPagination();
        }

        document.addEventListener("DOMContentLoaded", function() {
            applyPagination();

            if (window.location.hash === '#my-bookings') {
                showMyBookings(null);
            }
        });
    </script>




    <script src="/EventManagementSystem/public/assets/js/client/browse_events.js?v=<?php echo time(); ?>"></script>

    <script>
        (function () {
            if (!window.emsApi) return;

            const cancelForm = document.getElementById('cancel-booking-form');
            if (!cancelForm) return;

            cancelForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const id = document.getElementById('cancel-booking-id')?.value;
                if (!id) return;
                if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;

                try {
                    await window.emsApi.apiFetch(`/api/v1/bookings/${id}/cancel`, { method: 'PATCH' });
                    window.location.reload();
                } catch (err) {
                    console.error('Cancel via API failed:', err);
                    alert('Error: ' + err.message);
                }
            });
        })();
    </script>
    <?php include 'partials/feedback_popup.php'; ?>
<?php include 'partials/footer.php'; ?>