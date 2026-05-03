<?php
$packages = isset($event['packages']) ? json_decode($event['packages'], true) : [];

// Extract unique items from all packages to show in "What's Included"
$includedItems = [];
if (is_array($packages)) {
    foreach ($packages as $tier => $pkgData) {
        if (!empty($pkgData['items'])) {
            foreach ($pkgData['items'] as $item) {
                if (!empty($item['title'])) {
                    $includedItems[$item['title']] = $item;
                }
            }
        }
    }
}
$includedItemsList = array_values($includedItems);
if (empty($includedItemsList)) {
    $includedItemsList = [
        ['title' => 'Bespoke Floral Decoration'],
        ['title' => 'Premium Heritage Venue Setup'],
        ['title' => 'Gourmet Multi-cuisine Catering'],
        ['title' => 'Pro-cinematography & Photo'],
        ['title' => 'Live Music & Sound Engineering'],
        ['title' => 'Lead Event Coordinator']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-event.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events" class="active">Browse Events</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/EventManagementSystem/public/client/bookings">My Bookings</a>
                <a href="/EventManagementSystem/public/client/tickets">My Tickets</a>
            <?php endif; ?>
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
                foreach ($nameParts as $p) {
                    $initials .= strtoupper(substr($p, 0, 1));
                }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                ?>
                <div style="position: relative;" id="profile-container">
                    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="header-avatar">
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
                                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="dropdown-avatar">
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
            <?php else: ?>
                <a href="/EventManagementSystem/public/login"
                    style="color: #1f6f59; font-weight: 600; text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="/EventManagementSystem/public/client/home">Home</a> &gt;
            <a href="/EventManagementSystem/public/client/events">Browse Events</a> &gt;
            <span class="current"><?php echo htmlspecialchars($event['title']); ?></span>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <?php 
            if (!empty($event['image_path'])) {
                $image = ($event['image_path'][0] === '/') ? $event['image_path'] : '/EventManagementSystem/public/assets/images/events/' . $event['image_path'];
            } else {
                $image = '/EventManagementSystem/public/assets/images/placeholder.jpg';
            }
            ?>
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
            <div class="hero-content">
                <span class="category-tag"><?php echo htmlspecialchars($event['category'] ?: 'Event'); ?></span>
                <h1><?php echo htmlspecialchars($event['title']); ?></h1>
                <p>Curating timeless moments for your once-in-a-lifetime celebration with architectural precision.</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">

            <!-- Left Column -->
            <div class="left-col">
                <h2 class="section-title">About This Event</h2>
                <div class="about-text">
                    <?php
                    if (!empty($event['description'])) {
                        echo nl2br(htmlspecialchars($event['description']));
                    } else {
                        echo "Your event is a tapestry of moments that define your journey together. At e-Plan, we specialize in transforming your vision into an architectural masterpiece of floral arrangements, curated catering, and seamless logistical execution. We handle the structural foundation so you can focus on the heart of the celebration.";
                    }
                    ?>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin-bottom: 0;">What's Included</h2>
                    <span id="whats-included-subtitle"
                        style="font-size: 13px; font-weight: 600; color: #bfa15f; text-transform: uppercase;">All
                        Packages</span>
                </div>
                <div class="included-grid" id="includedGrid">
                    <?php foreach ($includedItemsList as $item): ?>
                        <div class="included-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <div style="display: flex; flex-direction: column;">
                                <span><?php echo htmlspecialchars($item['title']); ?></span>
                                <?php if (!empty($item['description'])): ?>
                                    <span
                                        style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;"><?php echo htmlspecialchars($item['description']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="details-box">
                    <h2 class="section-title">Event Details</h2>
                    <div class="details-grid">
                        <div class="detail-col">
                            <h4>Location</h4>
                            <p><?php echo htmlspecialchars($event['venue_name'] ?: $event['venue_location'] ?: 'Location TBD'); ?>
                            </p>
                            <span
                                style="font-size:11px; color:#6b7280;"><?php echo htmlspecialchars($event['venue_location']); ?></span>
                        </div>
                        <div class="detail-col">
                            <h4>Status</h4>
                            <p class="status-open">
                                <?php echo $event['status'] === 'active' ? 'Booking Open' : ucfirst($event['status']); ?>
                            </p>
                        </div>
                        <?php if (!empty($event['event_date'])): ?>
                        <div class="detail-col">
                            <h4>Date & Time</h4>
                            <p><?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                            <span style="font-size:11px; color:#6b7280;">Scheduled at <?php echo date('h:i A', strtotime($event['event_date'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="packages-box">
                    <h3>Choose Your Package</h3>

                    <?php
                    // Render packages elegantly
                    if (empty($packages)) {
                        echo '<p class="no-data">No package information available for this event.</p>';
                    }

                    $tiersToRender = ['basic', 'standard', 'premium'];

                    foreach ($tiersToRender as $tierKey):
                        if (isset($packages[$tierKey])):
                            $pkgData = $packages[$tierKey];

                            $cssClass = '';
                            if ($tierKey === 'standard')
                                $cssClass = 'standard';
                            if ($tierKey === 'premium')
                                $cssClass = 'premium';

                            $priceValue = $pkgData['price'] ?? ($pkgData['price_range'] ?? '');
                            $priceDisplay = !empty($priceValue) ? 'Rs. ' . number_format((float) str_replace(['Rs.', ',', ' '], '', $priceValue), 0) : 'Custom Pricing';
                    ?>
                            <div class="package-tier <?php echo $cssClass; ?>"
                                onclick="selectPackage('<?php echo $tierKey; ?>', this)">
                                <?php if ($tierKey === 'standard'): ?>
                                    <div class="most-popular-badge">Most Popular</div>
                                <?php endif; ?>

                                <div class="package-header">
                                    <div class="tier-name"><?php echo ucfirst($tierKey); ?></div>
                                    <div class="tier-price"><?php echo htmlspecialchars($priceDisplay); ?></div>
                                </div>
                                <div class="tier-desc">
                                    <?php echo htmlspecialchars($pkgData['description'] ?: 'Complete set of services curated for this tier.'); ?>
                                </div>
                            </div>
                    <?php
                        endif;
                    endforeach;
                    ?>

                    <button class="btn-book-now" onclick="proceedToBooking(<?php echo $event['id']; ?>)">
                        Book Now <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <p class="tax-note">* Prices are exclusive of taxes and subject to customization.</p>
                    <div class="policy-badge" style="margin-top: 15px; padding: 10px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 11px; color: #92400e; display: flex; align-items: start; gap: 8px; line-height: 1.4;">
                        <i class="fa-solid fa-circle-info" style="margin-top: 2px;"></i>
                        <span><b>Payment Policy:</b> Securing this event requires a non-refundable <b>50% advance payment</b> online. The remaining balance will be collected in cash on the event day.</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // Pass PHP packages array to JS
        const packagesData = <?php echo json_encode($packages); ?>;
        const globalItemsHtml = document.getElementById('includedGrid').innerHTML;

        function selectPackage(tierKey, element) {
            // Remove active class from all tiers
            document.querySelectorAll('.package-tier').forEach(el => {
                el.classList.remove('active-tier');
            });

            // Add active class to clicked tier
            if (element) {
                element.classList.add('active-tier');
            }

            const includedGrid = document.getElementById('includedGrid');
            const subtitle = document.getElementById('whats-included-subtitle');

            if (!tierKey || !packagesData[tierKey] || !packagesData[tierKey].items || packagesData[tierKey].items.length === 0) {
                includedGrid.innerHTML = globalItemsHtml;
                subtitle.innerText = "All Packages";
                return;
            }

            const items = packagesData[tierKey].items;
            subtitle.innerText = tierKey + " Package";

            let html = '';
            items.forEach(item => {
                const title = escapeHtml(item.title);
                const desc = item.description ? escapeHtml(item.description) : '';

                html += `
                <div class="included-item">
                    <i class="fa-solid fa-circle-check"></i>
                    <div style="display: flex; flex-direction: column;">
                        <span>${title}</span>
                        ${desc ? `<span style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;">${desc}</span>` : ''}
                    </div>
                </div>
            `;
            });

            includedGrid.innerHTML = html;
        }

        function escapeHtml(unsafe) {
            return (unsafe || "").toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function proceedToBooking(eventId) {
            const activeTierEl = document.querySelector('.package-tier.active-tier');
            if (!activeTierEl) {
                alert('Please select a package tier first.');
                return;
            }

            // Find which tier is selected by checking inner text or class
            let selectedTier = 'basic';
            if (activeTierEl.classList.contains('standard')) selectedTier = 'standard';
            if (activeTierEl.classList.contains('premium')) selectedTier = 'premium';

            window.location.href = `/EventManagementSystem/public/client/book?id=${eventId}&package=${selectedTier}`;
        }
    </script>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        (function () {
            if (!window.emsApi) return;
            // Optional: refresh this event data from API using query id for parity
            const params = new URLSearchParams(window.location.search);
            const id = params.get('id');
            if (!id) return;
            window.emsApi.apiFetch(`/api/v1/events/${id}`)
                .then(() => { /* if needed later, can live-update DOM */ })
                .catch(() => { /* keep PHP render */ });
        })();
    </script>
</body>

</html>
