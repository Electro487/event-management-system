<?php
$packages = isset($event['packages']) ? json_decode($event['packages'], true) : [];

// Find package details
$selectedPackageData = $packages[$packageTier] ?? null;
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

// Only use defaults as absolute last resort if cost is 0
if ($basePrice <= 0) {
    if ($packageTier == 'standard')
        $basePrice = 60000;
    else if ($packageTier == 'premium')
        $basePrice = 150000;
    else
        $basePrice = 20000;
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body style="background-color: #f9fbf9;">

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/home">Home</a>
            <a href="/EventManagementSystem/public/client/events" class="active">Browse Events</a>
            <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
        </nav>
        <div class="nav-icons">
            <i class="fa-regular fa-bell" style="font-size: 20px; color: #1f6f59; cursor: pointer;"></i>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div
                    style="width: 32px; height: 32px; background: #1f6f59; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; cursor: pointer;">
                    <?php echo strtoupper(substr($_SESSION['user_fullname'], 0, 1)); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

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
                <a href="/EventManagementSystem/public/client/events#my-bookings" class="btn-primary"
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
                                <input type="date" name="event_date" required min="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo isset($event['event_date']) ? date('Y-m-d', strtotime($event['event_date'])) : ''; ?>">
                            </div>
                            <div class="input-group">
                                <label>CHECK IN TIME</label>
                                <input type="time" name="checkin_time" required value="10:00">
                            </div>
                            <div class="input-group">
                                <label>GUEST COUNT</label>
                                <div class="icon-input">
                                    <input type="number" name="guest_count" required min="10" step="1" placeholder="150"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    <i class="fa-solid fa-user-group"></i>
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
                                <small style="font-size: 11px; color: #1f6f59; font-weight: 500; margin-top: 4px; display: block;">* Must be exactly 10 digits starting with 9</small>
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
                                <span class="pkg-badge"><?php echo strtoupper($packageTier); ?> PACKAGE</span>
                            </div>
                            <i class="fa-solid fa-award" style="color:#1f6f59; font-size:20px;"></i>
                        </div>
                        <p class="pkg-desc"><?php echo htmlspecialchars($selectedPackageData['description'] ?? ''); ?></p>

                        <div class="whats-included">
                            <span class="wi-label">WHAT'S INCLUDED</span>
                            <ul class="wi-list">
                                <?php foreach ($items as $item): ?>
                                    <li><i class="fa-regular fa-circle-check" style="color:#1f6f59;"></i>
                                        <?php echo htmlspecialchars($item['title']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span><?php echo ucfirst($packageTier); ?> Package</span>
                                <span><?php echo number_format($basePrice, 2); ?></span>
                            </div>
                        </div>

                        <div class="total-row">
                            <span>Total Amount</span>
                            <span class="total-price">NRs <?php echo number_format($totalAmount, 2); ?></span>
                        </div>

                        <button type="submit" class="btn-confirm">
                            Confirm Booking <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <p class="terms-text">By clicking confirm, you agree to e-Plan's Terms of Service and Privacy
                            Policy.</p>
                    </div>
                </div>

            </form>
        <?php endif; ?>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-left">
            <div class="footer-logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                    style="height: 28px; width: auto; object-fit: contain;"></div>
            <div class="copyright">&copy; 2026 e.plan Architectural Event Curation. All rights reserved.</div>
        </div>
        <div class="footer-links">
            <a href="#">PRIVACY POLICY</a>
            <a href="#">TERMS OF SERVICE</a>
            <a href="#">CONTACT SUPPORT</a>
        </div>
    </footer>

</body>

</html>