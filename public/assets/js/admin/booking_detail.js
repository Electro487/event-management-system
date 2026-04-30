/**
 * Admin Booking Detail API Integration
 */

document.addEventListener('DOMContentLoaded', () => {
    loadBookingDetail();
});

async function loadBookingDetail() {
    const bookingId = window.BOOKING_ID;
    if (!bookingId || !window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch(`/api/v1/bookings/${bookingId}`);
        if (res.success) {
            populateUI(res.data.booking);
        }
    } catch (err) {
        console.error('Failed to load booking detail:', err);
    }
}

function populateUI(booking) {
    const status = (booking.display_status || booking.status || 'pending').toLowerCase();
    
    // Header
    document.getElementById('booking-id-pad').innerText = booking.id.toString().padStart(3, '0');
    const statusBadge = document.getElementById('booking-status-badge');
    statusBadge.innerText = status.toUpperCase();
    statusBadge.className = `badge-status ${status}`;

    document.getElementById('event-title-display').innerText = booking.event_title;
    document.getElementById('package-tier-display').innerText = (booking.package_tier || 'Basic').charAt(0).toUpperCase() + (booking.package_tier || 'basic').slice(1).toLowerCase();

    // Client
    const clientName = booking.full_name || booking.client_user_name || 'Unknown Client';
    document.getElementById('client-name-display').innerText = clientName;
    document.getElementById('client-phone-display').innerText = booking.phone || '+977 9801234567';
    document.getElementById('client-email-display').innerText = booking.email || 'N/A';

    const avatarDisplay = document.getElementById('client-avatar-display');
    const tier = (booking.package_tier || 'basic').toLowerCase();
    avatarDisplay.className = `client-avatar-large ${tier}-av`;
    if (booking.client_profile_pic) {
        avatarDisplay.innerHTML = `<img src="${booking.client_profile_pic}" alt="Client" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">`;
    } else {
        let initials = clientName.match(/\b\w/g) || [];
        initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
        avatarDisplay.innerHTML = initials;
    }

    // Event Hero Image
    const heroImg = document.getElementById('event-hero-img');
    if (heroImg) heroImg.src = booking.event_image || '/EventManagementSystem/public/assets/images/placeholder.jpg';
    
    document.getElementById('event-category-display').innerText = booking.event_category || 'Event';
    document.getElementById('event-hero-title-display').innerText = booking.event_title || 'Untitled Event';
    document.getElementById('event-desc-display').innerText = booking.event_description || 'Curating timeless moments for your once-in-a-lifetime celebration with architectural precision.';

    // Quick Stats
    const eventDate = new Date(booking.event_date || booking.event_start_date);
    document.getElementById('event-date-display').innerText = eventDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('guest-count-display').innerText = `${booking.guest_count || 0} Persons`;
    document.getElementById('venue-name-display').innerText = booking.venue_name || 'Royal Palace';
    document.getElementById('venue-location-display').innerText = booking.venue_location || 'Bhaktapur';

    // Package
    document.getElementById('pkg-tier-name').innerText = `${(booking.package_tier || 'Basic').charAt(0).toUpperCase() + (booking.package_tier || 'basic').slice(1).toLowerCase()} Package`;
    document.getElementById('pkg-price-display').innerText = `Rs. ${parseFloat(booking.total_amount).toLocaleString()}`;

    // Features
    renderFeatures(booking);

    // Timeline
    renderTimeline(booking, eventDate);

    // Financials
    renderFinancials(booking);

    // Status Actions
    renderStatusActions(booking);
}

function renderFeatures(booking) {
    const container = document.getElementById('pkg-features-list');
    let features = [];
    let description = "Most popular for event planning";

    try {
        const allPackages = JSON.parse(booking.event_packages || '[]');
        const tier = (booking.package_tier || 'basic').toLowerCase();
        if (allPackages[tier]) {
            features = (allPackages[tier].items || []).map(i => i.title);
            description = allPackages[tier].description || description;
        }
    } catch (e) {
        console.error('Error parsing packages:', e);
    }

    document.getElementById('pkg-desc').innerText = description;

    if (!features.length) {
        features = ["Basic Event Management", "Standard Decoration", "Venue Coordination", "Essential Refreshments"];
    }

    container.innerHTML = features.map(f => `
        <div class="feature-box">
            <i class="fa-solid fa-circle-check"></i>
            <span>${f}</span>
        </div>
    `).join('');
}

