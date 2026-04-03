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
                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                    <div class="user-avatar-small">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_fullname'] ?? 'User'); ?>&background=0D8ABC&color=fff" alt="Profile" style="border-radius:50%; object-fit:cover;">
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
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="Weddings">Weddings</option>
                    <option value="Meetings">Meetings</option>
                    <option value="Cultural Events">Cultural Events</option>
                    <option value="Family Functions">Family Functions</option>
                    <option value="Other Events and Programs">Other Events and Programs</option>
                </select>
            </div>
            <div class="status-info">
                <span id="eventsCount">Showing <?php echo count($events); ?> event campaigns</span>
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
                        <p class="event-desc"><?php echo htmlspecialchars(substr($event['description'], 0, 80)) . '...'; ?></p>
                        <div class="event-stats">
                            <span class="stat">Bookings: <strong><?php echo $event['bookings_count']; ?></strong></span>
                        </div>
                        <div class="event-actions">
                            <a href="/EventManagementSystem/public/organizer/events/edit?id=<?php echo $event['id']; ?>" class="btn-action edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <a href="/EventManagementSystem/public/organizer/events/delete?id=<?php echo $event['id']; ?>" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to delete this event?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events-message" style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #999;">
                    <p>No events found. Start by creating your first curated experience!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const categorySelect = document.getElementById('categoryFilter');
    const eventCards = document.querySelectorAll('.event-card:not(.create-placeholder)');
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
        countLabel.textContent = `Showing ${visibleCount} event campaigns`;
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
