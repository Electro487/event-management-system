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
                    <div class="pd-delete-icon" onclick="deleteHeaderProfile('organizer')" title="Remove Photo">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                <?php endif; ?>
                <input type="file" id="header_profile_upload" accept="image/*" style="display: none;" onchange="uploadHeaderProfile(this, 'organizer')">
            </div>
            <h3 class="pd-name"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></h3>
            <span class="pd-role">Event Organizer</span>
        </div>
        <div class="pd-bottom">
            <?php
            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
            ?>
            <div class="pd-detail">
                <label>FULL NAME</label>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span id="pd-fullname-text"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?></span>
                    <button type="button" onclick="editFullname()" class="pd-edit-small-btn" title="Edit Name"><i class="fa-solid fa-pen"></i></button>
                </div>
                <div id="pd-fullname-edit" style="display: none; margin-top: 5px;">
                    <input type="text" id="pd-fullname-input" value="<?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?>" style="width: 100%; padding: 5px; border-radius: 4px; border: 1px solid #ddd;">
                    <div style="margin-top: 5px; display: flex; gap: 5px;">
                        <button type="button" onclick="saveFullname()" class="pd-save-btn">Save</button>
                        <button type="button" onclick="cancelEditFullname()" class="pd-cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>
            <div class="pd-detail">
                <label>EMAIL ADDRESS</label>
                <div><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
            </div>

            <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
        <style>
            .pd-edit-small-btn { background: none; border: none; color: #1e6f59; cursor: pointer; font-size: 12px; }
            .pd-save-btn { background: #1e6f59; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; }
            .pd-cancel-btn { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        </style>
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
            
            if(window.emsApi) {
                window.emsApi.apiFetch(endpoint, { method: 'POST', body: formData })
                .then(res => {
                    if (res.success) location.reload();
                    else alert(res.error?.message || 'Error updating profile picture.');
                })
                .catch(err => alert('Error: ' + err.message));
            } else {
                fetch(endpoint, { method: 'POST', body: formData })
                .then(r => r.json()).then(d => { if(d.success) location.reload(); else alert(d.message); });
            }
        }
    }

    function deleteHeaderProfile(role) {
        if (confirm('Are you sure you want to remove your profile picture?')) {
            const endpoint = (window.emsApi) ? '/api/v1/auth/profile/picture' : `/EventManagementSystem/public/${role}/profile/delete-picture`;
            
            if(window.emsApi) {
                window.emsApi.apiFetch(endpoint, { method: 'DELETE' })
                .then(res => { if(res.success) location.reload(); })
                .catch(err => alert('Error: ' + err.message));
            } else {
                fetch(endpoint, { method: 'POST' })
                .then(r => r.json()).then(d => { if(d.success) location.reload(); });
            }
        }
    }

    function editFullname() {
        document.getElementById('pd-fullname-text').style.display = 'none';
        document.querySelector('.pd-edit-small-btn').style.display = 'none';
        document.getElementById('pd-fullname-edit').style.display = 'block';
    }

    function cancelEditFullname() {
        document.getElementById('pd-fullname-text').style.display = 'inline';
        document.querySelector('.pd-edit-small-btn').style.display = 'inline';
        document.getElementById('pd-fullname-edit').style.display = 'none';
    }

    async function saveFullname() {
        const newName = document.getElementById('pd-fullname-input').value.trim();
        if(!newName) return;
        
        try {
            if(window.emsApi) {
                const res = await window.emsApi.apiFetch('/api/v1/auth/profile', {
                    method: 'POST',
                    body: { fullname: newName }
                });
                if(res.success) location.reload();
                else throw new Error(res.error?.message || 'Failed to update name');
            } else {
                alert('API not ready');
            }
        } catch(err) {
            alert('Error: ' + err.message);
        }
    }
</script>
