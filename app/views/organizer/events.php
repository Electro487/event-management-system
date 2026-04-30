<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - <?php echo SITE_NAME; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-events.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
</head>

<body>

    <?php
    $activePage = 'events';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <span class="current">Events</span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <div class="notifications-wrapper">
                        <div class="notification-bell-btn" id="notification-bell">
                            <i class="fa-regular fa-bell"></i>
                            <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                        </div>
                        <!-- Notifications Dropdown -->
                        <div class="notifications-dropdown" id="notifications-dropdown">
                            <div class="nd-header">
                                <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 New</span></h3>
                                <a href="javascript:void(0)" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="nd-content" id="nd-list">
                                <div class="nd-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    <p>No new notifications</p>
                                </div>
                            </div>
                            <div class="nd-footer">
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="user-avatar-small">
                        <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                    </div>
                </div>
            </div>
        </header>

        <section class="events-summary">
            <div class="summary-text">
                <h1>Manage Events</h1>
                <p>Curate and oversee your portfolio of high-end corporate and private experiences.</p>
            </div>
            <a href="/EventManagementSystem/public/organizer/events/create" class="btn-primary">+ Add New Event</a>
        </section>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert-success">
                <span>🎉 Event successfully created! Your curated experience is now saved.</span>
                <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; cursor:pointer; font-size:18px;">&times;</button>
            </div>
        <?php endif; ?>

        <section class="filters-bar">
            <div class="tabs">
                <button class="tab-btn active" data-filter-status="all">All</button>
                <button class="tab-btn" data-filter-status="active">Active</button>
                <button class="tab-btn" data-filter-status="draft">Draft</button>
            </div>
            <div class="category-filter">
                <div class="custom-premium-dropdown small" id="categoryFilter">
                    <div class="dropdown-trigger">
                        <span class="selected-val">All Categories</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-item active" data-value="all">All Categories</div>
                        <div class="dropdown-item" data-value="Weddings">Weddings</div>
                        <div class="dropdown-item" data-value="Meetings">Meetings</div>
                        <div class="dropdown-item" data-value="Cultural Events">Cultural Events</div>
                        <div class="dropdown-item" data-value="Family Functions">Family Functions</div>
                        <div class="dropdown-item" data-value="Other Events and Programs">Other Events and Programs</div>
                    </div>
                </div>
            </div>
            <div class="status-info">
                <span id="eventsCount">Loading events...</span>
            </div>
        </section>

        <div class="events-grid" id="eventsGrid">
            <!-- Create Event Placeholder -->
            <a href="/EventManagementSystem/public/organizer/events/create" class="event-card create-placeholder">
                <div class="placeholder-content">
                    <div class="plus-circle">+</div>
                    <h3>+ Create your event</h3>
                    <p>Start a new project and define your curated experience from scratch.</p>
                </div>
            </a>

            <!-- Events will be dynamically injected here by JS -->
            <div id="events-loading" style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #999;">
                <p>Loading events...</p>
            </div>
        </div>

        <div class="pagination-row" id="paginationWrapper" style="display: none;">
            <div class="showing-text" id="showingText">
                Showing <span id="visibleCount">0</span> of <span id="totalEventsSpan">0</span> event campaigns
            </div>
            <div class="pagination-controls" id="paginationControls">
                <!-- Pagination buttons will be injected here -->
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const categorySelect = document.getElementById('categoryFilter');
            const countLabel = document.getElementById('eventsCount');
            const eventsGrid = document.getElementById('eventsGrid');
            const loadingMsg = document.getElementById('events-loading');
            const paginationControls = document.getElementById('paginationControls');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const visibleCountLabel = document.getElementById('visibleCount');
            const totalEventsSpan = document.getElementById('totalEventsSpan');

            let currentStatus = 'all';
            let currentCategory = 'all';
            let allEvents = [];
            let currentPage = 1;
            const itemsPerPage = 8; 
            let filteredEvents = [];

            function renderEvents() {
                // Clear existing dynamically generated content
                const dynamicContent = eventsGrid.querySelectorAll('.event-card:not(.create-placeholder), .no-events-message');
                dynamicContent.forEach(item => item.remove());
                
                if (loadingMsg) loadingMsg.style.display = 'none';
                console.log('Rendering events. allEvents:', allEvents);
                let visibleCount = 0;

                filteredEvents = (Array.isArray(allEvents) ? allEvents : []).filter(event => {
                    const status = (event.status || '').toLowerCase().trim();
                    const category = (event.category || '').toLowerCase().trim();
                    const filterCat = currentCategory.toLowerCase().trim();

                    const statusMatch = (currentStatus === 'all' || status === currentStatus);
                    const categoryMatch = (filterCat === 'all' || category === filterCat);

                    return statusMatch && categoryMatch;
                });

                if (filteredEvents.length === 0) {
                    const noEvtMsg = document.createElement('div');
                    noEvtMsg.className = 'no-events-message';
                    noEvtMsg.style = 'grid-column: 1 / -1; text-align: center; padding: 60px; color: #999; width: 100%;';
                    noEvtMsg.innerHTML = `
                        <i class="fa-regular fa-calendar-xmark" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p style="font-size: 16px; font-weight: 500;">No events found matching your criteria.</p>
                    `;
                    eventsGrid.appendChild(noEvtMsg);
                    if(paginationWrapper) paginationWrapper.style.display = 'none';
                } else {
                    if(paginationWrapper) paginationWrapper.style.display = 'flex';
                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    const pageItems = filteredEvents.slice(start, end);

                    pageItems.forEach(event => {
                        const card = document.createElement('div');
                        card.className = 'event-card';
                        card.dataset.status = (event.status || '').toLowerCase();
                        card.dataset.category = event.category || '';
                        
                        const imgPath = event.image_path || '/EventManagementSystem/public/assets/images/placeholder.jpg';
                        const statusClass = (event.status || '').toLowerCase();
                        const statusText = event.status ? (event.status.charAt(0).toUpperCase() + event.status.slice(1)) : '';
                        
                        let statsHtml = `<span class="stat">Bookings: <strong>0</strong></span>`;
                        if (statusClass === 'active') {
                             statsHtml = `<span class="stat">Bookings: <strong>${event.dynamic_bookings_count || 0}</strong></span>`;
                        }

                        card.innerHTML = `
                            <div class="event-image">
                                <img src="${imgPath}" alt="${event.title || ''}" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg'">
                                <span class="status-badge ${statusClass}">${statusText}</span>
                                <span class="category-tag">${event.category || ''}</span>
                            </div>
                            <div class="event-details">
                                <h3 class="event-title">${event.title || ''}</h3>
                                <p class="event-desc">${event.description || ''}</p>
                                <div class="event-stats">
                                    ${statsHtml}
                                </div>
                                <div class="event-actions">
                                    <a href="/EventManagementSystem/public/organizer/events/edit?id=${event.id}" class="btn-action edit" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn-action delete" title="Delete" onclick="deleteEvent(${event.id}, this)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </div>
                            </div>
                        `;
                        eventsGrid.appendChild(card);
                    });
                    renderPagination();
                }

                if(visibleCountLabel) visibleCountLabel.textContent = filteredEvents.length;
                if(totalEventsSpan) totalEventsSpan.textContent = allEvents.length;
                countLabel.textContent = `Showing ${filteredEvents.length} event campaigns`;
            }

            function renderPagination() {
                const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
                if (totalPages <= 1) {
                    paginationControls.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;

                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i > 1 && i < totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 && currentPage > 3) html += `<span class="dots">...</span>`;
                            if (i === totalPages - 1 && currentPage < totalPages - 2) html += `<span class="dots">...</span>`;
                            continue;
                        }
                    }
                    html += `<button class="p-btn num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                }

                html += `<button class="p-btn next ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;

                paginationControls.innerHTML = html;
            }

            window.changePage = (p) => {
                const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
                if (p < 1 || p > totalPages) return;
                currentPage = p;
                renderEvents();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            function fetchEvents() {
                if (window.emsApi) {
                    window.emsApi.apiFetch('/api/v1/events?limit=100')
                        .then(res => {
                            console.log('Events API response:', res);
                            allEvents = res.data?.items || res.data || [];
                            renderEvents();
                        })
                        .catch(err => {
                            console.error('Failed to fetch events:', err);
                            if (loadingMsg) loadingMsg.innerHTML = `<p style="color:red;">Error loading events: ${err.message}</p>`;
                        });
                }
            }

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentStatus = this.dataset.filterStatus;
                    currentPage = 1; // Reset to page 1
                    renderEvents();
                });
            });

            DropdownManager.onSelect('categoryFilter', (val) => {
                currentCategory = val;
                currentPage = 1; // Reset to page 1
                renderEvents();
            });

            // Initial fetch
            fetchEvents();
        });
    </script>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        function deleteEvent(id, btn) {
            if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) return;

            if (window.emsApi) {
                window.emsApi.apiFetch(`/api/v1/events/${id}`, { method: 'DELETE' })
                    .then(() => {
                        const card = btn.closest('.event-card');
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            card.style.transition = 'all 0.3s ease';
                            setTimeout(() => {
                                card.remove();
                                // Update count label
                                const eventCards = document.querySelectorAll('.event-card:not(.create-placeholder)');
                                const countLabel = document.getElementById('eventsCount');
                                if (countLabel) countLabel.textContent = `Showing ${eventCards.length} event campaigns`;
                            }, 300);
                        }
                    })
                    .catch(err => alert('Failed to delete event: ' + err.message));
            } else {
                // Fallback to legacy MVC if API client not loaded (should not happen)
                window.location.href = `/EventManagementSystem/public/organizer/events/delete?id=${id}`;
            }
        }
    </script>
</body>

</html>
