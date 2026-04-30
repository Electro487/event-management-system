<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event | Organizer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/view-event.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        .main-content { padding: 30px; background-color: var(--bg-light); min-height: 100vh; }
        .hero { margin-top: 20px; border-radius: 16px; min-height: 350px; }
        .btn-back-link { display: inline-block; margin-bottom: 20px; color: var(--primary-light); text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-back-link:hover { text-decoration: underline; }
        
        .management-actions {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .btn-edit-event {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background: var(--primary-light);
            color: white;
            padding: 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.2s;
        }
        
        .btn-edit-event:hover {
            background: #1b5343;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 83, 67, 0.2);
        }

        .loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; font-weight: 600;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" class="loading-overlay">Loading event details...</div>

    <?php 
        $activePage = 'events';
        include_once __DIR__ . "/partials/sidebar.php"; 
    ?>
    
    <main class="main-content" id="mainContent" style="display: none;">
        <a href="/EventManagementSystem/public/organizer/events" class="btn-back-link"><i class="fas fa-arrow-left"></i> Back to My Events</a>
        
        <!-- Hero Section -->
        <div class="hero">
            <img id="eventHeroImg" src="/EventManagementSystem/public/assets/images/placeholder.jpg" alt="Event">
            <div class="hero-content">
                <span id="categoryTag" class="category-tag">Event</span>
                <h1 id="eventTitle">Event Title</h1>
                <p>Previewing your event as it appears to potential clients with our premium architectural layout.</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            
            <!-- Left Column -->
            <div class="left-col">
                <h2 class="section-title">About This Event</h2>
                <div id="eventDescription" class="about-text">
                    Loading description...
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin-bottom: 0;">What's Included</h2>
                    <span id="whats-included-subtitle" style="font-size: 13px; font-weight: 600; color: #bfa15f; text-transform: uppercase;">All Packages Scope</span>
                </div>
                <div class="included-grid" id="includedGrid">
                    <!-- Included items injected -->
                </div>

                <div class="details-box">
                    <h2 class="section-title">Event Logistics</h2>
                    <div class="details-grid">
                        <div class="detail-col">
                            <h4>Venue Details</h4>
                            <p id="venueName">Venue TBD</p>
                            <span id="venueLocation" style="font-size:11px; color:#6b7280;">Location</span>
                        </div>
                        <div class="detail-col">
                            <h4>Public Status</h4>
                            <p id="publicStatus" class="status-open">Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <div class="management-actions">
                    <a id="editEventLink" href="#" class="btn-edit-event">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Event Details
                    </a>
                </div>

                <div class="packages-box" id="packagesBox">
                    <h3>Service Tier Configurations</h3>
                    <!-- Packages injected -->
                </div>

                <div class="trust-badges" style="justify-content: center; margin-top: 15px;">
                    <span style="margin: 0 auto;"><i class="fa-solid fa-eye"></i>Preview Mode</span>
                </div>
            </div>

        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const eventId = new URLSearchParams(window.location.search).get('id');
            const mainContent = document.getElementById('mainContent');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (!eventId) {
                loadingOverlay.textContent = "Error: No event ID provided.";
                return;
            }

            let packagesData = {};
            let globalItems = [];

            async function loadEvent() {
                try {
                    const res = await window.emsApi.apiFetch(`/api/v1/events/${eventId}`);
                    const event = res.data.event;
                    if (!event) throw new Error("Event not found");

                    populateUI(event);
                    loadingOverlay.style.display = 'none';
                    mainContent.style.display = 'block';
                } catch (err) {
                    console.error(err);
                    loadingOverlay.innerHTML = `<p style="color:red;">Error: ${err.message}</p>`;
                }
            }

            function populateUI(e) {
                document.getElementById('eventTitle').textContent = e.title;
                document.getElementById('categoryTag').textContent = e.category || 'Event';
                let rawImg = e.image_path || '';
                let imgUrl = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                if (rawImg) {
                    imgUrl = (rawImg[0] === '/') ? rawImg : '/EventManagementSystem/public/assets/images/events/' + rawImg;
                }
                document.getElementById('eventHeroImg').src = imgUrl;
                document.getElementById('eventDescription').innerHTML = (e.description || 'No description provided.').replace(/\n/g, '<br>');
                document.getElementById('venueName').textContent = e.venue_name || 'Venue TBD';
                document.getElementById('venueLocation').textContent = e.venue_location || '';
                
                const statusEl = document.getElementById('publicStatus');
                const rawStatus = e.status || 'draft';
                statusEl.textContent = rawStatus.charAt(0).toUpperCase() + rawStatus.slice(1);
                statusEl.style.color = rawStatus === 'active' ? '#10b981' : '#6b7280';

                document.getElementById('editEventLink').href = `/EventManagementSystem/public/organizer/events/edit?id=${e.id}`;

                // Packages
                packagesData = JSON.parse(e.packages || '{}');
                const tiers = ['basic', 'standard', 'premium'];
                const packagesBox = document.getElementById('packagesBox');
                
                let pkgsHtml = '<h3>Service Tier Configurations</h3>';
                let hasPkgs = false;
                
                // Collect global items
                const itemMap = {};
                tiers.forEach(t => {
                    if (packagesData[t] && packagesData[t].items) {
                        packagesData[t].items.forEach(it => {
                            if (it.title) itemMap[it.title] = it;
                        });
                    }
                });
                globalItems = Object.values(itemMap);
                if (globalItems.length === 0) {
                    globalItems = [
                        {title: 'Bespoke Floral Decoration'},
                        {title: 'Premium Heritage Venue Setup'},
                        {title: 'Gourmet Multi-cuisine Catering'}
                    ];
                }

                tiers.forEach(t => {
                    if (packagesData[t]) {
                        hasPkgs = true;
                        const pkg = packagesData[t];
                        const cssClass = t === 'standard' ? 'standard' : (t === 'premium' ? 'premium' : '');
                        const price = pkg.price ? `Rs. ${parseFloat(pkg.price).toLocaleString()}` : 'Custom Pricing';
                        
                        pkgsHtml += `
                            <div class="package-tier ${cssClass}" onclick="window.selectPackage('${t}', this)">
                                ${t === 'standard' ? '<div class="most-popular-badge">Most Popular</div>' : ''}
                                <div class="package-header">
                                    <div class="tier-name">${t.charAt(0).toUpperCase() + t.slice(1)}</div>
                                    <div class="tier-price">${price}</div>
                                </div>
                                <div class="tier-desc">${pkg.description || 'Complete set of services curated for this tier.'}</div>
                            </div>
                        `;
                    }
                });

                if (!hasPkgs) {
                    pkgsHtml += '<p class="no-data">No package information configured.</p>';
                }
                packagesBox.innerHTML = pkgsHtml;

                renderIncluded(globalItems, "All Packages Scope");
            }

            function renderIncluded(items, subtitle) {
                const grid = document.getElementById('includedGrid');
                document.getElementById('whats-included-subtitle').textContent = subtitle;
                
                grid.innerHTML = items.map(it => `
                    <div class="included-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <div style="display: flex; flex-direction: column;">
                            <span>${escapeHtml(it.title)}</span>
                            ${it.description ? `<span style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;">${escapeHtml(it.description)}</span>` : ''}
                        </div>
                    </div>
                `).join('');
            }

            window.selectPackage = (tierKey, element) => {
                document.querySelectorAll('.package-tier').forEach(el => el.classList.remove('active-tier'));
                if (element) element.classList.add('active-tier');

                if (!tierKey || !packagesData[tierKey] || !packagesData[tierKey].items || packagesData[tierKey].items.length === 0) {
                    renderIncluded(globalItems, "All Packages Scope");
                } else {
                    renderIncluded(packagesData[tierKey].items, `${tierKey.charAt(0).toUpperCase() + tierKey.slice(1)} Package Scope`);
                }
            };

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            loadEvent();
        });
    </script>
</body>
</html>
