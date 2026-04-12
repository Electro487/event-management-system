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
                        <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    $initials = '';
                    $nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
                    foreach($nameParts as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                    }
                    if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if(!empty($_SESSION['user_profile_pic'])): ?>
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
                                    <?php if(!empty($_SESSION['user_profile_pic'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">
                                    <?php else: ?>
                                        <span id="dropdown-initials"><?php echo htmlspecialchars($initials); ?></span>
                                    <?php endif; ?>
                                </div>
                                <label for="profile_picture_upload" class="pd-edit-icon" title="Change Photo">
                                    <i class="fa-solid fa-pen"></i>
                                </label>
                                <?php if(!empty($_SESSION['user_profile_pic'])): ?>
                                    <div class="pd-delete-icon" onclick="deleteProfilePicture()" title="Remove Photo">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="profile_picture_upload" accept="image/*" style="display: none;" onchange="uploadProfilePicture(this)">
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
                            <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            <span class="pd-role"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Client')); ?></span>
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
                document.addEventListener('click', function(event) {
                    const container = document.getElementById('profile-container');
                    if (container && !container.contains(event.target)) {
                        document.getElementById('profile-dropdown').classList.remove('show');
                    }
                });

                function uploadProfilePicture(input) {
                    if (input.files && input.files[0]) {
                        const formData = new FormData();
                        formData.append('profile_picture', input.files[0]);
                        
                        fetch('/EventManagementSystem/public/client/profile/update', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update header avatar
                                let headerIcon = document.getElementById('profile-icon');
                                headerIcon.innerHTML = '<img src="' + data.path + '" style="width: 100%; height: 100%; object-fit: cover;" id="header-avatar">';
                                
                                // Update dropdown avatar
                                let dropdownAvatar = document.querySelector('.pd-avatar');
                                dropdownAvatar.innerHTML = '<img src="' + data.path + '" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">';

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
                            console.error('Error:', error);
                            alert('An error occurred during upload.');
                        });
                    }
                }

                function deleteProfilePicture() {
                    if (confirm('Are you sure you want to remove your profile picture?')) {
                        fetch('/EventManagementSystem/public/client/profile/delete-picture', {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const initialsElement = '<span id="header-initials"><?php echo htmlspecialchars($initials); ?></span>';
                                
                                // Update header avatar
                                let headerIcon = document.getElementById('profile-icon');
                                headerIcon.innerHTML = initialsElement;
                                
                                // Update dropdown avatar
                                let dropdownAvatar = document.querySelector('.pd-avatar');
                                dropdownAvatar.innerHTML = '<span id="dropdown-initials"><?php echo htmlspecialchars($initials); ?></span>';
                                
                                // Remove delete icon if exists
                                let deleteIcon = document.querySelector('.pd-delete-icon');
                                if (deleteIcon) deleteIcon.remove();
                            } else {
                                alert('Error removing image.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred.');
                        });
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
            <div class="booking-list" id="bookingsList">
                <?php if (empty($bookings)): ?>
                    <div class="empty-state">No bookings found in this category.</div>
                <?php else: ?>
                    <?php foreach ($bookings as $index => $booking):
                        $isUpcoming = in_array($booking['status'], ['pending', 'confirmed']) ? 'true' : 'false';
                        $catStyle = $booking['event_category'] == 'Exhibition' || strtolower($booking['event_category']) == 'education' ? 'background: #e5e7eb; color: #4b5563;' : '';
                        if (strtolower($booking['event_category']) == 'music') {
                            $catStyle = 'background: #fef08a; color: #854d0e;';
                        }
                        ?>
                        <div class="b-item" data-status="<?php echo $booking['status']; ?>"
                            data-upcoming="<?php echo $isUpcoming; ?>" data-index="<?php echo $index; ?>"
                            onclick="selectBooking(<?php echo $index; ?>, this)">

                            <?php $image = !empty($booking['event_image']) ? $booking['event_image'] : '/EventManagementSystem/public/assets/images/placeholder.jpg'; ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Event Cover" class="b-img">

                            <div class="b-content">
                                <div>
                                    <div class="b-top">
                                        <div class="b-title-wrap">
                                            <h3 class="b-title"><?php echo htmlspecialchars($booking['event_title']); ?></h3>
                                            <span class="b-cat-badge"
                                                style="<?php echo $catStyle; ?>"><?php echo htmlspecialchars($booking['event_category'] ?: 'Event'); ?></span>
                                        </div>
                                        <span class="b-status-badge status-<?php echo htmlspecialchars($booking['status']); ?>">
                                            <?php echo strtoupper($booking['status']); ?>
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
                                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                    </div>
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
                <div class="dp-info-list" style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
                    <div style="font-size: 11px; color: #64748b; font-weight: 700; margin-bottom: 12px; letter-spacing: 0.5px;">PAYMENT BREAKDOWN (50/50 POLICY)</div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-size: 13px; color: #475569;">Advance (50% Online)</span>
                        <span id="sb-advance-val" style="font-size: 13px; font-weight: 600; color: #1e293b;">Rs. 0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 13px; color: #475569;">Remaining (50% Cash)</span>
                        <span id="sb-balance-val" style="font-size: 13px; font-weight: 600; color: #1e293b;">Rs. 0.00</span>
                    </div>
                </div>

                <div class="dp-info-list" style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-bottom: 20px;">
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
                    <!-- Pay Now Button (Hidden by default, shown via JS) -->
                    <a href="#" id="sb-pay-btn" class="btn-send-msg" 
                       style="display: none; background: #246A55; color: white; text-align: center; text-decoration: none; border: none; font-weight: 600;">
                        <i class="fa-solid fa-credit-card"></i> Pay 50% Advance Online
                    </a>

                    <button class="btn-send-msg" type="button"><i class="fa-regular fa-message"></i> Send
                        Message</button>
                    <form id="cancel-booking-form" action="/EventManagementSystem/public/client/bookings/cancel"
                        method="POST" style="margin:0; display: none;">
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
        // Pass PHP data to JS for dynamic sidebar
        const bookingsData = <?php echo json_encode($bookings); ?>;

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

        function selectBooking(index, element) {
            // Highlight active card
            document.querySelectorAll('.b-item').forEach(el => el.classList.remove('active'));
            if (element) {
                element.classList.add('active');
            }

            const data = bookingsData[index];
            if (!data) return;

            // Populate Sidebar
            document.getElementById('sb-id').innerText = 'BK-' + String(data.id).padStart(3, '0');

            let imgUrl = data.event_image ? data.event_image : '/EventManagementSystem/public/assets/images/placeholder.jpg';
            document.getElementById('sb-img').src = imgUrl;

            const statusEl = document.getElementById('sb-status');
            statusEl.innerText = data.status.toUpperCase();
            statusEl.className = 'b-status-badge status-' + data.status;

            document.getElementById('sb-event-title').innerText = data.event_title;

            document.getElementById('sb-price').innerText = 'Rs. ' + parseFloat(data.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 });

            // Derive package name
            let pName = data.package_tier.charAt(0).toUpperCase() + data.package_tier.slice(1) + ' Package';
            let pDesc = 'Includes selected access & features.';

            // Try getting exact package data from JSON decoded array
            if (data.packages_data && data.packages_data[data.package_tier]) {
                const storedPkg = data.packages_data[data.package_tier];
                if (storedPkg.description) {
                    pDesc = storedPkg.description;
                }
            }

            document.getElementById('sb-pkg-name').innerText = pName;
            document.getElementById('sb-pkg-desc').innerText = pDesc;

            document.getElementById('sb-org-name').innerText = data.organizer_name || 'Event Organizer';

            let locName = data.venue_name || 'Convention Center';
            let locAddr = data.venue_location || 'Address TBD';
            if (!data.venue_name && data.venue_location) {
                locName = data.venue_location;
                locAddr = "Local Venue";
            }
            document.getElementById('sb-loc-name').innerText = locName;
            document.getElementById('sb-loc-address').innerText = locAddr;

            document.getElementById('sb-time').innerText = formatTime(data.event_date);

            // Handle Payment Breakdown
            const total = parseFloat(data.total_amount);
            const advance = total * 0.5;
            const balance = total * 0.5;
            const payStatus = (data.payment_status || 'unpaid').toLowerCase();

            document.getElementById('sb-advance-val').innerText = 'Rs. ' + advance.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('sb-balance-val').innerText = 'Rs. ' + balance.toLocaleString(undefined, { minimumFractionDigits: 2 });

            const advanceEl = document.getElementById('sb-advance-val');
            const balanceEl = document.getElementById('sb-balance-val');

            if (payStatus !== 'unpaid') {
                advanceEl.innerHTML = 'Rs. ' + advance.toLocaleString(undefined, { minimumFractionDigits: 2 }) + ' <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>';
                advanceEl.style.color = '#10b981';
            } else {
                advanceEl.style.color = '#1e293b';
            }

            if (payStatus === 'paid') {
                balanceEl.innerHTML = 'Rs. ' + balance.toLocaleString(undefined, { minimumFractionDigits: 2 }) + ' <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>';
                balanceEl.style.color = '#10b981';
            } else {
                balanceEl.style.color = '#1e293b';
            }

            // Handle Pay Now Button
            const payBtn = document.getElementById('sb-pay-btn');
            if (payBtn) {
                // Only show Pay Now if they haven't even paid the advance yet
                if (payStatus === 'unpaid' && (data.status === 'pending' || data.status === 'confirmed')) {
                    payBtn.href = '/EventManagementSystem/public/client/payment/checkout?booking_id=' + data.id;
                    payBtn.style.display = 'block';
                } else {
                    payBtn.style.display = 'none';
                }
            }

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

        function filterBookings(filterType, element) {
            // Update Tabs
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            // Filter List
            const items = document.querySelectorAll('.b-item');
            let visibleCount = 0;
            let firstVisibleIdx = -1;
            let firstVisibleEl = null;

            items.forEach(item => {
                const status = item.getAttribute('data-status');
                const upcoming = item.getAttribute('data-upcoming');
                const idx = item.getAttribute('data-index');

                let show = false;

                if (filterType === 'all') show = true;
                else if (filterType === 'upcoming' && upcoming === 'true') show = true;
                else if (status === filterType) show = true;

                if (show) {
                    item.style.display = 'flex';
                    visibleCount++;
                    if (firstVisibleIdx === -1) {
                        firstVisibleIdx = idx;
                        firstVisibleEl = item;
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            const noData = document.querySelector('.empty-state');
            const sidebar = document.getElementById('sidebarPanel');

            if (visibleCount === 0) {
                if (!noData && document.getElementById('bookingsList')) {
                    document.getElementById('bookingsList').innerHTML += '<div class="empty-state">No bookings found in this category.</div>';
                } else if (noData) {
                    noData.style.display = 'block';
                }
                sidebar.style.display = 'none';
            } else {
                if (noData) noData.style.display = 'none';
                sidebar.style.display = 'block';

                // Auto select first visible
                if (firstVisibleIdx !== -1) {
                    selectBooking(firstVisibleIdx, firstVisibleEl);
                }
            }
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

            const firstItem = document.querySelector('.b-item');
            if (firstItem) {
                selectBooking(firstItem.getAttribute('data-index'), firstItem);
            }
        });

    </script>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>

</html>
