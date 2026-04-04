<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        .upcoming-feature-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        .upcoming-feature-container i {
            font-size: 64px;
            color: var(--accent-color);
            margin-bottom: 20px;
        }
        .upcoming-feature-container h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
        }
        .upcoming-feature-container p {
            font-size: 16px;
            color: var(--text-muted);
            max-width: 500px;
        }
    </style>
</head>
<body>
    <?php 
        $activePage = 'messages';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>
    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <span class="current">Messages</span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
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
                    <div class="user-avatar-small">
                        <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                    </div>
                </div>
            </div>
        </header>

        <section class="upcoming-feature-container">
            <i class="far fa-envelope-open"></i>
            <h2>Messaging Center</h2>
            <p>Our messaging system is an upcoming feature! Soon, you'll be able to communicate directly with clients.</p>
        </section>
    </main>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
