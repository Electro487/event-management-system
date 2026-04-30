<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All System Events - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-events.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>

    <?php 
        $activePage = 'events';
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

        <section class="events-summary">
            <div class="summary-text">
                <h1>All System Events</h1>
                <p>Oversight of all curated event campaigns across the entire platform.</p>
            </div>
            <div class="summary-actions">
                <a href="/EventManagementSystem/public/admin/events/create" class="btn-create-admin">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
        </section>

        <section class="filters-bar">
            <div class="tabs">
                <button class="tab-btn active" data-filter-status="all">All</button>
                <button class="tab-btn" data-filter-status="active">Active</button>
                <button class="tab-btn" data-filter-status="draft">Draft</button>
            </div>
            <div class="category-filter">
                <div class="custom-premium-dropdown small" id="categoryFilter">
                    <div class="dropdown-trigger">
                        <span class="selected-val">All Categories</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-item active" data-value="all">All Categories</div>
                        <?php 
                        $cats = ["Weddings", "Meetings", "Cultural Events", "Family Functions", "Other Events and Programs"];
                        foreach($cats as $cat): ?>
                            <div class="dropdown-item" data-value="<?php echo strtolower($cat); ?>"><?php echo $cat; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="status-info">
                <span id="eventsCountLabel">Showing 0 system campaigns</span>
            </div>
        </section>

        <div class="events-grid" id="eventsGrid">
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #999;">
                <p>Loading system events...</p>
            </div>
        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/events.js?v=<?php echo time(); ?>"></script>
</body>
</html>
