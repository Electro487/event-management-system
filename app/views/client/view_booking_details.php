<?php
// Snapshots
$eSnap = !empty($booking['event_snapshot']) ? json_decode($booking['event_snapshot'], true) : null;
$pSnap = !empty($booking['package_snapshot']) ? json_decode($booking['package_snapshot'], true) : null;

$rawBg = !empty($eSnap['image_path']) ? $eSnap['image_path'] : (!empty($booking['event_image']) ? $booking['event_image'] : '');
$bgImage = !empty($rawBg) ? ($rawBg[0] === '/' ? $rawBg : '/EventManagementSystem/public/assets/images/events/' . $rawBg) : '/EventManagementSystem/public/assets/images/placeholder.jpg';

$eventTitle = $eSnap['title'] ?? ($booking['event_title'] ?? 'Event');
$eventCategory = $eSnap['category'] ?? ($booking['event_category'] ?: 'Event');
$venueName = $eSnap['venue_name'] ?? ($booking['venue_name'] ?: 'Venue TBD');
$venueLocation = $eSnap['venue_location'] ?? ($booking['venue_location'] ?: 'Address will be confirmed shortly.');

$displayStatus = $booking['display_status'] ?? strtolower($booking['status']);
$statusStr = strtoupper($displayStatus);
$statusClass = "status-" . strtolower($displayStatus);

// Parse package features
$selectedPackage = $pSnap ?? ($selectedPackage ?? []);
$items = $selectedPackage['items'] ?? [];
if (empty($items)) {
    if ($booking['package_tier'] == 'premium') {
        $items = [['title' => 'Exclusive Catering & Decor'], ['title' => 'Premium 5-course meal'], ['title' => 'Luxury imported floral arrangements']];
    } else if ($booking['package_tier'] == 'standard') {
        $items = [['title' => 'Full Venue Coordination'], ['title' => 'Premium Floral Arrangement'], ['title' => 'Standard Catering (150 guests)'], ['title' => 'Live String Quartet']];
    } else {
        $items = [['title' => 'Basic Management'], ['title' => 'Standard Decor'], ['title' => 'Venue Rental']];
    }
}

// Timeline Logic
$currentDate = new DateTime();
$todayStr = $currentDate->format('Y-m-d');
$eventDate = new DateTime($booking['event_date']);
$eventDateStr = $eventDate->format('Y-m-d');
$status = strtolower($booking['status']);

if (!function_exists('getStepClass')) {
    function getStepClass($stepKey, $currentStatus, $currentDate, $eventDate)
    {
        if ($currentStatus === 'cancelled')
            return '';
        $todayStr = $currentDate->format('Y-m-d');
        $eventDateStr = $eventDate->format('Y-m-d');

        switch ($stepKey) {
            case 'received':
            case 'review':
                return 'completed';
            case 'confirmed':
                if ($currentStatus === 'confirmed' || $currentStatus === 'completed')
                    return 'completed';
                return '';
            case 'event':
                if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed')
                    return '';
                if ($todayStr === $eventDateStr)
                    return 'active';
                if ($todayStr > $eventDateStr)
                    return 'completed';
                return '';
            case 'completed':
                if ($currentStatus !== 'confirmed' && $currentStatus !== 'completed')
                    return '';
                return ($todayStr > $eventDateStr) ? 'completed' : '';
            default:
                return '';
        }
    }
}