function renderTimeline(booking, eventDate) {
    const container = document.getElementById('booking-timeline');
    const status = (booking.display_status || booking.status || 'pending').toLowerCase();
    const today = new Date();
    today.setHours(0,0,0,0);
    const eDate = new Date(eventDate);
    eDate.setHours(0,0,0,0);

    const steps = [
        { label: 'Received', desc: 'Booking received successfully', key: 'received' },
        { label: 'Under Review', desc: status === 'cancelled' ? 'Booking cancelled' : 'Reviewing event details', key: 'review' },
        { label: 'Confirmed', desc: (status === 'confirmed' || status === 'completed') ? 'Booking confirmed' : 'Awaiting confirmation', key: 'confirmed' },
        { label: 'Event Day', desc: `Scheduled for ${eDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`, key: 'event' },
        { label: 'Completed', desc: (today > eDate && status !== 'cancelled') ? 'Event successfully completed' : 'Pending event day', key: 'completed' }
    ];

    container.innerHTML = steps.map(step => {
        let cls = '';
        if (status !== 'cancelled') {
            if (step.key === 'received' || step.key === 'review') cls = 'completed';
            else if (step.key === 'confirmed') {
                if (status === 'confirmed' || status === 'completed') cls = 'completed';
            } else if (step.key === 'event') {
                if (status === 'confirmed' || status === 'completed') {
                    if (today.getTime() === eDate.getTime()) cls = 'active';
                    else if (today > eDate) cls = 'completed';
                }
            } else if (step.key === 'completed') {
                if (status === 'confirmed' || status === 'completed') {
                    if (today > eDate) cls = 'completed';
                }
            }
        }

        return `
            <div class="timeline-item ${cls}">
                <div class="tl-dot">
                    <div class="dot-inner"><i class="fa-solid fa-check"></i></div>
                </div>
                <div class="tl-content">
                    <h5>${step.label}</h5>
                    <p>${step.desc}</p>
                </div>
            </div>
        `;
    }).join('');
}

function renderFinancials(booking) {
    const total = parseFloat(booking.total_amount || 0);
    const paidTotal = parseFloat(booking.paid_amount || 0);
    const pStat = (booking.payment_status || 'unpaid').toLowerCase();
    
    const advanceTarget = total * 0.5;
    const remainingAdvance = Math.max(0, advanceTarget - paidTotal);
    const isAdvanceComplete = (remainingAdvance <= 0.009) || (remainingAdvance < 50 && paidTotal > 0);
    const balance = total - paidTotal;

    document.getElementById('finance-total-amount').innerText = `Rs. ${total.toLocaleString()}`;
    
    const advanceEl = document.getElementById('finance-advance-display');
    const displayPaidAdvance = isAdvanceComplete ? advanceTarget : paidTotal;
    advanceEl.style.color = isAdvanceComplete ? '#10b981' : '#64748b';
    advanceEl.style.fontWeight = '600';
    advanceEl.innerHTML = `Rs. ${displayPaidAdvance.toLocaleString()} / ${advanceTarget.toLocaleString()} ${isAdvanceComplete ? '<i class="fa-solid fa-check-circle"></i>' : ''}`;

    const remainingEl = document.getElementById('finance-remaining-display');
    remainingEl.style.color = pStat === 'paid' ? '#10b981' : '#f59e0b';
    remainingEl.style.fontWeight = '600';
    remainingEl.innerHTML = `Rs. ${balance.toLocaleString()} ${pStat === 'paid' ? '<i class="fa-solid fa-check-circle"></i>' : ''}`;

    const statusLabel = document.getElementById('finance-status-label');
    let label = 'NOT PAID';
    let cls = 'pending';
    let style = '';

    if (pStat === 'paid') {
        label = 'FULLY PAID';
        cls = 'paid';
    } else if (isAdvanceComplete) {
        label = 'ADVANCE COMPLETE';
        cls = 'pending';
        style = 'background:#f0fdf4; color:#10b981; border:1px solid #dcfce7;';
    } else if (paidTotal > 0) {
        label = 'ADVANCE PARTIALLY PAID';
        cls = 'pending';
        style = 'background:#fff7ed; color:#f59e0b; border:1px solid #ffedd5;';
    }

    statusLabel.innerText = label;
    statusLabel.className = `val ${cls}`;
    if (style) statusLabel.style.cssText = style;

    // Attach to window for use in action buttons
    window.CURRENT_BOOKING_ADVANCE_COMPLETE = isAdvanceComplete;
}

