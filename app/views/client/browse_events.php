<?php
$categories = ['All', 'Weddings', 'Meetings', 'Cultural Events', 'Family Functions', 'Other Events and Programs'];
$currentCategory = $_GET['category'] ?? 'All';
$searchQuery = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/browse-events.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/" class="logo">e-Plan</a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/">Home</a>
            <a href="/EventManagementSystem/public/client/events" class="active">Browse Events</a>
            <a href="#">My Bookings</a>
            <a href="#">About</a>
        </nav>
        <div class="nav-icons">
            <i class="fa-regular fa-bell" style="font-size: 20px; color: #1f6f59; cursor: pointer;"></i>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="width: 32px; height: 32px; background: #1f6f59; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; cursor: pointer;">
                    <?php echo strtoupper(substr($_SESSION['user_fullname'], 0, 1)); ?>
                </div>
            <?php else: ?>
                <a href="/EventManagementSystem/public/login" style="color: #1f6f59; font-weight: 600; text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Browse Events</h1>
        <p>Discover premium event planning services tailored for your most memorable milestones across Nepal.</p>
        
        <form action="/EventManagementSystem/public/client/events" method="GET" class="search-bar">
            <?php if ($currentCategory !== 'All'): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($currentCategory); ?>">
            <?php endif; ?>
            <div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <input type="text" name="search" class="search-input" placeholder="Search for weddings, conferences, or festivals..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="search-btn">Search</button>
        </form>
    </section>

    <div class="container">
        <!-- Category Filters -->
        <div class="category-filters">
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?php echo urlencode($cat); ?><?php echo $searchQuery ? '&search='.urlencode($searchQuery) : ''; ?>" 
                   class="filter-btn <?php echo $currentCategory === $cat ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Event Grid -->
        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-image-container">
                            <?php 
                                $image = !empty($event['image_path']) ? $event['image_path'] : '/EventManagementSystem/public/assets/images/placeholder.jpg';
                            ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                            <span class="event-category-tag"><?php echo htmlspecialchars($event['category'] ?: 'Event'); ?></span>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                            
                            <div class="event-location">
                                <i class="fa-solid fa-location-dot"></i> 
                                <?php echo htmlspecialchars($event['venue_location'] ?: 'Location TBD'); ?>
                            </div>
                            
                            <?php 
                                // Parse packages JSON to find the starting price
                                $packages = json_decode($event['packages'], true);
                                $startingPrice = 0;
                                if (is_array($packages) && count($packages) > 0) {
                                    $prices = array_column($packages, 'price');
                                    // Remove empty and non-numeric
                                    $prices = array_filter($prices, 'is_numeric');
                                    if (!empty($prices)) {
                                        $startingPrice = min($prices);
                                    }
                                }
                                
                                // Default formatting if no explicit price
                                $displayPrice = $startingPrice > 0 ? number_format($startingPrice) : "10,000"; 
                            ?>
                            <div class="event-price">Packages from Rs. <?php echo $displayPrice; ?></div>
                            
                            <a href="/EventManagementSystem/public/client/events/view?id=<?php echo $event['id']; ?>" class="btn-view-packages">
                                View Packages &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <h3>No events found matching your criteria.</h3>
                    <p>Try selecting a different category or adjusting your search.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($events)): ?>
        <div class="pagination">
            <a href="#" class="page-item page-link-text">Previous</a>
            <a href="#" class="page-item active">1</a>
            <a href="#" class="page-item">2</a>
            <a href="#" class="page-item">3</a>
            <a href="#" class="page-item page-link-text">Next</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-left">
            <div class="footer-logo">e-Plan</div>
            <p>&copy; 2024 e-Plan Event Management. All rights reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Support</a>
        </div>
    </footer>

</body>
</html>
