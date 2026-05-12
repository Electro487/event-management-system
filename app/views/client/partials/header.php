<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'e-Plan'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/booking.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js"></script>
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body style="background:#f8fafc; margin:0; font-family:'Inter', sans-serif;">

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/client/home" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home" class="<?php echo ($activePage ?? '') == 'home' ? 'active' : ''; ?>">Home</a>
            <a href="/EventManagementSystem/public/client/events" class="<?php echo ($activePage ?? '') == 'events' ? 'active' : ''; ?>">Browse Events</a>
            <a href="/EventManagementSystem/public/client/bookings" class="<?php echo ($activePage ?? '') == 'bookings' ? 'active' : ''; ?>">My Bookings</a>
            <a href="/EventManagementSystem/public/client/tickets" class="<?php echo ($activePage ?? '') == 'tickets' ? 'active' : ''; ?>">My Tickets</a>
            <a href="/EventManagementSystem/public/client/requests" class="<?php echo ($activePage ?? '') == 'requests' ? 'active' : ''; ?>">My Requests</a>
            <a href="/EventManagementSystem/public/client/payments" class="<?php echo ($activePage ?? '') == 'payments' ? 'active' : ''; ?>">Payment History</a>
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
