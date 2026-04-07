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
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body style="background-color: #f9fbf9;">

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events" class="active">Browse Events</a>
            <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
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
                            <?php 
                                $tierIcon = 'fa-solid fa-award';
                                if ($packageTier === 'basic') $tierIcon = 'fa-solid fa-box';
                                if ($packageTier === 'standard') $tierIcon = 'fa-solid fa-certificate';
                            ?>
                            <i class="<?php echo $tierIcon; ?>" style="color:#1f6f59; font-size:24px;"></i>
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
                            <div class="price-row" style="margin-bottom: 5px;">
                                <span>Total Amount</span>
                                <span>₨ <?php echo number_format($totalAmount, 2); ?></span>
                            </div>
                            <div class="price-row" style="color: #1f6f59; font-weight: 600; margin-bottom: 5px;">
                                <span>Advance (50% Online)</span>
                                <span>₨ <?php echo number_format($totalAmount * 0.5, 2); ?></span>
                            </div>
                            <div class="price-row" style="color: #64748b; font-size: 13px;">
                                <span>Balance (50% Cash)</span>
                                <span>₨ <?php echo number_format($totalAmount * 0.5, 2); ?></span>
                            </div>
                        </div>

                        <input type="hidden" name="pay_later" id="pay_later_flag" value="0">

                        <button type="submit" class="btn-confirm">
                            Proceed to Pay Advance <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <button type="button" class="btn-confirm" onclick="submitWithPayLater()" 
                                style="background: transparent; color: #64748b; border: 1px solid #e2e8f0; margin-top: 10px;">
                            I'll Pay Advance Later
                        </button>

                        <div class="policy-info" style="margin-top: 15px; padding: 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 11px; color: #92400e; line-height: 1.4;">
                            <i class="fa-solid fa-circle-info"></i> <b>Advance Required:</b> A 50% non-refundable advance is required online to secure your booking. The remaining 50% balance must be paid in cash on the event day.
                        </div>

                        <p class="terms-text" style="margin-top: 15px;">By clicking confirm, you agree to e-Plan's Terms of Service and Privacy
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
                                    form.submit();
                                }
                            }
                        </script>
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

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>

</html>
