<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e.PLAN | Architectural Precision for Every Milestone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Caveat+Brush&family=Permanent+Marker&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/home.css">
</head>

<body>

    <header class="header">
        <div class="header-left">
            <div class="logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN" style="height: 45px; width: auto; object-fit: contain;"></div>
            <nav class="nav-links">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client'): ?>
                    <a href="/EventManagementSystem/public/client/home">Home</a>
                <?php else: ?>
                    <a href="/EventManagementSystem/public/login">Home</a>
                <?php endif; ?>
                <a href="/EventManagementSystem/public/client/events">Browse Events</a>
                <a href="#services">Services</a>
                <a href="#about">About</a>
            </nav>
        </div>
        <div class="nav-right">
            <?php
                if (isset($_SESSION['user_id'])) {
                    $loginUrl = '/EventManagementSystem/public/home';
                    $registerUrl = '/EventManagementSystem/public/home';
                } else {
                    $loginUrl = '/EventManagementSystem/public/login';
                    $registerUrl = '/EventManagementSystem/public/register';
                }
            ?>
            <a href="<?php echo $loginUrl; ?>" class="btn-secondary">Login</a>
            <a href="<?php echo $registerUrl; ?>" class="btn-primary">Get Started</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Architectural precision <span>for every milestone.</span></h1>
            <p>We bring your dream event into life.</p>
            <?php 
                $bookNowUrl = '/EventManagementSystem/public/login';
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client') {
                    $bookNowUrl = '/EventManagementSystem/public/client/events#my-bookings';
                }
            ?>
            <a href="<?php echo $bookNowUrl; ?>" class="btn-primary" style="padding: 18px 45px; font-size: 18px; text-decoration: none; display: inline-block;">Book Now</a>
        </div>
        <div class="hero-image">
            <div class="image-tilted" style="background-image: url('/EventManagementSystem/public/assets/images/marriage.jpeg');"></div>
        </div>
    </section>

    <section class="section-models" id="services">
        <div class="section-header">
            <div class="section-title">
                <h2>Event Models</h2>
                <p>Every celebration has its own structural logic. Explore our specialized categories for a tailored experience.</p>
            </div>
            <?php 
                $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client';
                $servicesUrl = $isLoggedIn ? '/EventManagementSystem/public/client/events' : '/EventManagementSystem/public/login';
            ?>
            <a href="<?php echo $servicesUrl; ?>" class="btn-view" style="color: #00796b; border-color: #00796b; height: 50px; padding: 15px 30px; display: inline-flex; align-items: center; text-decoration: none;">See All Services</a>
        </div>

        <div class="grid-top">
            <div class="model-card card-wedding" style="background-image: url('/EventManagementSystem/public/assets/images/marriage.jpeg');">
                <div class="model-info">
                    <h3>Weddings</h3>
                    <p style="margin-bottom: 20px; opacity: 0.8;">Timeless architectural celebrations of union.</p>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Weddings' : '/EventManagementSystem/public/login'; ?>" class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card card-meeting" style="background-image: url('/EventManagementSystem/public/assets/images/meetings.jpeg');">
                <div class="model-info">
                    <h3>Meetings</h3>
                    <p style="margin-bottom: 20px; opacity: 0.8;">Professional environments for strategic flow.</p>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Meetings' : '/EventManagementSystem/public/login'; ?>" class="btn-view btn-dark">View Types</a>
                </div>
            </div>
        </div>

        <div class="models-grid">
            <div class="model-card" style="background-image: url('/EventManagementSystem/public/assets/images/baby.png');">
                <div class="model-info">
                    <h3>Cultural Events</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Cultural Events' : '/EventManagementSystem/public/login'; ?>" class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card" style="background-image: url('/EventManagementSystem/public/assets/images/family_functions.png');">
                <div class="model-info">
                    <h3>Family Functions</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Family Functions' : '/EventManagementSystem/public/login'; ?>" class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card" style="background-image: url('/EventManagementSystem/public/assets/images/other_events.png');">
                <div class="model-info">
                    <h3>Other Events and Programs</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Other Events and Programs' : '/EventManagementSystem/public/login'; ?>" class="btn-view btn-dark">View Types</a>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <h2>About Us</h2>
        <p class="about-lead">At e.plan, we are dedicated to transforming your vision into reality. We provide comprehensive event curation services that blend architectural precision with creative elegance.</p>

        <div class="about-cards-container">
            <a href="#" class="about-card card-dark" style="text-decoration: none;">
                <h3>What We Do</h3>
                <p>We design and execute milestone events with a focus on structural integrity and curated aesthetic appeal.</p>
            </a>
            <a href="#services" class="about-card card-gold" style="text-decoration: none;">
                <h3>Our Services</h3>
                <p>From weddings and corporate meetings to cultural ceremonies, we offer full-service planning, catering, and decor.</p>
            </a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="logo-footer"><img src="/EventManagementSystem/public/assets/images/logo-white.png" alt="e.PLAN" style="height: 65px; width: auto; object-fit: contain; margin-bottom: 5px; image-rendering: high-quality;"></div>
                <p>Transforming spaces into curated architectural experiences for the elite.</p>
            </div>
            <nav class="footer-nav">
                <a href="javascript:void(0);">Privacy Policy</a>
                <a href="javascript:void(0);">Terms of Service</a>
                <a href="javascript:void(0);">Contact Us</a>
            </nav>
            <div class="footer-copy">
                &copy; 2026 e.plan Architectural Event Curation. All rights reserved.
            </div>
        </div>
    </footer>

</body>

</html>
