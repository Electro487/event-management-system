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
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/manage-events.css">
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
                    <span class="current">My Events</span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="icon-btn">🔔</button>
                    <div class="user-avatar-small">
                        <img src="/EventManagementSystem/public/assets/images/avatar-placeholder.png" alt="Profile">
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

        <section class="filters-bar">
            <div class="tabs">
                <button class="tab-btn active">All</button>
                <button class="tab-btn">Active</button>
                <button class="tab-btn">Draft</button>
            </div>
            <div class="category-filter">
                <select>
                    <option>All Categories</option>
                    <option>Weddings</option>
                    <option>Meetings</option>
                    <option>Cultural Events</option>
                    <option>Family Functions</option>
                    <option>Other Events and Programs</option>
                </select>
            </div>
            <div class="status-info">
                <span>Showing <?php echo count($events); ?> active event campaigns</span>
            </div>
        </section>

        <div class="events-grid">
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
                <div class="event-card">
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
                            <a href="#" class="btn-outline">Edit</a>
                            <a href="#" class="btn-outline">View</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Dummy data for design preview if no events exist -->
                <div class="event-card">
                    <div class="event-image">
                        <div class="dummy-img" style="background: #e0e0e0;"></div>
                        <span class="status-badge active">Active</span>
                        <span class="category-tag">Meetings</span>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Emerald Tech Summit 2024</h3>
                        <p class="event-desc">A premium multi-day summit focusing on architectural innovatio...</p>
                        <div class="event-stats">
                            <span class="stat">Bookings: <strong>142</strong></span>
                        </div>
                        <div class="event-actions">
                            <button class="btn-outline">✏️ Edit</button>
                            <button class="btn-outline">👁️ View</button>
                        </div>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <div class="dummy-img" style="background: #333;"></div>
                        <span class="status-badge inactive">Inactive</span>
                        <span class="category-tag">Weddings</span>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Winter Solstice Nuptials</h3>
                        <p class="event-desc">An intimate luxury wedding showcase designed for high-net-...</p>
                        <div class="event-stats">
                            <span class="stat">Bookings: <strong>0</strong></span>
                        </div>
                        <div class="event-actions">
                            <button class="btn-outline">✏️ Edit</button>
                            <button class="btn-outline">👁️ View</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
