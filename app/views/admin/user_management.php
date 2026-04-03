<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
    <style>
        .user-table th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .user-row:hover { background: #f1f5f9; }
        .role-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .role-client { background: #ecfdf5; color: #059669; }
        .role-organizer { background: #fff7ed; color: #c2410c; }
        .role-admin { background: #eef2ff; color: #4338ca; }
        .status-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 500; }
        .status-active::before { content: ""; width: 8px; height: 8px; background: #10b981; border-radius: 50%; }
        .status-blocked::before { content: ""; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; }
        .action-btn-outline { background: white; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .action-btn-outline:hover { border-color: #cbd5e1; background: #f8fafc; }
        .btn-block { color: #ef4444; border-color: #fecaca; }
        .btn-block:hover { background: #fef2f2; border-color: #ef4444; }
        .btn-unblock { color: #10b981; border-color: #a7f3d0; }
        .btn-unblock:hover { background: #f0fdf4; border-color: #10b981; }
        
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; }
        .modal-card { background: white; padding: 30px; border-radius: 16px; width: 400px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .modal-icon { font-size: 40px; color: #ef4444; margin-bottom: 20px; }
        .modal-card h3 { margin-bottom: 10px; font-size: 18px; }
        .modal-card p { color: #64748b; font-size: 14px; margin-bottom: 25px; line-height: 1.5; }
        .modal-btns { display: flex; gap: 12px; justify-content: center; }
        .modal-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        .btn-confirm-delete { background: #ef4444; color: white; }
    </style>
</head>
<body>

    <?php 
        $activePage = 'users';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="header">
            <form action="/EventManagementSystem/public/admin/events" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search events, bookings, or users...">
                <button type="submit" style="display:none;"></button>
            </form>
            <div class="header-icons">
                <i class="far fa-bell"></i>
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <section class="page-title-section" style="margin-bottom: 30px;">
            <h1 style="font-size: 24px; color: #1e293b;">User Management</h1>
            <p style="color: #64748b; font-size: 14px;">Manage system roles and access for all system users.</p>
        </section>

        <!-- Quick Stats -->
        <div class="stats-row" style="margin-bottom: 30px;">
            <div class="stat-card">
                <p>TOTAL USERS</p>
                <h3><?php echo number_format($stats['total']); ?></h3>
            </div>
            <div class="stat-card">
                <p>CLIENTS</p>
                <h3 style="color: #059669;"><?php echo number_format($stats['clients']); ?></h3>
            </div>
            <div class="stat-card">
                <p>ORGANIZERS</p>
                <h3 style="color: #c2410c;"><?php echo number_format($stats['organizers']); ?></h3>
            </div>
            <div class="stat-card">
                <p>BLOCKED</p>
                <h3 style="color: #ef4444;"><?php echo number_format($stats['blocked']); ?></h3>
            </div>
        </div>

        <!-- User Table -->
        <div class="recent-bookings">
            <div class="section-header" style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9;">
                <div class="search-bar" style="max-width: 300px; margin: 0; background: none;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="userSearchInput" placeholder="Search by name or email...">
                </div>
                <div class="filters" style="display: flex; gap: 10px;">
                    <div class="custom-premium-dropdown small" id="roleFilter">
                        <div class="dropdown-trigger">
                            <span class="selected-val">All Roles</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div class="dropdown-menu">
                            <div class="dropdown-item active" data-value="all">All Roles</div>
                            <div class="dropdown-item" data-value="admin">Admin</div>
                            <div class="dropdown-item" data-value="organizer">Organizer</div>
                            <div class="dropdown-item" data-value="client">Client</div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="user-row" data-name="<?php echo strtolower(htmlspecialchars($user['fullname'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($user['email'])); ?>" data-role="<?php echo strtolower($user['role']); ?>">
                        <td>
                            <div class="client-info">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                <?php else: ?>
                                    <div style="width: 32px; height: 32px; background: #f0f7f3; color: #246A55; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                                        <?php 
                                            $nameArr = explode(' ', $user['fullname']);
                                            $initArr = array_filter($nameArr);
                                            $init = '';
                                            foreach(array_slice($initArr, 0, 2) as $n) $init .= strtoupper(substr($n, 0, 1));
                                            echo $init ?: '??';
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <span style="margin-left: 12px;"><?php echo htmlspecialchars($user['fullname']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td style="font-size: 13px; color: #64748b;">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $user['is_blocked'] ? 'status-blocked' : 'status-active'; ?>">
                                <?php echo $user['is_blocked'] ? 'Blocked' : 'Active'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form action="/EventManagementSystem/public/admin/users/update-role" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                    <div class="custom-premium-dropdown small" data-auto-submit="true" style="width: 120px;">
                                        <div class="dropdown-trigger" style="height: 32px; padding: 0 10px;">
                                            <span class="selected-val"><?php echo ucfirst($user['role']); ?></span>
                                            <i class="fa-solid fa-angle-down"></i>
                                        </div>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item <?php echo $user['role'] == 'client' ? 'active' : ''; ?>" data-value="client">Client</div>
                                            <div class="dropdown-item <?php echo $user['role'] == 'organizer' ? 'active' : ''; ?>" data-value="organizer">Organizer</div>
                                            <div class="dropdown-item <?php echo $user['role'] == 'admin' ? 'active' : ''; ?>" data-value="admin">Admin</div>
                                        </div>
                                    </div>
                                </form>
                                
                                <form action="/EventManagementSystem/public/admin/users/toggle-block" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $user['is_blocked'] ? '0' : '1'; ?>">
                                    <?php if ($user['is_blocked']): ?>
                                        <button type="submit" class="action-btn-outline btn-unblock">Unblock</button>
                                    <?php else: ?>
                                        <button type="button" class="action-btn-outline btn-block" onclick="showBlockModal(<?php echo $user['id']; ?>, '<?php echo addslashes($user['fullname']); ?>')">Block</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Block Confirm Modal -->
    <div class="modal-overlay" id="blockModal">
        <div class="modal-card">
            <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3>Block This User?</h3>
            <p id="blockModalText">Are you sure you want to block this user? They will no longer be able to log in or access the system.</p>
            <div class="modal-btns">
                <button class="modal-btn btn-cancel" onclick="closeBlockModal()">Cancel</button>
                <form id="blockForm" action="/EventManagementSystem/public/admin/users/toggle-block" method="POST">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <input type="hidden" name="status" value="1">
                    <button type="submit" class="modal-btn btn-confirm-delete">Yes, Block User</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showBlockModal(userId, name) {
            document.getElementById('modalUserId').value = userId;
            document.getElementById('blockModalText').innerText = `${name} will no longer be able to log in or access their curated event dashboard. This action can be reversed by an administrator.`;
            document.getElementById('blockModal').style.display = 'flex';
        }
        function closeBlockModal() {
            document.getElementById('blockModal').style.display = 'none';
        }

        // Filtering Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('userSearchInput');
            const roleFilter = document.getElementById('roleFilter');
            const userRows = document.querySelectorAll('.user-row');

            function filterUsers() {
                const searchQ = searchInput.value.toLowerCase();
                const roleQ = roleFilter.value.toLowerCase();

                userRows.forEach(row => {
                    const name = row.dataset.name;
                    const email = row.dataset.email;
                    const role = row.dataset.role;

                    const matchesSearch = name.includes(searchQ) || email.includes(searchQ);
                    const matchesRole = roleQ === 'all' || role === roleQ;

                    if (matchesSearch && matchesRole) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterUsers);
            
            // Re-sync with custom dropdown
            document.getElementById('roleFilter').addEventListener('change', (e) => {
                const val = e.detail.value;
                const roleFilterEl = document.getElementById('roleFilter'); // Already exists as const in outer scope but local to DOMContentLoaded callback init
                // We overwrite roleQ in the parent function or just call it
                filterUsers();
            });
            
            // Need to update the filterUsers to read from custom component
            const originalFilterUsers = filterUsers;
            filterUsers = function() {
                const searchQ = searchInput.value.toLowerCase();
                const roleItem = document.querySelector('#roleFilter .dropdown-item.active');
                const roleQ = roleItem ? roleItem.dataset.value : 'all';

                userRows.forEach(row => {
                    const name = row.dataset.name;
                    const email = row.dataset.email;
                    const role = row.dataset.role;

                    const matchesSearch = name.includes(searchQ) || email.includes(searchQ);
                    const matchesRole = roleQ === 'all' || role === roleQ;

                    if (matchesSearch && matchesRole) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
