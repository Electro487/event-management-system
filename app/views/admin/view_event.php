<?php /** @var array $event */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event - <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-event.css">
</head>
<body>
    <?php 
        include_once dirname(__DIR__) . "/admin/partials/sidebar.php"; 
    ?>
    
    <main class="main-content">
        <a href="/EventManagementSystem/public/admin/events" class="btn-back">← Back to Dashboard</a>
        
        <div class="event-view-header">
            <img src="<?php echo $event['image_path'] ?: '/EventManagementSystem/public/assets/images/placeholder.jpg'; ?>" class="event-main-img" alt="">
            <div class="event-info">
                <span class="badge <?php echo strtolower($event['status']); ?>"><?php echo $event['status']; ?></span>
                <h1><?php echo htmlspecialchars($event['title']); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <h4>Category</h4>
                        <p><?php echo htmlspecialchars($event['category']); ?></p>
                    </div>
                    <div class="detail-item">
                        <h4>Venue</h4>
                        <p><?php echo htmlspecialchars($event['venue_name']); ?></p>
                    </div>
                    <div class="detail-item">
                        <h4>Location</h4>
                        <p><?php echo htmlspecialchars($event['venue_location']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="package-section">
            <h2>Service Packages</h2>
            <?php 
            $packages = json_decode($event['packages'], true);
            if (!empty($packages)): 
                foreach ($packages as $pkg): ?>
                <div class="package-card">
                    <?php if (is_array($pkg)): ?>
                        <h3><?php echo htmlspecialchars($pkg['title'] ?? 'Package'); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($pkg['description'] ?? '')); ?></p>
                    <?php else: ?>
                        <h3>Package</h3>
                        <p><?php echo nl2br(htmlspecialchars((string)$pkg)); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; 
            else: ?>
                <p>No packages defined for this event.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
