<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
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
                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                    <div class="user-avatar-small">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_fullname'] ?? 'User'); ?>&background=0D8ABC&color=fff" alt="Profile" style="border-radius:50%; object-fit:cover;">
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
</body>
</html>
