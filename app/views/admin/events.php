<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All System Events - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-events.css?v=<?php echo time(); ?>">
    <style>
        .organizer-tag { background: #f0fdf4; color: #246A55; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; margin-top: 5px; display: inline-block; }
    </style>
</head>
<body>

    <?php 
        $activePage = 'events';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="header">
            <form action="/EventManagementSystem/public/admin/events" method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search system-wide..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" style="display:none;"></button>
            </form>
            <div class="header-icons">
                <i class="far fa-bell"></i>
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <section class="events-summary">
            <div class="summary-text">
                <h1>All System Events</h1>
                <p>Oversight of all curated event campaigns across the entire platform.</p>
            </div>
            <div class="summary-actions">
                <a href="/EventManagementSystem/public/admin/events/create" class="btn-create" style="background: #246A55; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
        </section>

        <section class="filters-bar">
            <div class="tabs">
                <button class="tab-btn active" data-filter-status="all">All</button>
                <button class="tab-btn" data-filter-status="active">Active</button>
                <button class="tab-btn" data-filter-status="draft">Draft</button>
            </div>
            <div class="category-filter">
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <?php 
                    $cats = ["Weddings", "Meetings", "Cultural Events", "Family Functions", "Other Events and Programs"];
                    foreach($cats as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="status-info">
                <span id="eventsCount">Showing <?php echo count($events); ?> system campaigns</span>
            </div>
        </section>

        <div class="events-grid" id="eventsGrid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                <div class="event-card" data-status="<?php echo strtolower($event['status']); ?>" data-category="<?php echo $event['category']; ?>">
                    <div class="event-image">
                        <img src="<?php echo $event['image_path'] ?: '/EventManagementSystem/public/assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <span class="status-badge <?php echo strtolower($event['status']); ?>"><?php echo ucfirst($event['status']); ?></span>
                        <span class="category-tag"><?php echo htmlspecialchars($event['category']); ?></span>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <span class="organizer-tag"><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($event['organizer_name']); ?></span>
                        <p class="event-desc" style="margin-top: 10px;"><?php echo htmlspecialchars(substr($event['description'], 0, 80)) . '...'; ?></p>
                        
                        <div class="event-actions" style="margin-top: 15px;">
                            <a href="/EventManagementSystem/public/admin/events/edit?id=<?php echo $event['id']; ?>" class="btn-action edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <a href="/EventManagementSystem/public/admin/events/delete?id=<?php echo $event['id']; ?>" class="btn-action delete" onclick="return confirm('ADMIN: Delete this user\'s event?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events-message" style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #999;">
                    <p>No system events found.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const categorySelect = document.getElementById('categoryFilter');
    const eventCards = document.querySelectorAll('.event-card');
    const countLabel = document.getElementById('eventsCount');

    let currentStatus = 'all';
    let currentCategory = 'all';

    function filterEvents() {
        let visibleCount = 0;
        eventCards.forEach(card => {
            const status = card.dataset.status;
            const category = card.dataset.category;
            const statusMatch = (currentStatus === 'all' || status === currentStatus);
            const categoryMatch = (currentCategory === 'all' || category === currentCategory);

            if (statusMatch && categoryMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        countLabel.textContent = `Showing ${visibleCount} system campaigns`;
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.dataset.filterStatus;
            filterEvents();
        });
    });

    categorySelect.addEventListener('change', function() {
        currentCategory = this.value;
        filterEvents();
    });
});
</script>
</body>
</html>
