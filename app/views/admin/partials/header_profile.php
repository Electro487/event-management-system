<?php
$headerInitials = '';
$nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
foreach ($nameParts as $p) {
    if (trim($p) !== '') {
        $headerInitials .= strtoupper(substr(trim($p), 0, 1));
    }
}
if (strlen($headerInitials) > 2) $headerInitials = substr($headerInitials, 0, 2);
?>
<div style="position: relative;" id="profile-container">
    <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" id="header-avatar">
        <?php else: ?>
            <span id="header-initials" style="font-size: 14px;"><?php echo htmlspecialchars($headerInitials); ?></span>
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
                        <span id="dropdown-initials" style="font-size: 28px; color: white;"><?php echo htmlspecialchars($headerInitials); ?></span>
                    <?php endif; ?>
                </div>
                <label for="header_profile_upload" class="pd-edit-icon" title="Change Photo">
                    <i class="fa-solid fa-pen"></i>
                </label>
                <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                    <div class="pd-delete-icon" onclick="deleteHeaderProfile('admin')" title="Remove Photo">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                <?php endif; ?>
                <input type="file" id="header_profile_upload" accept="image/*" style="display: none;" onchange="uploadHeaderProfile(this, 'admin')">
            </div>
            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
            <span class="pd-role">System Admin</span>
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
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });

    function uploadHeaderProfile(input, role) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('profile_picture', input.files[0]);

            const endpoint = (window.emsApi) ? '/api/v1/auth/profile/picture' : `/EventManagementSystem/public/${role}/profile/update`;

            if (window.emsApi) {
                window.emsApi.apiFetch(endpoint, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        if (res.success) location.reload();
                        else alert(res.error?.message || 'Error updating profile picture.');
                    })
                    .catch(err => alert('Error: ' + err.message));
            } else {
                fetch(endpoint, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert(data.message || 'Error updating profile picture.');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during upload.');
                    });
            }
        }
    }

    function deleteHeaderProfile(role) {
        if (confirm('Are you sure you want to remove your profile picture?')) {
            const endpoint = (window.emsApi) ? '/api/v1/auth/profile/picture' : `/EventManagementSystem/public/${role}/profile/delete-picture`;

            if (window.emsApi) {
                window.emsApi.apiFetch(endpoint, {
                        method: 'DELETE'
                    })
                    .then(res => {
                        if (res.success) location.reload();
                        else alert(res.error?.message || 'Error deleting profile picture.');
                    })
                    .catch(err => alert('Error: ' + err.message));
            } else {
                fetch(endpoint, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert(data.message || 'Error deleting profile picture.');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during deletion.');
                    });
            }
        }
    }
</script>
