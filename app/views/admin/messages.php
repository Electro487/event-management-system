<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <style>
        .messaging-grid { display: grid; grid-template-columns: 1fr; gap: 15px; margin-top: 20px; }
        .conversation-card {
            background: white; border-radius: 12px; padding: 18px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; transition: all 0.2s; text-decoration: none; color: inherit;
        }
        .conversation-card:hover { transform: translateX(5px); border-color: #246A55; background: #f0fdfa; }
        .convo-info { display: flex; align-items: center; gap: 15px; }
        .convo-avatar {
            width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #1e40af; border: 1px solid #dbeafe;
        }
        .convo-text h3 { margin: 0; font-size: 15px; color: #1e293b; }
        .convo-text p { margin: 2px 0 0; font-size: 13px; color: #64748b; }
        .convo-meta { text-align: right; }
        .convo-time { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
        .status-pill { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-negotiating { background: #dbeafe; color: #1e40af; }
        .status-approved { background: #dcfce7; color: #166534; }
    </style>
</head>
<body class="admin-panel">
    <?php 
        $activePage = 'messages';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>
    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <span style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Admin Panel</span>
                    <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin: 0 8px; color: #94a3b8;"></i>
                    <span class="current">Negotiation Messages</span>
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

        <section class="messages-list" style="max-width: 1000px;">
            <div style="margin-bottom: 25px;">
                <h2 style="margin:0; font-size: 22px; font-weight: 800; color: #0f172a;">Messaging Hub</h2>
                <p style="color: #64748b; margin-top: 5px;">Showing negotiations for events you've organized.</p>
            </div>

            <?php if (empty($requests)): ?>
                <div style="text-align:center; padding: 80px 20px; background:white; border-radius:16px; border:1px solid #eef2f6;">
                    <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-regular fa-comments" style="font-size: 28px; color: #94a3b8;"></i>
                    </div>
                    <h3 style="color: #1e293b; font-weight: 700;">No active threads</h3>
                    <p style="color: #64748b; max-width: 400px; margin: 10px auto;">When clients request customizations for your events, they'll appear here.</p>
                </div>
            <?php else: ?>
                <div class="messaging-grid">
                    <?php foreach ($requests as $request): ?>
                        <a href="/EventManagementSystem/public/organizer/requests/view?id=<?php echo $request['id']; ?>" class="conversation-card">
                            <div class="convo-info">
                                <div class="convo-avatar">
                                    <?php echo strtoupper(substr($request['client_name'], 0, 1)); ?>
                                </div>
                                <div class="convo-text">
                                    <h3><?php echo htmlspecialchars($request['client_name']); ?> <small style="color: #94a3b8; font-weight: 400; margin-left: 8px;">(Client)</small></h3>
                                    <p>Negotiation for event: <span style="font-weight: 600; color: #475569;"><?php echo htmlspecialchars($request['event_title']); ?></span></p>
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
