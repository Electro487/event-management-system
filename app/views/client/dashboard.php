<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/dashboards.css">
</head>
<body>
    <div class="card">
        <h1>Client Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>!</p>
        <p>This is your simple client dashboard.</p>
        <a href="/EventManagementSystem/public/logout" class="logout-btn">Logout</a>
    </div>
</body>
</html>
