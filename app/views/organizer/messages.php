<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <style>
        .messaging-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .conversation-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .conversation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        .convo-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .convo-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary-color);
            object-fit: cover;
        }
        .convo-text h3 {
            margin: 0;
            font-size: 16px;
            color: #1e293b;
        }
        .convo-text p {
            margin: 4px 0 0;
            font-size: 14px;
            color: #64748b;
        }
        .convo-meta {
            text-align: right;
        }
        .convo-time {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 5px;
        }
        .status-pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-negotiating { background: #dbeafe; color: #1e40af; }
        .status-approved { background: #dcfce7; color: #166534; }
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
                    <span class="current">Messaging Center</span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-icons">
                    <div class="notifications-wrapper">
                        <div class="notification-bell-btn" id="notification-bell">
                            <i class="fa-regular fa-bell"></i>
                            <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                        </div>
                    </div>
                    <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                </div>
            </div>
        </header>

        <section class="messages-list">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h2 style="margin:0; font-size: 24px; font-weight: 800;">Conversations</h2>
                <span style="color:#64748b; font-size: 14px;"><?php echo count($requests); ?> active negotiations</span>
            </div>

            <?php if (empty($requests)): ?>
                <div style="text-align:center; padding: 60px 20px; background:white; border-radius:12px; border:1px solid #e2e8f0;">
                    <i class="far fa-envelope-open" style="font-size: 48px; color:#cbd5e1; margin-bottom:15px;"></i>
                    <h3>No conversations yet</h3>
                    <p style="color:#64748b;">Negotiation messages will appear here when clients request package customizations.</p>
                </div>
            <?php else: ?>
                <div class="messaging-grid">
                    <?php foreach ($requests as $request): ?>
                        <a href="/EventManagementSystem/public/organizer/requests/view?id=<?php echo $request['id']; ?>" class="conversation-card">
                            <div class="convo-info">
                                <div class="convo-avatar">
                                    <?php 
                                        $initials = strtoupper(substr($request['client_name'], 0, 1));
                                        echo $initials;
                                    ?>
                                </div>
                                <div class="convo-text">
                                    <h3><?php echo htmlspecialchars($request['client_name']); ?></h3>
                                    <p>Negotiation for: <strong><?php echo htmlspecialchars($request['event_title']); ?></strong></p>
                                </div>
                            </div>
                            <div class="convo-meta">
                                <div class="convo-time"><?php echo date('M d, H:i', strtotime($request['created_at'])); ?></div>
                                <span class="status-pill status-<?php echo strtolower($request['status']); ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
