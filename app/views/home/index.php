<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | e.PLAN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f7f8fa;
            color: #1a1e23;
            line-height: 1.6;
        }
        .header {
            background-color: #ffffff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .logo {
            font-weight: 700;
            font-size: 20px;
            color: #1a1e23;
        }
        .nav-links a {
            color: #6b7280;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        .nav-links a.logout {
            color: #ef4444;
        }
        .container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 40px;
        }
        .welcome-card {
            background-color: #ffffff;
            padding: 60px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
        }
        .welcome-card h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #1a1e23;
        }
        .welcome-card p {
            color: #6b7280;
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .btn-action {
            display: inline-block;
            padding: 14px 28px;
            background-color: #1a1e23;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn-action:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">e.PLAN</div>
    <nav class="nav-links">
        <a href="#">Dashboard</a>
        <a href="#">Events</a>
        <a href="/EventManagementSystem/public/logout" class="logout">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="welcome-card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?>!</h2>
        <p>You have successfully logged into the Event Management System. Explore your dashboard to manage your curated event experiences.</p>
        <a href="#" class="btn-action">View My Events</a>
    </div>
</div>

</body>
</html>
