<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/user-management.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php 
        $activePage = 'users';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="globalSearchInput" placeholder="Search system-wide events..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="header-icons">
                <div class="notifications-wrapper">
                    <div class="notification-bell-btn" id="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                    </div>
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
                <h3 id="stat-total-users">0</h3>
            </div>
            <div class="stat-card">
                <p>CLIENTS</p>
                <h3 style="color: #059669;" id="stat-clients">0</h3>
            </div>
            <div class="stat-card">
                <p>ORGANIZERS</p>
                <h3 style="color: #c2410c;" id="stat-organizers">0</h3>
            </div>
            <div class="stat-card">
                <p>BLOCKED</p>
                <h3 style="color: #ef4444;" id="stat-blocked">0</h3>
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
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px;">Loading users...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="pagination-container" style="margin-top: 20px;"></div>
    </main>

    <!-- Block Confirm Modal -->
    <div class="modal-overlay" id="blockModal">
        <div class="modal-card">
            <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3>Block This User?</h3>
            <p id="blockModalText">Are you sure you want to block this user? They will no longer be able to log in or access the system.</p>
            <div class="modal-btns">
                <button class="modal-btn btn-cancel" onclick="closeBlockModal()">Cancel</button>
                <div id="blockForm">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <button type="button" class="modal-btn btn-confirm-delete">Yes, Block User</button>
                </div>
            </div>
        </div>
    </div>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/user_management.js?v=<?php echo time(); ?>"></script>
</body>
</html>