$steps = [
    ['label' => 'Received', 'desc' => 'Booking received successfully', 'key' => 'received'],
    ['label' => 'Under Review', 'desc' => ($status === 'cancelled') ? 'Booking cancelled' : 'Reviewing event details', 'key' => 'review'],
    ['label' => 'Confirmed', 'desc' => ($status === 'confirmed' || $status === 'completed') ? 'Booking confirmed' : 'Awaiting confirmation', 'key' => 'confirmed'],
    ['label' => 'Event Day', 'desc' => 'Scheduled for ' . $eventDate->format('M d, Y'), 'key' => 'event'],
    ['label' => 'Completed', 'desc' => ($todayStr > $eventDateStr && $status !== 'cancelled') ? 'Event successfully completed' : 'Pending event day', 'key' => 'completed']
];
?>
<?php
$title = 'Booking #EPLN-' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT) . ' - e-Plan';
$activePage = (strtolower($eventCategory) === 'concert') ? 'tickets' : 'bookings';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-booking-details.css?v=' . time() . '">
';
include 'partials/header.php';
?>




    <!-- Immersive Hero Background -->
    <div class="hero">
        <img src="<?php echo htmlspecialchars($bgImage); ?>" alt="Event Background">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="category-tag"><?php echo htmlspecialchars($eventCategory); ?></span>
            <h1><?php echo htmlspecialchars($eventTitle); ?></h1>
            <p>Your curated architectural event experience is <?php echo strtolower($statusStr); ?>.</p>
        </div>
    </div>

    <div class="container">
        <!-- Breadcrumbs/Status Bar -->
        <div class="status-bar">
            <div class="sb-left">
                <span class="sb-ref">BOOKING REF:</span>
                <span class="sb-id">#EPLN-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="status-badge <?php echo $statusClass; ?>">
                <?php echo $statusStr; ?>
            </div>
        </div>

        <!-- 2-Column Grid -->
        <div class="content-grid">

            <!-- Left Column: All Booking Details -->
            <div class="left-col">
                <div class="card-section">
                    <h2 class="card-title"><i class="fa-regular fa-id-badge"></i>
                        <?php echo $isConcert ? 'Ticket' : 'Booking'; ?> Information</h2>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Booked By</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['full_name']); ?></span>
                            <i class="fa-regular fa-user info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Event Date</span>
                            <span
                                class="info-val"><?php echo date('F d, Y', strtotime($booking['event_date'])); ?></span>
                            <i class="fa-regular fa-calendar info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Check-in Time</span>
                            <span class="info-val"><?php
                            $time = !empty($booking['checkin_time']) ? $booking['checkin_time'] : '10:00 AM';
                            // If it's in 24hr format from input (HH:mm), convert to AM/PM
                            if (preg_match('/^\d{2}:\d{2}$/', $time)) {
                                echo date('h:i A', strtotime($time));
                            } else {
                                echo htmlspecialchars($time);
                            }
                            ?></span>
                            <i class="fa-regular fa-clock info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Guests</span>
                            <span class="info-val"><?php echo htmlspecialchars($booking['guest_count']); ?>
                                Attendees</span>
                            <i class="fa-solid fa-user-group info-icon"></i>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Primary Email</span>
                            <span class="info-val"
                                style="word-break:break-all; font-size:14px;"><?php echo htmlspecialchars($booking['email']); ?></span>
                            <i class="fa-regular fa-envelope info-icon"></i>
                        </div>
                    </div>

                    <div class="pkg-details">
                        <div class="pkg-header">
                            <span class="pkg-name">Package Details</span>
                            <?php 
                                $tierName = $booking['package_tier'] ?? 'Basic';
                                $capTier = ucfirst($tierName);
                            ?>
                            <span class="pkg-tier-label"><?php echo htmlspecialchars($capTier); ?>
                                Package</span>
                        </div>
                        <p class="pkg-desc">
                            <?php echo htmlspecialchars($selectedPackage['description'] ?? 'Your selected package features exclusive services carefully curated by our team.'); ?>
                        </p>

                        <h4 style="font-size:13px; margin-bottom:10px; color:#1f6f59;">WHAT'S INCLUDED:</h4>
                        <ul class="items-list">
                            <?php foreach ($items as $item): ?>
                                <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($item['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Venue & Organizer Info -->
                <div class="card-section">
                    <h2 class="card-title"><i class="fa-solid fa-location-dot"></i> Venue & Organizer</h2>
                    <div class="info-grid" style="grid-template-columns: 1fr;">
                        <div class="info-item" style="border-left-color: #ffc241;">
                            <span class="info-label">Venue</span>
                            <span class="info-val"><?php echo htmlspecialchars($venueName); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">
                                <?php echo htmlspecialchars($venueLocation); ?>
                            </span>
                        </div>
                        <div class="info-item" style="border-left-color: #ffc241;">
                            <span class="info-label">Organizer</span>
                            <span
                                class="info-val"><?php echo htmlspecialchars($booking['organizer_name'] ?: 'e-Plan Elite Team'); ?></span>
                            <span style="display:block; font-size:12px; color:var(--text-gray); margin-top:4px;">Lead
                                Architect & Coordinator</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Journey -->
            <div class="right-col">
                <div class="card-section journey-card">
                    <h2 class="card-title"><i class="fa-solid fa-route"></i> Booking Journey</h2>
                    <div class="timeline">
                        <?php foreach ($steps as $step):
                            $cls = getStepClass($step['key'], $displayStatus, $currentDate, $eventDate);
                            ?>
                            <div class="timeline-item <?php echo $cls; ?>">
                                <div class="tl-dot">
                                    <div class="dot-inner"><i class="fa-solid fa-check"></i></div>
                                </div>
                                <div class="tl-content">
                                    <h5><?php echo $step['label']; ?></h5>
                                    <p><?php echo $step['desc']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="summary-box">
                    <h3><?php echo (strtolower($eventCategory) === 'concert') ? 'Ticket Payment Summary' : 'Payment Summary'; ?>
                    </h3>

                    <div class="price-row">
                        <span><?php echo (strtolower($eventCategory) === 'concert') ? 'Total Ticket Price' : 'Total Booking Amount'; ?></span>
                        <span>Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>

                    <?php
                    $isConcert = (strtolower($eventCategory) === 'concert');
                    $payStatus = strtolower($booking['payment_status'] ?? 'unpaid');

                    if (!isset($paidAdvance))
                        $paidAdvance = $paymentModel->getSucceededTotalByBookingId($booking['id']);

                    $isFullyPaid = ($payStatus === 'paid');
                    $balance = max(0, $booking['total_amount'] - $paidAdvance);

                    // Standardize 50% advance for all events
                    $advance = $booking['total_amount'] * 0.5;
                    $remainingAdvance = max(0, $advance - $paidAdvance);
                    $hasAnyAdvancePaid = ($paidAdvance > 0.009);
                    $isAdvanceComplete = ($remainingAdvance <= 0.009) || ($remainingAdvance < 50 && $hasAnyAdvancePaid);
                    
                    if ($isAdvanceComplete)
                        $remainingAdvance = 0;
                    
                    $isPartiallyPaid = ($payStatus === 'partially_paid');
                    ?>

                    <div class="price-row">
                        <span>Advance (50% Online)</span>
                        <span style="color: <?php echo ($isAdvanceComplete || $isFullyPaid) ? '#10b981' : '#64748b'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($paidAdvance, 2); ?> /
                            <?php echo number_format($advance, 2); ?>
                            <?php if ($isAdvanceComplete || $isFullyPaid): ?><i class="fa-solid fa-check-circle"></i><?php endif; ?>
                        </span>
                    </div>

                    <div class="price-row">
                        <span>Remaining Online Advance</span>
                        <span style="color: <?php echo $isAdvanceComplete ? '#10b981' : '#ef4444'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($remainingAdvance, 2); ?>
                        </span>
                    </div>

                    <div class="price-row">
                        <span>Balance (50% Cash on Event Day)</span>
                        <span style="color: <?php echo $isFullyPaid ? '#10b981' : '#f59e0b'; ?>; font-weight: 600;">
                            Rs. <?php echo number_format($balance, 2); ?>
                            <?php if ($isFullyPaid): ?><i class="fa-solid fa-check-circle"></i><?php endif; ?>
                        </span>
                    </div>

                    <?php if (!empty($transactionId)): ?>
                        <div class="price-row"
                            style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-top: 15px; border: 1px solid #e2e8f0; flex-direction: column; align-items: flex-start; gap: 4px;">
                            <span
                                style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Transaction
                                ID (TX ID)</span>
                            <span
                                style="font-family: monospace; color: #1e293b; font-size: 12px; word-break: break-all;"><?php echo htmlspecialchars($transactionId); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="price-row total <?php echo ($isFullyPaid || $isAdvanceComplete) ? 'paid' : 'pending'; ?>"
                        style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                        <span>Current Status</span>
                        <span>
                            <?php
                            if ($isFullyPaid)
                                echo 'FULLY PAID';
                            elseif ($isAdvanceComplete)
                                echo 'ADVANCE COMPLETE';
                            elseif ($hasAnyAdvancePaid)
                                echo 'ADVANCE PARTIALLY PAID';
                            else
                                echo 'PAYMENT PENDING';
                            ?>
                        </span>
                    </div>

                    <?php if (!$isFullyPaid && !$isAdvanceComplete): ?>
                        <div style="margin-top: 20px;">
                            <a href="/EventManagementSystem/public/client/payment/checkout?booking_id=<?php echo $booking['id']; ?>"
                                class="btn-primary"
                                style="display: block; text-align: center; background: #246A55; color: white;">
                                <i class="fa-solid fa-credit-card"></i>
                                Pay Advance Installment (Rs. <?php echo number_format($remainingAdvance, 2); ?>)
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="policy-note"
                        style="margin-top:15px; padding:12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                        <span style="font-size:12px; color:#0369a1; display:flex; gap:8px; line-height:1.4;">
                            <i class="fa-solid fa-circle-info" style="margin-top:2px;"></i>
                            <span><b>Payment Policy:</b> Advanced payments are non-refundable. Remaining 50% balance must be settled in cash with the organizer by or on the day of the event.</span>
                        </span>
                    </div>

                    <?php
                    $isLocked = ($displayStatus === 'confirmed' && $payStatus !== 'unpaid');
                    $canCancel = ($displayStatus === 'pending' || ($displayStatus === 'confirmed' && $payStatus === 'unpaid'));
                    ?>

                    <?php if ($canCancel): ?>
                        <div style="margin-top: 15px;">
                            <form action="/EventManagementSystem/public/client/bookings/cancel" method="POST"
                                style="margin:0;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-danger">
                                    <i class="fa-solid fa-xmark"></i> Cancel Reservation
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php
                    $backUrl = (strtolower($eventCategory) === 'concert') ? '/EventManagementSystem/public/client/tickets' : '/EventManagementSystem/public/client/bookings';
                    $backLabel = (strtolower($eventCategory) === 'concert') ? 'Back to My Tickets' : 'Back to My Bookings';
                    ?>
                    <a href="<?php echo $backUrl; ?>" class="btn-primary"
                        style="margin-top: 15px; color:#1a1e23; background:#ffc241;">
                        <i class="fa-solid fa-arrow-left"></i> <?php echo $backLabel; ?>
                    </a>
                </div>
            </div>

        </div>
    </div>


    <script>
        (function () {
            if (!window.emsApi) return;

            const cancelForm = document.querySelector('form[action*="/client/bookings/cancel"]');
            if (cancelForm) {
                cancelForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const id = cancelForm.querySelector('input[name="booking_id"]')?.value;
                    if (!id) return;
                    if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;

                    try {
                        await window.emsApi.apiFetch(`/api/v1/bookings/${id}/cancel`, { method: 'PATCH' });
                        const isConcert = "<?php echo (strtolower($eventCategory) === 'concert'); ?>";
                        window.location.href = isConcert ? '/EventManagementSystem/public/client/tickets' : '/EventManagementSystem/public/client/bookings';
                    } catch (err) {
                        console.error('Cancel via API failed:', err);
                        alert('Error: ' + err.message);
                    }
                });
            }

            // Replace checkout link to prefer API-created checkout url (optional)
            const payLink = document.querySelector('a[href*="/client/payment/checkout"]');
            if (payLink) {
                const match = payLink.getAttribute('href').match(/booking_id=(\d+)/);
                const bookingId = match ? match[1] : null;
                if (bookingId) {
                    payLink.addEventListener('click', async function (e) {
                        e.preventDefault();
                        try {
                            const checkout = await window.emsApi.apiFetch('/api/v1/payments/checkout', {
                                method: 'POST',
                                body: { booking_id: Number(bookingId) }
                            });
                            const url = checkout?.data?.checkout_url;
                            if (!url) throw new Error('Missing checkout_url');
                            window.location.href = url;
                        } catch (err) {
                            console.error('Checkout via API failed, falling back to MVC link.', err);
                            window.location.href = payLink.getAttribute('href');
                        }
                    });
                }
            }
        })();
    </script>
<?php include 'partials/footer.php'; ?>