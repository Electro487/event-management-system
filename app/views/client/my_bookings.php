<?php ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <!-- Base styles (navbar etc) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/bookings" class="active">My Bookings</a>
            <a href="/EventManagementSystem/public/client/tickets">My Tickets</a>
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
                <?php
                $initials = '';
                $nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
                foreach ($nameParts as $p) {
                    $initials .= strtoupper(substr($p, 0, 1));
                }
                if (strlen($initials) > 2)
                    $initials = substr($initials, 0, 2);
                ?>
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
                            <?php
                            $firstName = $nameParts[0] ?? '';
                            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                            ?>
                            <div class="pd-detail">
                                <label>FIRST NAME</label>
                                <div><?php echo htmlspecialchars($firstName); ?></div>
                            </div>
                            <div class="pd-detail">
                                <label>LAST NAME</label>
                                <div><?php echo htmlspecialchars($lastName); ?></div>
                            </div>
                            <div class="pd-detail">
                                <label>EMAIL ADDRESS</label>
                                <div><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
                            </div>

                            <a href="/EventManagementSystem/public/client/feedback" class="pd-rating-btn">
                                <i class="fa-solid fa-star"></i> Rating &amp; Feedback
                            </a>
                            <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <script>
                    function toggleProfileDropdown() {
                        const dropdown = document.getElementById('profile-dropdown');
                        dropdown.classList.toggle('show');
                    }

                    // Hide dropdown when clicking outside
                    document.addEventListener('click', function (event) {
                        const container = document.getElementById('profile-container');
                        if (container && !container.contains(event.target)) {
                            document.getElementById('profile-dropdown').classList.remove('show');
                        }
                    });

                    function uploadProfilePicture(input) {
                        if (input.files && input.files[0]) {
                            const formData = new FormData();
                            formData.append('profile_picture', input.files[0]);

                            if (window.emsApi) {
                                window.emsApi.apiFetch('/api/v1/auth/profile/picture', {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(data => {
                                        if (data.success) {
                                            const path = data.data?.path || data.path;
                                            // Update header avatar
                                            let headerIcon = document.getElementById('profile-icon');
                                            headerIcon.innerHTML = '<img src="' + path + '" style="width: 100%; height: 100%; object-fit: cover;" id="header-avatar">';

                                            // Update dropdown avatar
                                            let dropdownAvatar = document.querySelector('.pd-avatar');
                                            dropdownAvatar.innerHTML = '<img src="' + path + '" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">';

                                            // Add delete icon if not exists
                                            if (!document.querySelector('.pd-delete-icon')) {
                                                let avatarContainer = document.querySelector('.pd-avatar-container');
                                                let deleteBtn = document.createElement('div');
                                                deleteBtn.className = 'pd-delete-icon';
                                                deleteBtn.title = 'Remove Photo';
                                                deleteBtn.onclick = deleteProfilePicture;
                                                deleteBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                                                avatarContainer.appendChild(deleteBtn);
                                            }
                                        } else {
                                            alert(data.message || 'Error uploading image.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('API Error:', error);
                                        alert('An error occurred during upload: ' + error.message);
                                    });
                            } else {
                                fetch('/EventManagementSystem/public/client/profile/update', {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) location.reload();
                                        else alert(data.message || 'Error uploading image.');
                                    })
                                    .catch(error => alert('An error occurred.'));
                            }
                        }
                    }

                    function deleteProfilePicture() {
                        if (confirm('Are you sure you want to remove your profile picture?')) {
                            if (window.emsApi) {
                                window.emsApi.apiFetch('/api/v1/auth/profile/picture', {
                                    method: 'DELETE'
                                })
                                    .then(data => {
                                        if (data.success) {
                                            const initialsElement = '<span id="header-initials"><?php echo htmlspecialchars($initials); ?></span>';
                                            let headerIcon = document.getElementById('profile-icon');
                                            headerIcon.innerHTML = initialsElement;
                                            let dropdownAvatar = document.querySelector('.pd-avatar');
                                            dropdownAvatar.innerHTML = '<span id="dropdown-initials"><?php echo htmlspecialchars($initials); ?></span>';
                                            let deleteIcon = document.querySelector('.pd-delete-icon');
                                            if (deleteIcon) deleteIcon.remove();
                                        } else {
                                            alert('Error removing image.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('API Error:', error);
                                        alert('An error occurred: ' + error.message);
                                    });
                            } else {
                                fetch('/EventManagementSystem/public/client/profile/delete-picture', {
                                    method: 'POST'
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) location.reload();
                                        else alert('Error removing image.');
                                    })
                                    .catch(error => alert('An error occurred.'));
                            }
                        }
                    }
                </script>
            <?php endif; ?>
        </div>
    </header>

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
                <div class="dp-info-list" id="standard-payment-breakdown" style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
                    <div style="font-size: 11px; color: #64748b; font-weight: 700; margin-bottom: 12px; letter-spacing: 0.5px;">
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
                    style="display: none; background: #246A55; color: white; text-align: center; text-decoration: none; border: none; font-weight: 600;">
                    <i class="fa-solid fa-credit-card"></i> Pay 50% Advance Online
                </a>

                <!-- Print Ticket Button (Concerts Only) -->
                <a href="#" id="sb-print-btn" class="btn-send-msg" target="_blank"
                    style="display: none; background: #F59E0B; color: white; text-align: center; text-decoration: none; border: none; font-weight: 600;">
                    <i class="fa-solid fa-print"></i> Print Your Ticket
                </a>

                <button class="btn-send-msg" type="button"><i class="fa-regular fa-message"></i> Send
                    Message</button>
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
                
                const eSnap = booking.event_snapshot ? JSON.parse(booking.event_snapshot) : null;
                const bListTitle = eSnap?.title || booking.event_title;
                const bListCat = eSnap?.category || booking.event_category || 'Event';
                
                let rawImg = eSnap?.image_path || booking.event_image || '';
                let bListImg = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                if (rawImg) {
                    bListImg = (rawImg[0] === '/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
                }

                let catStyle = '';
                if (bListCat === 'Exhibition' || bListCat.toLowerCase() === 'education') {
                    catStyle = 'background: #e5e7eb; color: #4b5563;';
                } else if (bListCat.toLowerCase() === 'music') {
                    catStyle = 'background: #fef08a; color: #854d0e;';
                }

                const isUpcoming = ['pending', 'confirmed'].includes(booking.status.toLowerCase());
                const packageLabel = (bListCat.toLowerCase() === 'concert') ? booking.package_tier.charAt(0).toUpperCase() + booking.package_tier.slice(1) + ' Tier' : booking.package_tier.charAt(0).toUpperCase() + booking.package_tier.slice(1) + ' Package';
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
                                    <span><i class="fa-regular fa-calendar"></i> ${new Date(booking.event_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>
                                </div>
                            </div>
                            <div class="b-bottom">
                                <div class="b-date-booked">Booked on: ${new Date(booking.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</div>
                                <div class="b-price-action">
                                    <span class="b-price">Rs. ${parseFloat(booking.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                                    <a href="/EventManagementSystem/public/client/bookings/view?id=${booking.id}" class="b-view-link">View Details</a>
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
            
            html += `<button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                html += `<button onclick="goToPage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
            }
            
            html += `<button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`;
            
            pagControls.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            renderBookingsList();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function selectBookingByObject(id, element) {
            document.querySelectorAll('.b-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            const data = bookingsData.find(b => b.id === id);
            if (!data) return;

            // Snapshots
            const eSnap = data.event_snapshot ? JSON.parse(data.event_snapshot) : null;
            const pSnap = data.package_snapshot ? JSON.parse(data.package_snapshot) : null;

            // Populate Sidebar
            document.getElementById('sb-id').innerText = 'BK-' + String(data.id).padStart(3, '0');

            let rawImg = eSnap?.image_path || data.event_image || '';
            let imgUrl = '/EventManagementSystem/public/assets/images/placeholder.jpg';
            if (rawImg) {
                imgUrl = (rawImg[0] === '/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
            }
            document.getElementById('sb-img').src = imgUrl;

            const statusEl = document.getElementById('sb-status');
            statusEl.innerText = data.status.toUpperCase();
            statusEl.className = 'b-status-badge status-' + data.status.toLowerCase();

            document.getElementById('sb-event-title').innerText = eSnap?.title || data.event_title;

            document.getElementById('sb-price').innerText = 'Rs. ' + parseFloat(data.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 });

            // Derive package name
            const isConcert = (eSnap?.category || data.event_category || '').toLowerCase() === 'concert';
            let pLabel = isConcert ? 'SELECTED TIER' : 'SELECTED PACKAGE';
            let pName = data.package_tier.charAt(0).toUpperCase() + data.package_tier.slice(1) + (isConcert ? ' Tier' : ' Package');
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
                    payBtn.style.display = 'block';
                } else {
                    payBtn.style.display = 'none';
                }
            }

            if (printBtn) {
                if (isConcert && (data.status === 'confirmed' || data.status === 'completed')) {
                    printBtn.href = '/EventManagementSystem/public/client/ticket?id=' + data.id;
                    printBtn.style.display = 'block';
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
</body>

</html>