<?php
$title = 'My Bookings - e-Plan';
$activePage = 'bookings';
include 'partials/header.php';
?>


    <div class="dashboard-container">

        <!-- Header Row -->
        <div class="page-header-row clearfix">
            <a href="/EventManagementSystem/public/client/events" class="btn-browse-more">Browse More Events <i
                    class="fa-solid fa-arrow-right"></i></a>
            <div class="headings">
                <h1 class="page-header-title">MY BOOKINGS</h1>
                <p class="page-header-desc">Manage your upcoming and past event reservations. Keep track of invitations,
                    payments, and schedules in one place.</p>
            </div>
        </div>

        <!-- 4 Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card total"
                onclick="filterBookings('all', document.querySelector('.filter-tab[onclick*=\'all\']'))"
                style="cursor:pointer;">
                <span class="stat-label">Total Bookings</span>
                <span class="stat-value"><?php echo str_pad($totalBookings, 2, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="stat-card confirmed"
                onclick="filterBookings('confirmed', document.querySelector('.filter-tab[onclick*=\'upcoming\']'))"
                style="cursor:pointer;">
                <span class="stat-label">Confirmed</span>
                <span class="stat-value"><?php echo str_pad($confirmedCount, 2, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="stat-card pending"
                onclick="filterBookings('pending', document.querySelector('.filter-tab[onclick*=\'upcoming\']'))"
                style="cursor:pointer;">
                <span class="stat-label">Pending</span>
                <span class="stat-value"><?php echo str_pad($pendingCount, 2, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="stat-card completed"
                onclick="filterBookings('completed', document.querySelector('.filter-tab[onclick*=\'completed\']'))"
                style="cursor:pointer;">
                <span class="stat-label">Completed</span>
                <span class="stat-value"><?php echo str_pad($completedCount, 2, '0', STR_PAD_LEFT); ?></span>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <div class="filter-tab active" onclick="filterBookings('all', this)">All
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
            <div class="booking-list-wrapper" style="display: flex; flex-direction: column; gap: 20px;">
                <div class="booking-list" id="bookingsList">
                    <div class="empty-state"><i class="fa-solid fa-spinner fa-spin"></i> Loading your bookings...</div>
                </div>
                <div class="pagination-controls" id="paginationControls" style="display: none;"></div>
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

                <div class="dp-info-list" style="margin-bottom: 0; padding-bottom: 0;">
                    <div class="dp-info-item">
                        <div class="dp-ii-icon"><i class="fa-regular fa-user"></i></div>
                        <div class="dp-ii-content">
                            <span class="dp-ii-label">ORGANIZER</span>
                            <span class="dp-ii-val" id="sb-org-name">Organizer Name</span>
                            <span class="dp-ii-sub">Event Manager</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Breakdown Section -->
                <!-- Payment Breakdown Section -->
                <div class="dp-info-list" id="standard-payment-breakdown"
                    style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
                    <div
                        style="font-size: 11px; color: #64748b; font-weight: 700; margin-bottom: 12px; letter-spacing: 0.5px;">
                        PAYMENT BREAKDOWN (50/50 POLICY)</div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-size: 13px; color: #475569;">Advance (50% Online)</span>
                        <span id="sb-advance-val" style="font-size: 13px; font-weight: 600; color: #1e293b;">Rs.
                            0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 13px; color: #475569;">Remaining (50% Cash)</span>
                        <span id="sb-balance-val" style="font-size: 13px; font-weight: 600; color: #1e293b;">Rs.
                            0.00</span>
                    </div>
                </div>

                <div class="dp-info-list"
                    style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-bottom: 20px;">
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

                <!-- Pay Now Button (Hidden by default, shown via JS) -->
                <a href="#" id="sb-pay-btn" class="btn-send-msg"
                    style="display: none; background: #246A55; color: white; text-align: center; text-decoration: none; border: none; font-weight: 600; margin-bottom: 12px;">
                    <i class="fa-solid fa-credit-card"></i> Pay 50% Advance Online
                </a>

                <!-- Print Ticket Button (Concerts Only) -->
                <a href="#" id="sb-print-btn" class="btn-send-msg" target="_blank"
                    style="display: none; background: #F59E0B; color: white; text-align: center; text-decoration: none; border: none; font-weight: 600; margin-bottom: 12px;">
                    <i class="fa-solid fa-print"></i> Print Your Ticket
                </a>

                <form id="cancel-booking-form" action="/EventManagementSystem/public/client/bookings/cancel"
                    method="POST" style="margin-top: 12px; display: none;">
                    <input type="hidden" name="booking_id" id="cancel-booking-id" value="">
                    <button class="btn-send-msg" type="submit" style="background: #fee2e2; color: #b91c1c;"
                        onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                        <i class="fa-solid fa-xmark"></i> Cancel Booking
                    </button>
                </form>
            </div>
        </div>

    </div>
    </div>

    <script>
        // API-based logic
        let bookingsData = [];
        let filteredBookings = [];
        let currentFilterType = 'all';
        let currentPage = 1;
        const itemsPerPage = 10;

        // Format date string to AM/PM Time
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

        function safeParse(json) {
            if (!json) return null;
            if (typeof json === 'object') return json;
            try {
                return JSON.parse(json);
            } catch (e) {
                console.error('SafeParse error:', e);
                return null;
            }
        }

        function updateStats() {
            const total = bookingsData.length;
            const confirmed = bookingsData.filter(b => b.status.toLowerCase() === 'confirmed').length;
            const pending = bookingsData.filter(b => b.status.toLowerCase() === 'pending').length;
            const completed = bookingsData.filter(b => b.status.toLowerCase() === 'completed').length;
            const cancelled = bookingsData.filter(b => b.status.toLowerCase() === 'cancelled').length;

            document.querySelector('.stat-card.total .stat-value').innerText = String(total).padStart(2, '0');
            document.querySelector('.stat-card.confirmed .stat-value').innerText = String(confirmed).padStart(2, '0');
            document.querySelector('.stat-card.pending .stat-value').innerText = String(pending).padStart(2, '0');
            document.querySelector('.stat-card.completed .stat-value').innerText = String(completed).padStart(2, '0');

            document.querySelector('.filter-tab[onclick*="all"] span').innerText = total;
            document.querySelector('.filter-tab[onclick*="upcoming"] span').innerText = pending + confirmed;
            document.querySelector('.filter-tab[onclick*="completed"] span').innerText = completed;
            document.querySelector('.filter-tab[onclick*="cancelled"] span').innerText = cancelled;
        }

        function getValidImageUrl(imagePath) {
            if (!imagePath) return '/EventManagementSystem/public/assets/images/placeholder.jpg';
            if (imagePath.startsWith('[')) {
                try {
                    const paths = JSON.parse(imagePath);
                    if (paths && paths.length > 0) {
                        return (paths[0].startsWith('/')) ? paths[0] : '/EventManagementSystem/public/assets/images/events/' + paths[0];
                    }
                } catch (e) {
                    console.error('Error parsing image_path JSON', e);
                }
            }
            return (imagePath.startsWith('/')) ? imagePath : '/EventManagementSystem/public/assets/images/events/' + imagePath;
        }

        function fetchBookings() {
            if (!window.emsApi) return;
            window.emsApi.apiFetch('/api/v1/bookings')
                .then(res => {
                    if (res.success && res.data && res.data.items) {
                        // Filter out concerts as per original logic
                        bookingsData = res.data.items.filter(b => (b.event_category || '').trim().toLowerCase() !== 'concert');

                        // Sort by created_at DESC
                        bookingsData.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                        // Refresh UI
                        updateStats();
                        applyFilter(currentFilterType);
                    }
                })
                .catch(err => {
                    console.error('Failed to load bookings:', err);
                    document.getElementById('bookingsList').innerHTML = '<div class="empty-state" style="color:red;">Failed to load bookings.</div>';
                });
        }

        function renderBookingsList() {
            const listContainer = document.getElementById('bookingsList');
            const pagControls = document.getElementById('paginationControls');
            listContainer.innerHTML = '';

            if (filteredBookings.length === 0) {
                listContainer.innerHTML = '<div class="empty-state">No bookings found in this category.</div>';
                document.getElementById('sidebarPanel').style.display = 'none';
                pagControls.style.display = 'none';
                return;
            }

            const totalPages = Math.ceil(filteredBookings.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentItems = filteredBookings.slice(startIndex, endIndex);

            currentItems.forEach((booking, idx) => {
                const actualIndex = startIndex + idx; // To map back to filteredBookings if needed

                const eSnap = safeParse(booking.event_snapshot);
                const bListTitle = eSnap?.title || booking.event_title;
                const bListCat = eSnap?.category || booking.event_category || 'Event';

                let rawImg = eSnap?.image_path || booking.event_image || '';
                let bListImg = getValidImageUrl(rawImg);

                let catStyle = '';
                if (bListCat === 'Exhibition' || bListCat.toLowerCase() === 'education') {
                    catStyle = 'background: #e5e7eb; color: #4b5563;';
                } else if (bListCat.toLowerCase() === 'music') {
                    catStyle = 'background: #fef08a; color: #854d0e;';
                }

                const isUpcoming = ['pending', 'confirmed'].includes(booking.status.toLowerCase());
                const safeTier = booking.package_tier ? booking.package_tier : 'standard';
                const packageLabel = (bListCat.toLowerCase() === 'concert') ? safeTier.charAt(0).toUpperCase() + safeTier.slice(1) + ' Tier' : safeTier.charAt(0).toUpperCase() + safeTier.slice(1) + ' Package';
                const guestLabel = (bListCat.toLowerCase() === 'concert') ? 'Tickets' : 'Guests';

                const html = `
                    <div class="b-item" onclick="selectBookingByObject(${booking.id}, this)">
                        <img src="${bListImg}" alt="Event Cover" class="b-img">
                        <div class="b-content">
                            <div>
                                <div class="b-top">
                                    <div class="b-title-wrap">
                                        <h3 class="b-title">${bListTitle}</h3>
                                        <span class="b-cat-badge" style="${catStyle}">${bListCat}</span>
                                    </div>
                                    <span class="b-status-badge status-${booking.status.toLowerCase()}">${booking.status.toUpperCase()}</span>
                                </div>
                                <div class="b-middle">
                                    <span><i class="fa-solid fa-address-card"></i> ${packageLabel}</span>
                                    <span><i class="fa-solid fa-user-group"></i> ${booking.guest_count} ${guestLabel}</span>
                                    <span><i class="fa-regular fa-calendar"></i> ${new Date(booking.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                </div>
                            </div>
                            <div class="b-bottom">
                                <div class="b-date-booked">Booked on: ${new Date(booking.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                                <div class="b-price-action">
                                    <span class="b-price">Rs. ${parseFloat(booking.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                                    <a href="/EventManagementSystem/public/client/bookings/view?id=${booking.id}" onclick="event.stopPropagation()" class="b-view-link">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', html);
            });

            renderPagination(totalPages);

            // Select first item of the page automatically
            document.getElementById('sidebarPanel').style.display = 'block';
            const firstItem = listContainer.querySelector('.b-item');
            if (firstItem) {
                selectBookingByObject(currentItems[0].id, firstItem);
            }
        }

        function renderPagination(totalPages) {
            const pagControls = document.getElementById('paginationControls');
            if (totalPages <= 1) {
                pagControls.style.display = 'none';
                return;
            }

            pagControls.style.display = 'flex';
            let html = '';

            // Previous Button
            html += `<button class="nav-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                        <i class="fa-solid fa-chevron-left"></i> Previous
                    </button>`;

            // Page Numbers Logic with Ellipsis
            const delta = 1; // Numbers on each side of current page
            const range = [];
            for (let i = Math.max(2, currentPage - delta); i <= Math.min(totalPages - 1, currentPage + delta); i++) {
                range.push(i);
            }

            if (currentPage - delta > 2) {
                range.unshift("...");
            }
            if (currentPage + delta < totalPages - 1) {
                range.push("...");
            }

            range.unshift(1);
            if (totalPages > 1) range.push(totalPages);

            range.forEach(p => {
                if (p === "...") {
                    html += `<span class="pagination-ellipsis">...</span>`;
                } else {
                    html += `<button class="page-num ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
                }
            });

            // Next Button
            html += `<button class="nav-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                        Next <i class="fa-solid fa-chevron-right"></i>
                    </button>`;

            pagControls.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            renderBookingsList();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function selectBookingByObject(id, element) {
            // If on mobile/tablet where sidebar is hidden, navigate to full details page
            if (window.innerWidth <= 1024) {
                window.location.href = `/EventManagementSystem/public/client/bookings/view?id=${id}`;
                return;
            }

            document.querySelectorAll('.b-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            const data = bookingsData.find(b => b.id == id);
            if (!data) return;

            // Snapshots
            const eSnap = safeParse(data.event_snapshot);
            const pSnap = safeParse(data.package_snapshot);

            // Populate Sidebar
            document.getElementById('sb-id').innerText = 'BK-' + String(data.id).padStart(3, '0');

            let rawImg = eSnap?.image_path || data.event_image || '';
            let imgUrl = getValidImageUrl(rawImg);
            document.getElementById('sb-img').src = imgUrl;

            const statusEl = document.getElementById('sb-status');
            statusEl.innerText = data.status.toUpperCase();
            statusEl.className = 'b-status-badge status-' + data.status.toLowerCase();

            document.getElementById('sb-event-title').innerText = eSnap?.title || data.event_title;

            document.getElementById('sb-price').innerText = 'Rs. ' + parseFloat(data.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 });

            // Derive package name
            const isConcert = (eSnap?.category || data.event_category || '').toLowerCase() === 'concert';
            let pLabel = isConcert ? 'SELECTED TIER' : 'SELECTED PACKAGE';
            const safeTier2 = data.package_tier ? data.package_tier : 'standard';
            let pName = safeTier2.charAt(0).toUpperCase() + safeTier2.slice(1) + (isConcert ? ' Tier' : ' Package');
            let pDesc = isConcert ? 'Allows entry to the event.' : 'Includes selected access & features.';

            if (pSnap && pSnap.description) {
                pDesc = pSnap.description;
            }

            document.querySelector('.dp-pkg-label').innerText = pLabel;
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

            document.getElementById('sb-time').innerText = formatTime(data.event_date);

            // Handle Payment Breakdown
            const total = parseFloat(data.total_amount);
            const payStatus = (data.payment_status || 'unpaid').toLowerCase();

            if (isConcert) {
                const concertStatusEl = document.getElementById('sb-concert-pay-status');
                if (concertStatusEl) {
                    concertStatusEl.innerText = payStatus.toUpperCase().replace('_', ' ');
                    concertStatusEl.style.color = (payStatus === 'paid') ? '#10b981' : '#f59e0b';
                }
            } else {
                const advance = total * 0.5;
                const balance = total * 0.5;

                const advanceValEl = document.getElementById('sb-advance-val');
                const balanceValEl = document.getElementById('sb-balance-val');

                if (advanceValEl) {
                    advanceValEl.innerText = 'Rs. ' + advance.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    if (payStatus !== 'unpaid') {
                        advanceValEl.innerHTML += ' <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>';
                        advanceValEl.style.color = '#10b981';
                    } else {
                        advanceValEl.style.color = '#1e293b';
                    }
                }

                if (balanceValEl) {
                    balanceValEl.innerText = 'Rs. ' + balance.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    if (payStatus === 'paid') {
                        balanceValEl.innerHTML += ' <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>';
                        balanceValEl.style.color = '#10b981';
                    } else {
                        balanceValEl.style.color = '#1e293b';
                    }
                }
            }

            // Handle Pay Now & Print Ticket Buttons
            const payBtn = document.getElementById('sb-pay-btn');
            const printBtn = document.getElementById('sb-print-btn');

            if (payBtn) {
                if (payStatus === 'unpaid' && (data.status === 'pending' || data.status === 'confirmed')) {
                    payBtn.href = '/EventManagementSystem/public/client/payment/checkout?booking_id=' + data.id;
                    payBtn.innerHTML = isConcert ? '<i class="fa-solid fa-credit-card"></i> Pay for Ticket Online' : '<i class="fa-solid fa-credit-card"></i> Pay 50% Advance Online';
                    payBtn.style.display = 'flex';
                } else {
                    payBtn.style.display = 'none';
                }
            }

            if (printBtn) {
                if (isConcert && (data.status === 'confirmed' || data.status === 'completed')) {
                    printBtn.href = '/EventManagementSystem/public/client/ticket?id=' + data.id;
                    printBtn.style.display = 'flex';
                } else {
                    printBtn.style.display = 'none';
                }
            }

            // Handle Cancel Button
            let cancelForm = document.getElementById('cancel-booking-form');
            if (cancelForm) {
                const bStatus = (data.status || '').toLowerCase();
                const paySt = (data.payment_status || 'unpaid').toLowerCase();

                const isLocked = (bStatus === 'confirmed' && paySt !== 'unpaid');
                const isActive = (bStatus === 'pending' || bStatus === 'confirmed');

                if (isActive && !isLocked) {
                    document.getElementById('cancel-booking-id').value = data.id;
                    cancelForm.style.display = 'block';
                } else {
                    cancelForm.style.display = 'none';
                }
            }
        }

        function applyFilter(filterType) {
            currentFilterType = filterType;
            currentPage = 1;

            if (filterType === 'all') {
                filteredBookings = [...bookingsData];
            } else if (filterType === 'upcoming') {
                filteredBookings = bookingsData.filter(b => ['pending', 'confirmed'].includes(b.status.toLowerCase()));
            } else {
                filteredBookings = bookingsData.filter(b => b.status.toLowerCase() === filterType);
            }

            renderBookingsList();
        }

        function filterBookings(filterType, element) {
            // Update Tabs
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            applyFilter(filterType);
        }

        // Initialize first item on load
        document.addEventListener("DOMContentLoaded", function () {
            // Check for success notifications
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('booking_success')) {
                // Show a nice notification (could be a toast, but using alert for simplicity in this base)
                setTimeout(() => {
                    alert("✅ Event Reserved! Your booking is saved, but please remember to pay the 50% advance soon to secure your date. You can pay anytime from the sidebar.");
                }, 500);

                // Clear the URL param without refreshing
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.hash;
                window.history.pushState({ path: newUrl }, '', newUrl);
            }

            fetchBookings();
        });

    </script>

    <?php include 'partials/feedback_popup.php'; ?>
<?php include 'partials/footer.php'; ?>