<?php
$packages = isset($event['packages']) ? json_decode($event['packages'], true) : [];

// Find package details
$selectedPackageData = $packages[$packageTier] ?? null;

if ($customRequest) {
    $customData = json_decode($customRequest['custom_packages'], true);
    $selectedPackageData = [
        'description' => 'Negotiated Custom Package',
        'price' => $customRequest['proposed_price'],
        'items' => $customData['items'] ?? []
    ];
}

if (!$selectedPackageData) {
    // Fallback if not configured in DB for this event but passed in URL
    $selectedPackageData = [
        'description' => 'Complete set of services curated for this tier.',
        'price_range' => ($packageTier == 'premium' ? 150000 : ($packageTier == 'standard' ? 60000 : 20000)),
        'items' => []
    ];
}

// Ensure correct types for calculation
$priceValue = $selectedPackageData['price'] ?? ($selectedPackageData['price_range'] ?? '');
$basePrice = !empty($priceValue) ? (float) str_replace(['Rs.', ',', ' '], '', $priceValue) : 0;

$isConcert = (strtolower($event['category'] ?? '') === 'concert');

// Only use defaults as absolute last resort if cost is 0
if ($basePrice <= 0) {
    if ($packageTier == 'standard')
        $basePrice = 60000;
    else if ($packageTier == 'premium') {
        $basePrice = 150000;
        // Cap premium concert tickets to 100k
        if ($isConcert)
            $basePrice = 100000;
    } else
        $basePrice = 20000;
} else {
    // If we have a base price from DB, still cap it for premium concert tickets
    if ($isConcert && $packageTier == 'premium' && $basePrice > 100000) {
        $basePrice = 100000;
    }
}

$serviceFee = 0.00;
$totalAmount = $basePrice;

// Default Items for display if none
$items = $selectedPackageData['items'] ?? [];
if (empty($items)) {
    if ($packageTier == 'premium') {
        $items = [['title' => 'Exclusive Catering & Decor'], ['title' => 'Premium 5-course meal'], ['title' => 'Luxury imported floral arrangements']];
    } else if ($packageTier == 'standard') {
        $items = [['title' => 'Full Venue Coordination'], ['title' => 'Premium Floral Arrangement'], ['title' => 'Standard Catering (150 guests)'], ['title' => 'Live String Quartet (2 hours)']];
    } else {
        $items = [['title' => 'Basic Management'], ['title' => 'Standard Decor'], ['title' => 'Venue Rental']];
    }
}

