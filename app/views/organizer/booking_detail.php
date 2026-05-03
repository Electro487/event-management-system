<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail | Organizer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking-detail.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            font-weight: 600;
            color: #1e293b;
        }
    </style>
</head>
<body>

    <div id="loadingOverlay" class="loading-overlay">Loading booking details...</div>

    <?php 
        $activePage = ($_GET['source'] ?? '') === 'tickets' ? 'tickets' : 'bookings';
        include_once __DIR__ . "/partials/sidebar.php"; 
    ?>

    <main class="main-content" id="mainContent" style="display: none;">
        <header class="detail-header">
            <div class="header-left-info">
                <div class="breadcrumb-container" id="breadcrumbContainer">
                    <a href="/EventManagementSystem/public/organizer/bookings" class="bc-link">Bookings</a> 
                    <span class="separator">❯</span> 
                    <span class="bc-link current">Booking Detail</span>
                </div>
                <div class="title-section">
                    <h1>Booking <span id="bookingIdDisplay">#BK-000</span> <span id="statusBadge" class="badge-status">STATUS</span></h1>
                    <p class="sub-title" id="eventTitleDisplay">Event Name - Package</p>
                </div>
            </div>

            <div class="header-right-actions">
                <a href="/EventManagementSystem/public/organizer/bookings" class="back-link" id="backToParentLink">
                    <i class="fa-solid fa-arrow-left"></i> Back to Bookings
                </a>
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
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                </div>
            </div>
        </header>

        <div class="detail-grid">
            <div class="grid-left-col">
                <!-- Client Card -->
                <div class="card client-card">
                    <div class="card-header-small">
                        <h4>Client Information</h4>
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="client-info-main">
                        <div id="clientAvatar" class="client-avatar-large">
                            <!-- Initials or Image -->
                        </div>
                        <div class="client-details">
                            <h3 id="clientName">Client Name</h3>
                            <div class="contact-row">
                                <span id="clientPhone"><i class="fa-solid fa-phone"></i> +977 0000000000</span>
                                <span id="clientEmail"><i class="fa-solid fa-envelope"></i> email@example.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Overview Card -->
                <div class="card event-overview-card">
                    <div class="event-hero">
                        <img id="eventHeroImg" src="/EventManagementSystem/public/assets/images/placeholder.jpg" alt="Event">
                        <div class="event-hero-overlay">
                            <span id="categoryChip" class="cat-chip">Category</span>
                            <h2 id="eventHeroTitle">Event Title</h2>
                            <p id="eventHeroDesc" class="event-hero-desc">Event Description</p>
                        </div>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="stat-item">
                            <label>Date</label>
                            <span id="eventDateDisplay">Date</span>
                        </div>
                        <div class="stat-item">
                            <label>Check-in</label>
                            <span id="checkinTimeDisplay">10:00 AM</span>
                        </div>
                        <div class="stat-item">
                            <label>Guests</label>
                            <span id="guestCountDisplay">0 Persons</span>
                        </div>
                        <div class="stat-item">
                            <label>Venue</label>
                            <span id="venueNameDisplay">Venue Name</span>
                        </div>
                        <div class="stat-item" style="grid-column: span 2;">
                            <label>Location</label>
                            <span id="venueLocationDisplay">Venue Location</span>
                        </div>
                    </div>
                </div>

                <!-- Package Details Card -->
                <div class="card package-card">
                    <div class="pkg-header">
                        <div class="pkg-title">
                            <h3 id="packageTierTitle">Tier Package</h3>
                            <p id="packageOverview">Package Overview</p>
                        </div>
                        <div class="pkg-price">
                            <span class="lbl">Price</span>
                            <span id="packagePriceDisplay" class="amt">Rs. 0</span>
                        </div>
                    </div>

                    <div id="featuresGrid" class="features-grid">
                        <!-- Features Injected -->
                    </div>
                </div>
            </div>

            <div class="grid-right-col">
                <!-- Booking Journey -->
                <div class="card">
                    <div class="card-header-small"><h4>Booking Journey</h4></div>
                    <div id="timeline" class="timeline">
                        <!-- Timeline Items Injected -->
                    </div>
                </div>

                <!-- Manage Status -->
                <div id="manageStatusCard" class="card">
                    <div class="card-header-small">
                        <h4>Manage Status</h4>
                        <span id="manageStatusBadge" class="badge-status">STATUS</span>
                    </div>
                    
                    <div id="actionButtons" class="action-buttons-container" style="margin-top: 15px;">
                        <!-- Action Buttons Injected -->
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="card">
                    <div class="card-header-small"><h4>Financial Summary</h4></div>
                    <div class="finance-row">
                        <span>Total Amount</span>
                        <span id="totalAmountDisplay">Rs. 0</span>
                    </div>

                    <div class="finance-row">
                        <span>Advance (50% Online)</span>
                        <span id="advanceDisplay" style="font-weight: 600;">
                            Rs. 0 
                        </span>
                    </div>

                    <div class="finance-row">
                        <span>Remaining (50% Cash)</span>
                        <span id="balanceDisplay" style="font-weight: 600;">
                            Rs. 0
                        </span>
                    </div>
                    
                    <div id="txRow" class="finance-row" style="display: none; background: #f8fafc; padding: 10px; border-radius: 6px; margin-top: 10px; border: 1px solid #e2e8f0;">
                        <span style="font-size: 11px; color: #64748b;">TX ID</span>
                        <span id="transactionIdDisplay" style="font-family: monospace; font-size: 11px; color: #1e293b; word-break: break-all;">-</span>
                    </div>

                    <div class="payment-status">
                        <span class="lbl">Current Status</span>
                        <span id="payStatusBadge" class="val">STATUS</span>
                    </div>

                    <div class="policy-note" style="margin-top:15px; padding:10px; background:#f8fafc; border-radius:8px; border:1px dashed #cbd5e1;">
                        <span style="font-size:11px; color:#64748b; display:block; line-height:1.4;">
                            <i class="fa-solid fa-circle-info"></i> <b>Policy:</b> Advanced payments are non-refundable upon cancellation. Balance is to be collected in cash by or on the event day.
                        </span>
                    </div>
                </div>

                <button class="btn-manage btn-message" disabled><i class="fa-regular fa-paper-plane"></i> Send Message to Client</button>
            </div>
        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const mainContent = document.getElementById('mainContent');
            const bookingId = new URLSearchParams(window.location.search).get('id');

            if (!bookingId) {
                loadingOverlay.textContent = "Error: No booking ID provided.";
                return;
            }

            if (!window.emsApi) {
                loadingOverlay.textContent = "Error: API client not loaded.";
                return;
            }

            async function loadBooking() {
                try {
                    const res = await window.emsApi.apiFetch(`/api/v1/bookings/${bookingId}`);
                    const b = res.data?.booking;
                    if (!b) throw new Error("Booking not found");

                    populateUI(b);
                    loadingOverlay.style.display = 'none';
                    mainContent.style.display = 'block';
                } catch (err) {
                    console.error(err);
                    loadingOverlay.innerHTML = `<div style="text-align:center; color:red;">
                        <p>Error loading booking: ${err.message}</p>
                        <a href="/EventManagementSystem/public/organizer/bookings" style="color:blue; text-decoration:underline;">Back to Bookings</a>
                    </div>`;
                }
            }

            function populateUI(b) {
                const status = (b.status || '').toLowerCase();
                const pStat = (b.payment_status || 'unpaid').toLowerCase();
                const eSnap = b.event_snapshot ? JSON.parse(b.event_snapshot) : null;
                const category = (eSnap?.category || b.event_category || '').toLowerCase().trim();
                const isConcert = (category === 'concert');

                // Update navigation if it's a concert
                if (isConcert) {
                    const backLink = document.getElementById('backToParentLink');
                    if (backLink) {
                        backLink.href = '/EventManagementSystem/public/organizer/tickets';
                        backLink.innerHTML = '<i class="fa-solid fa-arrow-left"></i> Back to Tickets';
                    }
                    const bcContainer = document.getElementById('breadcrumbContainer');
                    if (bcContainer) {
                        bcContainer.innerHTML = `
                            <a href="/EventManagementSystem/public/organizer/tickets" class="bc-link">Tickets</a> 
                            <span class="separator">❯</span> 
                            <span class="bc-link current">Ticket Detail</span>
                        `;
                    }
                    // Update Sidebar active state via JS
                    document.querySelectorAll('.sidebar nav a').forEach(a => {
                        a.classList.remove('active');
                        if (a.getAttribute('href').includes('/organizer/tickets')) {
                            a.classList.add('active');
                        }
                    });
                }

                const displayStatus = (b.display_status || status).toLowerCase();
                const fullName = b.full_name || b.client_user_name || 'Unknown Client';
                const tierKey = (b.package_tier || '').toLowerCase();
                const eventDateStr = b.event_date || b.event_start_date;
                const eventDate = new Date(eventDateStr);
                const today = new Date();
                today.setHours(0,0,0,0);

                // Header
                document.getElementById('bookingIdDisplay').textContent = `#BK-${bookingId.toString().padStart(3, '0')}`;
                const pSnap = b.package_snapshot ? JSON.parse(b.package_snapshot) : null;
                const eTitle = eSnap?.title || b.event_title;

                const statusBadge = document.getElementById('statusBadge');
                statusBadge.textContent = displayStatus.toUpperCase();
                statusBadge.className = `badge-status ${displayStatus}`;
                document.getElementById('eventTitleDisplay').textContent = `${eTitle} - ${b.package_tier} Package`;

                // Client Card
                const clientAvatar = document.getElementById('clientAvatar');
                clientAvatar.className = `client-avatar-large ${tierKey}-av`;
                if (b.client_profile_pic) {
                    clientAvatar.innerHTML = `<img src="${b.client_profile_pic}" alt="Client" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                } else {
                    let initials = fullName.substring(0,2).toUpperCase();
                    if (fullName.includes(' ')) {
                        const parts = fullName.split(' ');
                        initials = (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
                    }
                    clientAvatar.textContent = initials;
                }
                document.getElementById('clientName').textContent = fullName;
                document.getElementById('clientPhone').innerHTML = `<i class="fa-solid fa-phone"></i> ${b.phone || '+977 0000000000'}`;
                document.getElementById('clientEmail').innerHTML = `<i class="fa-solid fa-envelope"></i> ${b.email}`;

                // Event Overview
                let rawImg = eSnap?.image_path || b.event_image || '';
                let eImage = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                if (rawImg) {
                    eImage = (rawImg[0] === '/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
                }
                document.getElementById('eventHeroImg').src = eImage;
                document.getElementById('categoryChip').textContent = eSnap?.category || b.event_category;
                document.getElementById('eventHeroTitle').textContent = eTitle;
                document.getElementById('eventHeroDesc').textContent = b.event_description || 'Curating timeless moments...';
                document.getElementById('eventDateDisplay').textContent = eventDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                
                let checkin = b.checkin_time || '10:00 AM';
                if (/^\d{2}:\d{2}$/.test(checkin)) {
                    const [h, m] = checkin.split(':');
                    const hr = parseInt(h);
                    checkin = `${hr % 12 || 12}:${m} ${hr >= 12 ? 'PM' : 'AM'}`;
                }
                document.getElementById('checkinTimeDisplay').textContent = checkin;
                document.getElementById('guestCountDisplay').textContent = `${b.guest_count} Persons`;
                document.getElementById('venueNameDisplay').textContent = eSnap?.venue_name || b.venue_name || 'Royal Palace';
                document.getElementById('venueLocationDisplay').textContent = eSnap?.venue_location || b.venue_location || 'Bhaktapur';

                // Package
                document.getElementById('packageTierTitle').textContent = `${b.package_tier} Package`;
                const pkgData = pSnap || (JSON.parse(b.event_packages || '{}')[tierKey] || {});
                document.getElementById('packageOverview').textContent = pkgData.description || `Most popular for ${tierKey} events`;
                document.getElementById('packagePriceDisplay').textContent = `Rs. ${parseFloat(b.total_amount).toLocaleString()}`;

                const featuresGrid = document.getElementById('featuresGrid');
                const items = pkgData.items || [];
                if (items.length > 0) {
                    featuresGrid.innerHTML = items.map(it => `<div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>${it.title}</span></div>`).join('');
                } else {
                    featuresGrid.innerHTML = `
                        <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Basic Event Management</span></div>
                        <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Standard Decoration</span></div>
                        <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Venue Coordination</span></div>
                        <div class="feature-box"><i class="fa-solid fa-circle-check"></i> <span>Essential Refreshments</span></div>
                    `;
                }

                // Timeline
                const timeline = document.getElementById('timeline');
                const steps = [
                    { label: 'Received', desc: 'Booking received successfully', key: 'received' },
                    { label: 'Under Review', desc: status === 'cancelled' ? 'Booking cancelled' : 'Reviewing event details', key: 'review' },
                    { label: 'Confirmed', desc: (status === 'confirmed' || status === 'completed') ? 'Booking confirmed' : 'Awaiting confirmation', key: 'confirmed' },
                    { label: 'Event Day', desc: 'Scheduled for ' + eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }), key: 'event' },
                    { label: 'Completed', desc: (today > eventDate && status !== 'cancelled') ? 'Event successfully completed' : 'Pending event day', key: 'completed' }
                ];

                timeline.innerHTML = steps.map(s => {
                    let cls = '';
                    if (status !== 'cancelled') {
                        if (s.key === 'received' || s.key === 'review') cls = 'completed';
                        else if (s.key === 'confirmed' && (status === 'confirmed' || status === 'completed')) cls = 'completed';
                        else if (s.key === 'event') {
                            if (status === 'confirmed' || status === 'completed') {
                                if (today.getTime() === eventDate.getTime()) cls = 'active';
                                else if (today > eventDate) cls = 'completed';
                            }
                        } else if (s.key === 'completed') {
                            if ((status === 'confirmed' || status === 'completed') && today > eventDate) cls = 'completed';
                        }
                    }
                    return `
                        <div class="timeline-item ${cls}">
                            <div class="tl-dot"><div class="dot-inner"><i class="fa-solid fa-check"></i></div></div>
                            <div class="tl-content"><h5>${s.label}</h5><p>${s.desc}</p></div>
                        </div>
                    `;
                }).join('');

                // Manage Status
                const mBadge = document.getElementById('manageStatusBadge');
                mBadge.textContent = displayStatus.toUpperCase();
                mBadge.className = `badge-status ${displayStatus}`;
                
                // Finance calculations first to determine if advance is complete
                var total = parseFloat(b.total_amount);
                var paidTotal = parseFloat(b.paid_amount || 0);
                var advanceTarget = total * 0.5;
                var remainingAdvance = Math.max(0, advanceTarget - paidTotal);
                var isAdvanceComplete = (remainingAdvance <= 0.009) || (remainingAdvance < 50 && paidTotal > 0);

                const actionButtons = document.getElementById('actionButtons');
                const canConfirm = (pStat === 'paid' || isAdvanceComplete);
                const hasButtons = (displayStatus === 'pending' || (displayStatus === 'confirmed' && pStat !== 'paid'));
                
                if (!hasButtons) {
                    document.getElementById('manageStatusCard').classList.add('card-status-empty');
                    let emptyMsg = 'No actions available.';
                    let icon = 'fa-circle-info';
                    
                    if (status === 'completed') {
                        emptyMsg = 'Event Successfully Completed';
                        icon = 'fa-circle-check';
                    } else if (status === 'cancelled') {
                        emptyMsg = 'Booking Cancelled';
                        icon = 'fa-circle-xmark';
                    } else if (status === 'confirmed' && pStat === 'paid') {
                        emptyMsg = 'All Steps Done & Paid';
                        icon = 'fa-shield-check';
                    }
                    
                    actionButtons.innerHTML = `<div style="text-align:center; padding: 30px 0; color: var(--text-muted);">
                        <i class="fa-solid ${icon}" style="font-size: 32px; display: block; margin-bottom: 10px; color: var(--accent-color); opacity: 0.5;"></i>
                        <span style="font-weight: 500;">${emptyMsg}</span>
                    </div>`;
                } else {
                    document.getElementById('manageStatusCard').classList.remove('card-status-empty');
                    let btnsHtml = '';
                    
                    if (status === 'pending') {
                        btnsHtml += `
                            <button type="button" class="btn-manage btn-confirm" ${!canConfirm ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : `onclick="updateStatus('approve', 'Are you sure you want to CONFIRM this booking?')"`}>
                                <i class="fa-solid fa-circle-check"></i> 
                                ${canConfirm ? 'Confirm Booking' : 'Advance Payment Required'}
                            </button>
                            ${!canConfirm ? `<p style="color: #ef4444; font-size: 11px; margin-top: 8px; font-weight: 500; display: flex; align-items: center; gap: 5px;"><i class="fa-solid fa-triangle-exclamation"></i> Wait for client to pay 50% advance first.</p>` : ''}
                        `;
                    }
                    
                    if (status !== 'cancelled' && pStat === 'partially_paid') {
                        btnsHtml += `
                            <button type="button" class="btn-manage" style="background: #10b981; color: white; margin-top:10px;" onclick="updateStatus('mark-paid', 'Confirm that you have received the remaining 50% cash balance?')">
                                <i class="fa-solid fa-money-bill-check"></i> Mark as Fully Paid (Cash)
                            </button>
                        `;
                    }

                    if ((status === 'pending' || status === 'confirmed') && pStat !== 'paid') {
                        btnsHtml += `
                            <button type="button" class="btn-manage btn-cancel" style="margin-top:10px;" onclick="updateStatus('cancel', 'Are you sure you want to CANCEL this booking?')">
                                <i class="fa-solid fa-circle-xmark"></i> Cancel Booking
                            </button>
                        `;
                    }
                    actionButtons.innerHTML = btnsHtml;
                }

                // Finance
                // (Using variables already declared above for calculations)
                
                document.getElementById('totalAmountDisplay').textContent = `Rs. ${total.toLocaleString()}`;
                
                // Show Advance
                const displayPaidAdvance = isAdvanceComplete ? advanceTarget : paidTotal;
                document.getElementById('advanceDisplay').innerHTML = `Rs. ${displayPaidAdvance.toLocaleString()} / ${advanceTarget.toLocaleString()} ${isAdvanceComplete ? '<i class="fa-solid fa-check-circle" style="color:#10b981"></i>' : ''}`;
                document.getElementById('advanceDisplay').style.color = isAdvanceComplete ? '#10b981' : '#64748b';
                
                // Show Remaining (Balance)
                const balance = total - paidTotal;
                document.getElementById('balanceDisplay').innerHTML = `Rs. ${balance.toLocaleString()} ${pStat === 'paid' ? '<i class="fa-solid fa-check-circle" style="color:#10b981"></i>' : ''}`;
                document.getElementById('balanceDisplay').style.color = pStat === 'paid' ? '#10b981' : '#f59e0b';

                const payBadge = document.getElementById('payStatusBadge');
                if (pStat === 'paid') {
                    payBadge.textContent = 'FULLY PAID';
                    payBadge.className = 'val paid';
                } else if (isAdvanceComplete) {
                    payBadge.textContent = 'ADVANCE COMPLETE';
                    payBadge.className = 'val pending';
                    payBadge.style = 'background:#f0fdf4; color:#10b981; border:1px solid #dcfce7;';
                } else if (paidTotal > 0) {
                    payBadge.textContent = 'ADVANCE PARTIALLY PAID';
                    payBadge.className = 'val pending';
                    payBadge.style = 'background:#fff7ed; color:#f59e0b; border:1px solid #ffedd5;';
                } else {
                    payBadge.textContent = 'NOT PAID';
                    payBadge.className = 'val pending';
                }

                if (b.transaction_id) {
                    document.getElementById('txRow').style.display = 'flex';
                    document.getElementById('transactionIdDisplay').textContent = b.transaction_id;
                }
            }

            window.updateStatus = async (action, confirmText) => {
                if (confirmText && !confirm(confirmText)) return;
                try {
                    const method = (action === 'cancel') ? 'PATCH' : 'PATCH'; // Use PATCH for all per routes.php
                    const endpoint = `/api/v1/bookings/${bookingId}/${action}`;
                    await window.emsApi.apiFetch(endpoint, { method: 'PATCH' });
                    loadBooking(); // Reload data
                } catch (err) {
                    alert('Error: ' + err.message);
                }
            };

            loadBooking();
        });
    </script>
</body>
</html>
