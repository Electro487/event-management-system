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
            <?php $image = !empty($event['image_path']) ? $event['image_path'] : '/EventManagementSystem/public/assets/images/placeholder.jpg'; ?>
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
            <div class="hero-content">
                <span class="category-tag"><?php echo htmlspecialchars($event['category'] ?: 'Event'); ?></span>
                <h1><?php echo htmlspecialchars($event['title']); ?></h1>
                <p>Curating timeless moments for your once-in-a-lifetime celebration with architectural precision.</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            
            <!-- Left Column -->
            <div class="left-col">
                <h2 class="section-title">About This Event</h2>
                <div class="about-text">
                    <?php 
                        if (!empty($event['description'])) {
                            echo nl2br(htmlspecialchars($event['description']));
                        } else {
                            echo "Your event is a tapestry of moments that define your journey together. At e-Plan, we specialize in transforming your vision into an architectural masterpiece of floral arrangements, curated catering, and seamless logistical execution. We handle the structural foundation so you can focus on the heart of the celebration.";
                        }
                    ?>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin-bottom: 0;">What's Included</h2>
                    <span id="whats-included-subtitle" style="font-size: 13px; font-weight: 600; color: #bfa15f; text-transform: uppercase;">All Packages Scope</span>
                </div>
                <div class="included-grid" id="includedGrid">
                    <?php foreach ($includedItemsList as $item): ?>
                        <div class="included-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <div style="display: flex; flex-direction: column;">
                                <span><?php echo htmlspecialchars($item['title']); ?></span>
                                <?php if (!empty($item['description'])): ?>
                                    <span style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;"><?php echo htmlspecialchars($item['description']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="details-box">
                    <h2 class="section-title">Event Details</h2>
                    <div class="details-grid">
                        <div class="detail-col">
                            <h4>Location</h4>
                            <p><?php echo htmlspecialchars($event['venue_name'] ?: $event['venue_location'] ?: 'Location TBD'); ?></p>
                            <span style="font-size:11px; color:#6b7280;"><?php echo htmlspecialchars($event['venue_location']); ?></span>
                        </div>
                        <div class="detail-col">
                            <h4>Status</h4>
                            <p class="status-open"><?php echo $event['status'] === 'active' ? 'Booking Open' : ucfirst(htmlspecialchars($event['status'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="packages-box">
                    <h3>Service Tier Configuration</h3>

                    <?php 
                    if (empty($packages)) {
                        echo '<p class="no-data">No package information configured for this event.</p>';
                    }

                    $tiersToRender = ['basic', 'standard', 'premium'];
                    
                    foreach ($tiersToRender as $tierKey):
                        if (isset($packages[$tierKey])):
                            $pkgData = $packages[$tierKey];
                            
                            $cssClass = '';
                            if ($tierKey === 'standard') $cssClass = 'standard';
                            if ($tierKey === 'premium') $cssClass = 'premium';
                            
                            $priceValue = $pkgData['price'] ?? ($pkgData['price_range'] ?? '');
                            $priceDisplay = !empty($priceValue) ? 'Rs. ' . number_format((float)str_replace(['Rs.', ',', ' '], '', $priceValue), 0) : 'Custom Pricing';
                    ?>
                    <div class="package-tier <?php echo $cssClass; ?>" onclick="selectPackage('<?php echo $tierKey; ?>', this)">
                        <?php if ($tierKey === 'standard'): ?>
                            <div class="most-popular-badge">Most Popular</div>
                        <?php endif; ?>

                        <div class="package-header">
                            <div class="tier-name"><?php echo ucfirst($tierKey); ?></div>
                            <div class="tier-price"><?php echo htmlspecialchars($priceDisplay); ?></div>
                        </div>
                        <div class="tier-desc">
                            <?php echo htmlspecialchars($pkgData['description'] ?: 'Complete set of services curated for this tier.'); ?>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                    <p class="tax-note" style="margin-top: 20px;">* Read-only view for administrative oversight.</p>
                </div>

                <div class="trust-badges" style="justify-content: center; margin-top: 15px;">
                    <span style="margin: 0 auto;"><i class="fa-solid fa-lock"></i> Administrative Access</span>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Pass PHP packages array to JS
        const packagesData = <?php echo json_encode($packages); ?>;
        const globalItemsHtml = document.getElementById('includedGrid').innerHTML;

        function selectPackage(tierKey, element) {
            // Remove active class from all tiers
            document.querySelectorAll('.package-tier').forEach(el => {
                el.classList.remove('active-tier');
            });

            // Add active class to clicked tier
            if (element) {
                element.classList.add('active-tier');
            }

            const includedGrid = document.getElementById('includedGrid');
            const subtitle = document.getElementById('whats-included-subtitle');

            if (!tierKey || !packagesData[tierKey] || !packagesData[tierKey].items || packagesData[tierKey].items.length === 0) {
                includedGrid.innerHTML = globalItemsHtml;
                subtitle.innerText = "All Packages Scope";
                return;
            }

            const items = packagesData[tierKey].items;
            subtitle.innerText = tierKey.charAt(0).toUpperCase() + tierKey.slice(1) + " Package Scope";

            let html = '';
            items.forEach(item => {
                const title = escapeHtml(item.title);
                const desc = item.description ? escapeHtml(item.description) : '';

                html += `
                <div class="included-item">
                    <i class="fa-solid fa-circle-check"></i>
                    <div style="display: flex; flex-direction: column;">
                        <span>${title}</span>
                        ${desc ? `<span style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;">${desc}</span>` : ''}
                    </div>
                </div>
            `;
            });

            includedGrid.innerHTML = html;
        }

        function escapeHtml(unsafe) {
            return (unsafe || "").toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>