$title = 'Book Event - e-Plan';
$activePage = 'events';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=' . time() . '">
';
include 'partials/header.php';
?>





    <div class="container">

        <?php if (isset($_GET['success'])): ?>
            <!-- STEP 3: CONFIRMATION -->
            <div class="stepper">
                <div class="step">
                    <div class="step-circle completed"><i class="fa-solid fa-check"></i></div>
                    <div class="step-label">1. Select Package</div>
                </div>
                <div class="step-line active"></div>
                <div class="step">
                    <div class="step-circle completed"><i class="fa-solid fa-check"></i></div>
                    <div class="step-label">2. Your Details</div>
                </div>
                <div class="step-line active"></div>
                <div class="step">
                    <div class="step-circle active">3</div>
                    <div class="step-label" style="font-weight: 600; color: #1f6f59;">3. Confirm</div>
                </div>
            </div>

            <div class="confirmation-box">
                <div class="success-icon">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <h2>Booking Confirmed!</h2>
                <p>Your booking request for has been successfully received. Our concierges will review it and contact you
                    shortly to finalize the arrangements.</p>
                <div class="conf-details">
                    <span>Booking Reference ID:</span>
                    <strong>#EPLN-<?php echo str_pad($_GET['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                </div>
                <a href="/EventManagementSystem/public/client/bookings" class="btn-primary"
                    style="display:inline-block; margin-top:20px; text-decoration:none;">View My Bookings</a>
            </div>

        <?php else: ?>
            <!-- STEP 2: FILL DETAILS -->
            <div class="stepper">
                <div class="step">
                    <div class="step-circle completed"><i class="fa-solid fa-check"></i></div>
                    <div class="step-label" style="color: #1f6f59;">1. Select Package</div>
                </div>
                <div class="step-line active"></div>
                <div class="step">
                    <div class="step-circle active">2</div>
                    <div class="step-label" style="font-weight: 600; color: #1f6f59;">2. Your Details</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <div class="step-label">3. Confirm</div>
                </div>
            </div>

            <form action="/EventManagementSystem/public/client/book/store" method="POST" class="booking-grid">

                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                <input type="hidden" name="custom_request_id" value="<?php echo htmlspecialchars($requestId ?? ''); ?>">
                <input type="hidden" name="package_tier" value="<?php echo htmlspecialchars($packageTier); ?>">
                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">

                <!-- Left Column -->
                <div class="left-col">

                    <!-- Event Summary Card -->
                    <div class="event-summary-card">
                        <?php $image = !empty($event['image_path']) ? $event['image_path'] : '/EventManagementSystem/public/assets/images/placeholder.jpg'; ?>
                        <div class="event-img" style="background-image: url('<?php echo htmlspecialchars($image); ?>');">
                            <div class="img-overlay">
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details Box -->
                    <div class="form-section">
                        <h4><i class="fa-regular fa-calendar" style="color:#1f6f59; margin-right:8px;"></i> Event Details
                        </h4>

                        <div class="input-row">
                            <div class="input-group">
                                <label>EVENT DATE</label>
                                <?php if (strtolower($event['category']) === 'concert'): ?>
                                    <input type="text"
                                        value="<?php echo isset($event['event_date']) ? date('F d, Y', strtotime($event['event_date'])) : 'Fixed Date'; ?>"
                                        readonly style="background: #f1f5f9; cursor: not-allowed;">
                                    <input type="hidden" name="event_date"
                                        value="<?php echo isset($event['event_date']) ? date('Y-m-d', strtotime($event['event_date'])) : ''; ?>">
                                <?php else: ?>
                                    <input type="date" name="event_date" required min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo isset($event['event_date']) ? date('Y-m-d', strtotime($event['event_date'])) : ''; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="input-group">
                                <label>TIME</label>
                                <?php if (strtolower($event['category']) === 'concert'): ?>
                                    <?php
                                    $fixedTime = isset($event['event_date']) ? date('h:i A', strtotime($event['event_date'])) : '06:00 PM';
                                    ?>
                                    <input type="text" value="<?php echo $fixedTime; ?>" readonly
                                        style="background: #f1f5f9; cursor: not-allowed;">
                                    <input type="hidden" name="checkin_time"
                                        value="<?php echo date('H:i', strtotime($fixedTime)); ?>">
                                <?php else: ?>
                                    <input type="time" name="checkin_time" required value="10:00">
                                <?php endif; ?>
                            </div>
                            <div class="input-group">
                                <label><?php echo (strtolower($event['category']) === 'concert') ? 'NUMBER OF TICKETS' : 'GUEST COUNT'; ?></label>
                                <div class="icon-input">
                                    <input type="number" id="ticket_quantity" name="guest_count" required min="1"
                                        max="<?php echo $isConcert ? '5' : '10000'; ?>" step="1"
                                        placeholder="<?php echo $isConcert ? 'Qty' : '150'; ?>" oninput="calculateTotal()"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    <i class="fa-solid fa-ticket-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info Box -->
                    <div class="form-section">
                        <h4><i class="fa-regular fa-user" style="color:#1f6f59; margin-right:8px;"></i> Personal Information
                        </h4>

                        <div class="input-group full-width">
                            <label>FULL NAME</label>
                            <input type="text" name="full_name" required placeholder="John Doe">
                        </div>

                        <div class="input-row" style="margin-top: 20px;">
                            <div class="input-group">
                                <label>EMAIL ADDRESS</label>
                                <input type="email" name="email" required placeholder="john.doe@example.com">
                            </div>
                            <div class="input-group">
                                <label>PHONE NUMBER</label>
                                <input type="tel" name="phone" id="phone" required placeholder="9XXXXXXXXX"
                                    pattern="9[0-9]{9}" maxlength="10"
                                    title="Phone number must start with 9 and contain exactly 10 digits"
                                    oninvalid="this.setCustomValidity('Please enter a 10-digit number starting with 9')"
                                    oninput="this.setCustomValidity('')"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <small
                                    style="font-size: 11px; color: #1f6f59; font-weight: 500; margin-top: 4px; display: block;">*
                                    Must be exactly 10 digits starting with 9</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column - Order Summary -->
                <div class="right-col">
                    <div class="order-summary">
                        <h3>Order Summary</h3>

                        <div class="summary-pkg-header">
                            <div>
                                <h4><?php echo htmlspecialchars($event['category'] ?: 'Event'); ?></h4>
                                <span class="pkg-badge"><?php echo strtoupper($packageTier); ?>
                                    <?php echo (strtolower($event['category']) === 'concert') ? 'TIER' : 'PACKAGE'; ?></span>
                            </div>
                            <?php
                            $tierIcon = 'fa-solid fa-award';
                            if ($packageTier === 'basic')
                                $tierIcon = 'fa-solid fa-box';
                            if ($packageTier === 'standard')
                                $tierIcon = 'fa-solid fa-certificate';
                            ?>
                            <i class="<?php echo $tierIcon; ?>" style="color:#1f6f59; font-size:24px;"></i>
                        </div>
                        <p class="pkg-desc"><?php echo htmlspecialchars($selectedPackageData['description'] ?? ''); ?></p>

                        <div class="whats-included">
                            <span
                                class="wi-label"><?php echo (strtolower($event['category']) === 'concert') ? "TICKET FEATURES" : "WHAT'S INCLUDED"; ?></span>
                            <ul class="wi-list">
                                <?php foreach ($items as $item): ?>
                                    <li><i class="fa-regular fa-circle-check" style="color:#1f6f59;"></i>
                                        <?php echo htmlspecialchars($item['title']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <?php if ($isConcert): ?>
                        <div class="promo-section" style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px; text-transform: uppercase;">Have a Promo Code?</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" id="promo_code_input" placeholder="Enter code" style="flex: 1; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; text-transform: uppercase; outline: none;">
                                <button type="button" onclick="applyPromoCode()" style="padding: 8px 16px; background: #1f6f59; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">Apply</button>
                            </div>
                            <div id="promo_message" style="font-size: 12px; margin-top: 8px; display: none;"></div>
                            <input type="hidden" name="promo_code" id="applied_promo_code" value="">
                        </div>
                        <?php endif; ?>

                        <div class="price-breakdown">
                            <?php if ($isConcert): ?>
                                <div class="price-row" style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                                    <span>Price per Ticket</span>
                                    <span>₨ <span
                                            id="display_base_price"><?php echo number_format($basePrice, 2); ?></span></span>
                                </div>
                                <div id="discount_row" class="price-row" style="color: #dc2626; font-size: 13px; margin-bottom: 5px; display: none;">
                                    <span>Promo Discount (<span id="discount_percent">0</span>%)</span>
                                    <span>- ₨ <span id="display_discount_amount">0.00</span></span>
                                </div>
                                <div class="price-row" style="color: #1f6f59; font-weight: 700; margin-bottom: 5px; font-size: 16px;">
                                    <span>Total Ticket Price</span>
                                    <span>₨ <span
                                            id="display_total_amount"><?php echo number_format($totalAmount, 2); ?></span></span>
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-bottom: 12px;">Full payment required for
                                    instant ticket generation. Max 5 tickets.</div>
                            <?php else: ?>
                                <div class="price-row" style="margin-bottom: 5px;">
                                    <span>Subtotal</span>
                                    <span>₨ <span id="display_subtotal_amount"><?php echo number_format($totalAmount, 2); ?></span></span>
                                </div>
                                <div id="discount_row" class="price-row" style="color: #dc2626; font-size: 13px; margin-bottom: 5px; display: none;">
                                    <span>Promo Discount (<span id="discount_percent">0</span>%)</span>
                                    <span>- ₨ <span id="display_discount_amount">0.00</span></span>
                                </div>
                                <div class="price-row" style="margin-bottom: 5px; font-weight: 700;">
                                    <span>Total Amount</span>
                                    <span>₨ <span
                                            id="display_total_amount"><?php echo number_format($totalAmount, 2); ?></span></span>
                                </div>
                                <div class="price-row" style="color: #1f6f59; font-weight: 600; margin-bottom: 5px;">
                                    <span>Advance (50% Online)</span>
                                    <span>₨ <span
                                            id="display_advance_amount"><?php echo number_format($totalAmount * 0.5, 2); ?></span></span>
                                </div>
                                <div class="price-row" style="color: #64748b; font-size: 13px;">
                                    <span>Balance (50% Cash)</span>
                                    <span>₨ <span
                                            id="display_balance_amount"><?php echo number_format($totalAmount * 0.5, 2); ?></span></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="pay_later" id="pay_later_flag" value="0">

                        <button type="submit" class="btn-confirm">
                            Proceed to Pay
                            <?php echo (strtolower($event['category']) === 'concert') ? 'for Ticket' : 'Advance'; ?> <i
                                class="fa-solid fa-arrow-right"></i>
                        </button>

                        <?php if (strtolower($event['category']) !== 'concert'): ?>
                            <button type="button" class="btn-confirm" onclick="submitWithPayLater()"
                                style="background: transparent; color: #64748b; border: 1px solid #e2e8f0; margin-top: 10px;">
                                I'll Pay Advance Later
                            </button>
                        <?php endif; ?>

                        <div class="policy-info"
                            style="margin-top: 15px; padding: 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 11px; color: #92400e; line-height: 1.4;">
                            <i class="fa-solid fa-circle-info"></i>
                            <?php if (strtolower($event['category']) === 'concert'): ?>
                                <b>Full Payment Required:</b> Tickets are non-refundable and require full online payment for
                                instant confirmation and email delivery.
                            <?php else: ?>
                                <b>Advance Required:</b> A 50% non-refundable advance is required online to secure your booking.
                                The remaining 50% balance must be paid in cash on the event day.
                            <?php endif; ?>
                        </div>

                        <p class="terms-text" style="margin-top: 15px;">By clicking confirm, you agree to e-Plan's Terms of
                            Service and Privacy
                            Policy.</p>

                        <script>
                            function submitWithPayLater() {
                                const form = document.querySelector('form');

                                // Manually trigger HTML5 validation
                                if (!form.reportValidity()) {
                                    return; // Stop if form is invalid
                                }

                                if (confirm("Notice: Your booking will remain 'PENDING' and is NOT secured until the 50% advance is received. You can pay this later from your 'My Bookings' dashboard. Do you want to proceed and book now?")) {
                                    document.getElementById('pay_later_flag').value = '1';
                                    // Trigger the submit event listener which handles the API call
                                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                                }
                            }
                        </script>
                    </div>
                </div>

            </form>
        <?php endif; ?>

    </div>


    <script>
        const BASE_PRICE = <?php echo $basePrice; ?>;
        const IS_CONCERT = <?php echo $isConcert ? 'true' : 'false'; ?>;
        let appliedDiscountPercent = 0;

        function calculateTotal() {
            const qtyInput = document.getElementById('ticket_quantity');
            let qty = parseInt(qtyInput.value) || 0;

            if (IS_CONCERT && qty > 5) {
                qty = 5;
                qtyInput.value = 5;
                alert('Maximum 5 tickets allowed per booking.');
            }

            const subtotal = BASE_PRICE * (IS_CONCERT ? qty : 1);
            const discountAmount = (subtotal * appliedDiscountPercent) / 100;
            const total = subtotal - discountAmount;
            
            document.querySelector('input[name="total_amount"]').value = total;
            
            // Update UI
            const subtotalDisplay = document.getElementById('display_subtotal_amount');
            if (subtotalDisplay) subtotalDisplay.innerText = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            const discountRow = document.getElementById('discount_row');
            const discountDisplay = document.getElementById('display_discount_amount');
            const discountPercentDisplay = document.getElementById('discount_percent');
            
            if (appliedDiscountPercent > 0) {
                discountRow.style.display = 'flex';
                discountDisplay.innerText = discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                discountPercentDisplay.innerText = appliedDiscountPercent;
            } else {
                discountRow.style.display = 'none';
            }

            const totalDisplay = document.getElementById('display_total_amount');
            if (totalDisplay) totalDisplay.innerText = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (!IS_CONCERT) {
                const advanceDisplay = document.getElementById('display_advance_amount');
                if (advanceDisplay) advanceDisplay.innerText = (total * 0.5).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const balanceDisplay = document.getElementById('display_balance_amount');
                if (balanceDisplay) balanceDisplay.innerText = (total * 0.5).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        async function applyPromoCode() {
            if (!IS_CONCERT) return;
            const codeInput = document.getElementById('promo_code_input');
            const code = codeInput.value.trim().toUpperCase();
            const messageEl = document.getElementById('promo_message');
            const hiddenInput = document.getElementById('applied_promo_code');

            if (!code) {
                alert('Please enter a promo code.');
                return;
            }

            try {
                messageEl.style.display = 'block';
                messageEl.style.color = '#64748b';
                messageEl.innerText = 'Validating code...';

                const res = await window.emsApi.apiFetch('/api/v1/promo-codes/validate', {
                    method: 'POST',
                    body: { code }
                });

                if (res.success && res.data.valid) {
                    appliedDiscountPercent = res.data.discount_percentage;
                    hiddenInput.value = code;
                    messageEl.style.color = '#15803d';
                    messageEl.innerText = `Success! ${appliedDiscountPercent}% discount applied.`;
                    calculateTotal();
                } else {
                    throw new Error(res.message || 'Invalid code');
                }
            } catch (err) {
                appliedDiscountPercent = 0;
                hiddenInput.value = '';
                messageEl.style.color = '#dc2626';
                messageEl.innerText = 'Invalid or expired promo code.';
                calculateTotal();
            }
        }

        (function () {
            if (!window.emsApi) return;
            const form = document.querySelector('form.booking-grid');
            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!form.reportValidity()) return;
                
                if (IS_CONCERT) {
                    const qty = parseInt(document.getElementById('ticket_quantity').value) || 0;
                    if (qty > 5) {
                        alert('Max 5 tickets per booking!');
                        return;
                    }
                }

                const submitBtns = form.querySelectorAll('button');
                submitBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.cursor = 'not-allowed';
                });

                const fd = new FormData(form);
                const payLater = (fd.get('pay_later') === '1');

                const payload = {
                    event_id: Number(fd.get('event_id')),
                    custom_request_id: fd.get('custom_request_id') ? Number(fd.get('custom_request_id')) : null,
                    package_tier: String(fd.get('package_tier') || ''),
                    event_date: String(fd.get('event_date') || ''),
                    guest_count: Number(fd.get('guest_count') || 0),
                    full_name: String(fd.get('full_name') || ''),
                    email: String(fd.get('email') || ''),
                    phone: String(fd.get('phone') || ''),
                    checkin_time: String(fd.get('checkin_time') || ''),
                    total_amount: Number(fd.get('total_amount') || 0),
                    promo_code: String(fd.get('promo_code') || '')
                };

                try {
                    const res = await window.emsApi.apiFetch('/api/v1/bookings', { method: 'POST', body: payload });
                    const bookingId = res?.data?.booking_id;
                    if (!bookingId) throw new Error('Booking creation failed.');

                    if (payLater) {
                        const targetUrl = IS_CONCERT ? '/EventManagementSystem/public/client/tickets' : '/EventManagementSystem/public/client/bookings';
                        window.location.href = `${targetUrl}?booking_success=1`;
                        return;
                    }

                    const checkout = await window.emsApi.apiFetch('/api/v1/payments/checkout', {
                        method: 'POST',
                        body: { booking_id: Number(bookingId) }
                    });

                    const url = checkout?.data?.checkout_url;
                    if (!url) throw new Error('Checkout created but missing checkout_url.');
                    window.location.href = url;
                } catch (err) {
                    console.error('API booking flow failed:', err);
                    alert('Error: ' + err.message);
                    submitBtns.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                    });
                }
            });
        })();
    </script>
<?php include 'partials/footer.php'; ?>