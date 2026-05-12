<?php
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
        ['title' => 'Gourmet Multi-cuisine Catering'],
        ['title' => 'Pro-cinematography & Photo'],
        ['title' => 'Live Music & Sound Engineering'],
        ['title' => 'Lead Event Coordinator']
    ];
}

$title = htmlspecialchars($event['title']) . ' - e-Plan';
$activePage = 'events';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-event.css?v=' . time() . '">
';
include 'partials/header.php';
?>



    <div class="container">
        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="/EventManagementSystem/public/client/home">Home</a> &gt;
            <a href="/EventManagementSystem/public/client/events">Browse Events</a> &gt;
            <span class="current"><?php echo htmlspecialchars($event['title']); ?></span>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <?php
            if (!empty($event['image_path'])) {
                $image = ($event['image_path'][0] === '/') ? $event['image_path'] : '/EventManagementSystem/public/assets/images/events/' . $event['image_path'];
            } else {
                $image = '/EventManagementSystem/public/assets/images/placeholder.jpg';
            }
            ?>
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
                    <span id="whats-included-subtitle"
                        style="font-size: 13px; font-weight: 600; color: #bfa15f; text-transform: uppercase;">All
                        Packages</span>
                </div>
                <div class="included-grid" id="includedGrid">
                    <?php foreach ($includedItemsList as $item): ?>
                        <div class="included-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <div style="display: flex; flex-direction: column;">
                                <span><?php echo htmlspecialchars($item['title']); ?></span>
                                <?php if (!empty($item['description'])): ?>
                                    <span
                                        style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;"><?php echo htmlspecialchars($item['description']); ?></span>
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
                            <p><?php echo htmlspecialchars($event['venue_name'] ?: $event['venue_location'] ?: 'Location TBD'); ?>
                            </p>
                            <span
                                style="font-size:11px; color:#6b7280;"><?php echo htmlspecialchars($event['venue_location']); ?></span>
                        </div>
                        <div class="detail-col">
                            <h4>Status</h4>
                            <p class="status-open">
                                <?php echo $event['status'] === 'active' ? 'Booking Open' : ucfirst($event['status']); ?>
                            </p>
                        </div>
                        <?php if (!empty($event['event_date'])): ?>
                            <div class="detail-col">
                                <h4>Date & Time</h4>
                                <p><?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                                <span style="font-size:11px; color:#6b7280;">Scheduled at
                                    <?php echo date('h:i A', strtotime($event['event_date'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="packages-box">
                    <h3>Choose Your Package</h3>

                    <?php
                    // Render packages elegantly
                    if (empty($packages)) {
                        echo '<p class="no-data">No package information available for this event.</p>';
                    }

                    $tiersToRender = ['basic', 'standard', 'premium'];

                    foreach ($tiersToRender as $tierKey):
                        if (isset($packages[$tierKey])):
                            $pkgData = $packages[$tierKey];

                            $cssClass = '';
                            if ($tierKey === 'standard')
                                $cssClass = 'standard';
                            if ($tierKey === 'premium')
                                $cssClass = 'premium';

                            $priceValue = $pkgData['price'] ?? ($pkgData['price_range'] ?? '');
                            $priceDisplay = !empty($priceValue) ? 'Rs. ' . number_format((float) str_replace(['Rs.', ',', ' '], '', $priceValue), 0) : 'Custom Pricing';
                            ?>
                            <div class="package-tier <?php echo $cssClass; ?>"
                                onclick="selectPackage('<?php echo $tierKey; ?>', this)">
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

                    <button class="btn-book-now" onclick="proceedToBooking(<?php echo $event['id']; ?>)">
                        Book Now <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button class="btn-modify-package" onclick="modifyPackage(<?php echo $event['id']; ?>)" style="margin-top: 10px; width: 100%; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">
                        Modify Package <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    
                    <p class="tax-note">* Prices are exclusive of taxes and subject to customization.</p>
                    <div class="policy-badge"
                        style="margin-top: 15px; padding: 10px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 11px; color: #92400e; display: flex; align-items: start; gap: 8px; line-height: 1.4;">
                        <i class="fa-solid fa-circle-info" style="margin-top: 2px;"></i>
                        <span><b>Payment Policy:</b> Securing this event requires a non-refundable <b>50% advance
                                payment</b> online. The remaining balance will be collected in cash on the event
                            day.</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

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
                subtitle.innerText = "All Packages";
                return;
            }

            const items = packagesData[tierKey].items;
            subtitle.innerText = tierKey + " Package";

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

        function proceedToBooking(eventId) {
            const activeTierEl = document.querySelector('.package-tier.active-tier');
            if (!activeTierEl) {
                alert('Please select a package tier first.');
                return;
            }

            // Find which tier is selected by checking inner text or class
            let selectedTier = 'basic';
            if (activeTierEl.classList.contains('standard')) selectedTier = 'standard';
            if (activeTierEl.classList.contains('premium')) selectedTier = 'premium';

            window.location.href = `/EventManagementSystem/public/client/book?id=${eventId}&package=${selectedTier}`;
        }

        function modifyPackage(eventId) {
            const activeTierEl = document.querySelector('.package-tier.active-tier');
            if (!activeTierEl) {
                alert('Please select a package tier to modify first.');
                return;
            }

            let selectedTier = 'basic';
            if (activeTierEl.classList.contains('standard')) selectedTier = 'standard';
            if (activeTierEl.classList.contains('premium')) selectedTier = 'premium';

            window.location.href = `/EventManagementSystem/public/client/events/modify?id=${eventId}&package=${selectedTier}`;
        }
    </script>


    <script>
        (function () {
            if (!window.emsApi) return;
            // Optional: refresh this event data from API using query id for parity
            const params = new URLSearchParams(window.location.search);
            const id = params.get('id');
            if (!id) return;
            window.emsApi.apiFetch(`/api/v1/events/${id}`)
                .then(() => { /* if needed later, can live-update DOM */ })
                .catch(() => { /* keep PHP render */ });
        })();
    </script>

<?php include 'partials/footer.php'; ?>