function renderStatusActions(booking) {
    const container = document.getElementById('status-actions-container');
    const status = (booking.status || 'pending').toLowerCase();
    const pStat = (booking.payment_status || 'unpaid').toLowerCase();
    
    const canApprove = window.CURRENT_BOOKING_ADVANCE_COMPLETE || pStat === 'paid';
    const hasButtons = (status === 'pending' || (status === 'confirmed' && pStat !== 'paid'));

    const manageBadge = document.getElementById('manage-status-badge');
    manageBadge.innerText = (booking.display_status || status).toUpperCase();
    manageBadge.className = `badge-status ${booking.display_status || status}`;

    if (!hasButtons) {
        container.innerHTML = `
            <div class="status-centered-box" style="text-align: center; padding: 20px 0;">
                <span class="badge-status-lg ${booking.display_status || status}" style="padding: 12px 25px; font-size: 16px; border-radius: 50px;">
                    ${(booking.display_status || status).toUpperCase()}
                </span>
            </div>
        `;
        return;
    }

    let html = '';

    if (status === 'pending') {
        html += `
            <button type="button" class="btn-manage btn-confirm" ${!canApprove ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : `onclick="updateBookingStatus('approve')"`}>
                <i class="fa-solid fa-circle-check"></i> 
                ${canApprove ? 'Confirm Booking' : 'Advance Payment Required'}
            </button>
            ${!canApprove ? `
                <p style="color: #ef4444; font-size: 11px; margin-top: 8px; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> 
                    Wait for client to pay 50% advance first.
                </p>
            ` : ''}
        `;
    }

    if (status !== 'cancelled' && pStat === 'partially_paid') {
        html += `
            <button type="button" class="btn-manage" style="background: #10b981; color: white; margin-top: 10px;" onclick="updateBookingStatus('mark-paid')">
                <i class="fa-solid fa-money-bill-check"></i> Mark as Fully Paid (Cash)
            </button>
        `;
    }

    if ((status === 'pending' || status === 'confirmed') && pStat !== 'paid') {
        html += `
            <button type="button" class="btn-manage btn-cancel" style="margin-top: 10px;" onclick="updateBookingStatus('cancel')">
                <i class="fa-solid fa-circle-xmark"></i> Cancel Booking
            </button>
        `;
    }

    container.innerHTML = html;
}

async function updateBookingStatus(action) {
    const bookingId = window.BOOKING_ID;
    let confirmMsg = "Are you sure?";
    let url = "";
    let method = "PATCH";

    if (action === 'approve') {
        confirmMsg = "Are you sure you want to CONFIRM this booking?";
        url = `/api/v1/bookings/${bookingId}/approve`;
    } else if (action === 'cancel') {
        confirmMsg = "Are you sure you want to CANCEL this booking? This action cannot be undone.";
        url = `/api/v1/bookings/${bookingId}/cancel`;
    } else if (action === 'mark-paid') {
        confirmMsg = "Confirm that you have received the remaining 50% cash balance for this booking?";
        url = `/api/v1/bookings/${bookingId}/mark-paid`;
    }

    if (!confirm(confirmMsg)) return;

    try {
        const res = await window.emsApi.apiFetch(url, { method });
        if (res.success) {
            loadBookingDetail(); // Refresh
        } else {
            alert(res.message || 'Action failed.');
        }
    } catch (err) {
        console.error(`Error during ${action}:`, err);
    }
}
