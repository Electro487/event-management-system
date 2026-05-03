<?php 
/** @var array $event */ 
$packages = isset($event['packages']) ? json_decode($event['packages'], true) : [];

// Extract unique items from all packages to show in "What's Included"
$includedItems = [];
if (is_array($packages)) {
    foreach ($packages as $tier => $pkgData) {
        if (!empty($pkgData['items'])) {
            foreach ($pkgData['items'] as $item) {
                if (!empty($item['title'])) {
                    $includedItems[$item['title']] = $item;
                }
            }
        }
    }
}
$includedItemsList = array_values($includedItems);
if (empty($includedItemsList)) {
    $includedItemsList = [
        ['title' => 'Bespoke Floral Decoration'],
        ['title' => 'Premium Heritage Venue Setup'],
        ['title' => 'Gourmet Multi-cuisine Catering']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <!-- Import premium view event styles -->
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-event.css?v=<?php echo time(); ?>">
    <style>
        /* Small overrides to ensure view-event.css works nicely within admin layout */
        .main-content { padding: 30px; background-color: var(--bg-light); min-height: 100vh; }
        .hero { margin-top: 20px; border-radius: 16px; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: var(--primary-light); text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-back:hover { text-decoration: underline; }
        /* Enable interactions on tiers for admin now that they are interactive */
        .package-tier { cursor: pointer; }
    </style>
</head>
<body>
    <?php 
        $activePage = 'events';
        include_once dirname(__DIR__) . "/admin/partials/sidebar.php"; 
    ?>
    
    <main class="main-content">
        <a href="/EventManagementSystem/public/admin/events" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Events</a>
        
        <!-- Hero Section -->
        <div class="hero">
            <img src="/EventManagementSystem/public/assets/images/placeholder.jpg" alt="Event" id="hero-img">
            <div class="hero-content">
                <span class="category-tag" id="event-category-label">...</span>
                <h1 id="event-title-header">...</h1>
                <p id="event-tagline">...</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            
            <!-- Left Column -->
            <div class="left-col">
                <h2 class="section-title">About This Event</h2>
                <div class="about-text" id="event-description-body">
                    Loading description...
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin-bottom: 0;">What's Included</h2>
                    <span id="whats-included-subtitle" style="font-size: 13px; font-weight: 600; color: #bfa15f; text-transform: uppercase;">All Packages Scope</span>
                </div>
                <div class="included-grid" id="includedGrid">
                    <div class="included-item"><span>Loading details...</span></div>
                </div>

                <div class="details-box">
                    <h2 class="section-title">Event Details</h2>
                    <div class="details-grid">
                        <div class="detail-col">
                            <h4>Location</h4>
                            <p id="event-venue-name">...</p>
                            <span style="font-size:11px; color:#6b7280;" id="event-venue-location">...</span>
                        </div>
                        <div id="scheduleColumn" class="detail-col" style="display: none;">
                            <h4>Event Schedule</h4>
                            <p id="eventSchedule">Fixed Date & Time</p>
                            <span style="font-size:11px; color:#6b7280;">Fixed by Organizer</span>
                        </div>
                        <div class="detail-col">
                            <h4>Status</h4>
                            <p class="status-open" id="event-status-label">...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="packages-box">
                    <h3>Service Tier Configuration</h3>
                    <div id="packages-list-container">
                        <p class="no-data">Loading packages...</p>
                    </div>
                    <p class="tax-note" style="margin-top: 20px;">* Read-only view for administrative oversight.</p>
                </div>

                <div class="trust-badges" style="justify-content: center; margin-top: 15px;">
                    <span style="margin: 0 auto;"><i class="fa-solid fa-lock"></i> Administrative Access</span>
                </div>
            </div>

        </div>
    </main>

    <script>
        window.EVENT_ID = <?php echo (int)($event['id'] ?? 0); ?>;
    </script>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/view_event.js?v=<?php echo time(); ?>"></script>
</body>
</html>
