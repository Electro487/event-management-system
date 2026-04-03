<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/browse-events.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .home-container {
            max-width: 1200px;
            margin: 100px auto;
            text-align: center;
            padding: 40px;
            min-height: 50vh;
        }

        .home-title {
            font-size: 3rem;
            color: #1f6f59;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .home-subtitle {
            font-size: 1.25rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo"><img
                src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;"></a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/home" class="active">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
            <?php endif; ?>
        </nav>
        <div class="nav-icons">
            <i class="fa-regular fa-bell" style="font-size: 20px; color: #1f6f59; cursor: pointer;"></i>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div
                    style="width: 32px; height: 32px; background: #1f6f59; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; cursor: pointer;">
                    <?php echo strtoupper(substr($_SESSION['user_fullname'], 0, 1)); ?>
                </div>
            <?php else: ?>
                <a href="/EventManagementSystem/public/login"
                    style="color: #1f6f59; font-weight: 600; text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="home-container">
        <h1 class="home-title">Home</h1>
        <p class="home-subtitle">Welcome to your e-Plan Home. This page is currently under development and will soon be
            the hub for all your event management and planning activities.</p>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-left">
            <div class="footer-logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                    style="height: 28px; width: auto; object-fit: contain;"></div>
            <p>&copy; 2026 e.plan Architectural Event Curation. All rights reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Support</a>
        </div>
    </footer>

</body>

</html>