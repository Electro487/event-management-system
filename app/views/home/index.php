<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e.PLAN | Architectural Precision for Every Milestone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Caveat+Brush&family=Permanent+Marker&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/home.css?v=<?php echo time(); ?>">
    <style>
        .recent-reviews-section {
            padding: 40px 100px 100px;
            background-color: #ffffff;
        }

        .reviews-grid {
            display: grid;
            /* Dynamic grid based on count */
            grid-template-columns: repeat(var(--review-count, 1), 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .review-card {
            background: #f8fcfb;
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(12, 43, 34, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 200px;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-color: #ffc247;
        }

        .review-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .review-card-stars {
            color: #ffc247;
            font-size: 14px;
            display: flex;
            gap: 2px;
        }

        .review-card-date {
            font-size: 12px;
            color: #888;
        }

        .review-card-comment {
            font-size: 15px;
            color: #333;
            line-height: 1.6;
            font-style: italic;
            margin-bottom: 20px;
            flex: 1;
        }

        .review-card-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .review-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0c2b22;
            color: #ffc247;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            object-fit: cover;
        }

        .review-user-name {
            font-weight: 700;
            font-size: 14px;
            color: #0c2b22;
        }

        .section-subtitle {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-subtitle h3 {
            font-size: 28px;
            color: #0c2b22;
            margin-bottom: 10px;
        }

        .section-subtitle p {
            color: #666;
            font-size: 16px;
        }
        
        @media (max-width: 900px) {
            .reviews-grid {
                grid-template-columns: 1fr !important;
            }
            .recent-reviews-section {
                padding: 40px 20px 60px;
            }
        }
    </style>
</head>

<body>
    <?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>

    <header class="header">
        <div class="header-left">
            <div class="logo"><img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                    style="height: 45px; width: auto; object-fit: contain;"></div>
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
            <a href="<?php echo $bookNowUrl; ?>" class="btn-primary"
                style="padding: 18px 45px; font-size: 18px; text-decoration: none; display: inline-block;">Book Now</a>
        </div>
        <div class="hero-image">
            <div class="image-tilted"
                style="background-image: url('/EventManagementSystem/public/assets/images/marriage.jpeg');"></div>
        </div>
    </section>

    <section class="section-models" id="services">
        <div class="section-header">
            <div class="section-title">
                <h2>Event Models</h2>
                <p>Every celebration has its own structural logic. Explore our specialized categories for a tailored
                    experience.</p>
            </div>
            <?php
            $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client';
            $servicesUrl = $isLoggedIn ? '/EventManagementSystem/public/client/events' : '/EventManagementSystem/public/login';
            ?>
            <a href="<?php echo $servicesUrl; ?>" class="btn-view"
                style="color: #00796b; border-color: #00796b; height: 50px; padding: 15px 30px; display: inline-flex; align-items: center; text-decoration: none;">See
                All Services</a>
        </div>

        <div class="grid-top">
            <div class="model-card card-wedding"
                style="background-image: url('/EventManagementSystem/public/assets/images/marriage.jpeg');">
                <div class="model-info">
                    <h3>Weddings</h3>
                    <p style="margin-bottom: 20px; opacity: 0.8;">Timeless architectural celebrations of union.</p>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Weddings' : '/EventManagementSystem/public/login'; ?>"
                        class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card card-meeting"
                style="background-image: url('/EventManagementSystem/public/assets/images/meetings.jpeg');">
                <div class="model-info">
                    <h3>Meetings</h3>
                    <p style="margin-bottom: 20px; opacity: 0.8;">Professional environments for strategic flow.</p>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Meetings' : '/EventManagementSystem/public/login'; ?>"
                        class="btn-view btn-dark">View Types</a>
                </div>
            </div>
        </div>

        <div class="models-grid">
            <div class="model-card"
                style="background-image: url('/EventManagementSystem/public/assets/images/baby.png');">
                <div class="model-info">
                    <h3>Cultural Events</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Cultural Events' : '/EventManagementSystem/public/login'; ?>"
                        class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card"
                style="background-image: url('/EventManagementSystem/public/assets/images/family_functions.png');">
                <div class="model-info">
                    <h3>Family Functions</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Family Functions' : '/EventManagementSystem/public/login'; ?>"
                        class="btn-view btn-dark">View Types</a>
                </div>
            </div>
            <div class="model-card"
                style="background-image: url('/EventManagementSystem/public/assets/images/other_events.png');">
                <div class="model-info">
                    <h3>Other Events and Programs</h3>
                    <a href="<?php echo $isLoggedIn ? '/EventManagementSystem/public/client/events?category=Other Events and Programs' : '/EventManagementSystem/public/login'; ?>"
                        class="btn-view btn-dark">View Types</a>
                </div>
            </div>
        </div>
    </section>

    <section class="review-section" id="reviews">
        <div class="review-container">
            <div class="review-text">
                <h2>What our clients say.</h2>
                <p style="margin-bottom: 25px;">Service satisfaction we provide for clients to organize events is
                    reflected in our ratings.</p>
                <?php
                $isLoggedInClient = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client';
                $rateUsUrl = $isLoggedInClient ? '/EventManagementSystem/public/client/feedback' : '/EventManagementSystem/public/login';
                ?>
                <a href="<?php echo $rateUsUrl; ?>" class="btn-primary"
                    style="padding: 12px 30px; font-size: 15px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-pen-nib"></i> Rate Us
                </a>
            </div>

            <div class="review-stats">
                <div class="review-score-large" id="avg-rating">0.0</div>
                <div class="review-details">
                    <div class="review-stars-new" id="star-rating-container">
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <div class="review-meta"><span id="review-meta-text">Loading reviews...</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Reviews List -->
    <section class="recent-reviews-section">
        <div class="section-subtitle">
            <h3>Recent Testimonials</h3>
            <p>Real experiences from our elite clientele.</p>
        </div>
        <div id="reviews-grid" class="reviews-grid">
            <!-- Loaded via API -->
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
                <i class="fa-solid fa-spinner fa-spin"></i> Curating testimonials...
            </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <h2>About Us</h2>
        <p class="about-lead">At e.plan, we are dedicated to transforming your vision into reality. We provide
            comprehensive event curation services that blend architectural precision with creative elegance.</p>

        <div class="about-cards-container">
            <a href="#" class="about-card card-dark" style="text-decoration: none;">
                <h3>What We Do</h3>
                <p>We design and execute milestone events with a focus on structural integrity and curated aesthetic
                    appeal.</p>
            </a>
            <a href="#services" class="about-card card-gold" style="text-decoration: none;">
                <h3>Our Services</h3>
                <p>From weddings and corporate meetings to cultural ceremonies, we offer full-service planning,
                    catering, and decor.</p>
            </a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="logo-footer"><img src="/EventManagementSystem/public/assets/images/logo-white.png"
                        alt="e.PLAN"
                        style="height: 65px; width: auto; object-fit: contain; margin-bottom: 5px; image-rendering: high-quality;">
                </div>
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

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.emsApi) return;
            
            // 1. Fetch Stats
            window.emsApi.apiFetch('/api/v1/feedback/stats')
            .then(res => {
                if (res.success) {
                    const stats = res.data;
                    const avg = parseFloat(stats.avg);
                    const total = parseInt(stats.total);
                    
                    document.getElementById('avg-rating').textContent = avg.toFixed(1);
                    
                    const starContainer = document.getElementById('star-rating-container');
                    let starsHtml = '';
                    const fullStars = Math.floor(avg);
                    const halfStar = (avg - fullStars >= 0.5) ? 1 : 0;
                    const emptyStars = 5 - fullStars - halfStar;

                    for (let i = 0; i < fullStars; i++) starsHtml += '<i class="fa-solid fa-star"></i>';
                    if (halfStar) starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>';
                    for (let i = 0; i < emptyStars; i++) starsHtml += '<i class="fa-regular fa-star"></i>';
                    
                    starContainer.innerHTML = starsHtml;
                    
                    let label = 'Good';
                    if (avg >= 4.5) label = 'Excellent';
                    else if (avg >= 3.5) label = 'Very Good';
                    else if (avg >= 2.5) label = 'Good';
                    else if (avg >= 1.5) label = 'Fair';
                    else label = 'Poor';
                    
                    document.getElementById('review-meta-text').innerHTML = `Based on ${total} reviews &bull; ${label}`;
                }
            })
            .catch(err => console.error('Failed to load landing stats:', err));

            // 2. Fetch Recent Reviews
            window.emsApi.apiFetch('/api/v1/feedback')
            .then(res => {
                if (res.success) {
                    const allReviews = res.data || [];
                    const grid = document.getElementById('reviews-grid');
                    
                    if (allReviews.length === 0) {
                        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">No reviews shared yet.</div>';
                        return;
                    }

                    // --- SELECTION LOGIC ---
                    // 1. Sort by rating DESC, then date DESC
                    const sorted = [...allReviews].sort((a, b) => {
                        if (b.rating !== a.rating) return b.rating - a.rating;
                        return new Date(b.created_at) - new Date(a.created_at);
                    });

                    const selected = [];
                    const seenUsers = new Set();

                    // Pass 1: Prioritize different users (top review for each unique user)
                    for (const r of sorted) {
                        if (selected.length >= 3) break;
                        if (!seenUsers.has(r.client_id)) {
                            selected.push(r);
                            seenUsers.add(r.client_id);
                        }
                    }

                    // Pass 2: If we still need more to hit 3, pick remaining top reviews even if user is duplicate
                    if (selected.length < 3) {
                        for (const r of sorted) {
                            if (selected.length >= 3) break;
                            // Check if this specific review object is already selected
                            if (!selected.some(s => s.id === r.id)) {
                                selected.push(r);
                            }
                        }
                    }

                    // --- RENDER ---
                    grid.style.setProperty('--review-count', selected.length);
                    
                    grid.innerHTML = selected.map(r => {
                        const stars = [];
                        for(let i=1; i<=5; i++) stars.push(`<i class="${i <= r.rating ? 'fas' : 'far'} fa-star"></i>`);
                        
                        const nameParts = r.client_name.split(' ');
                        const initials = (nameParts[0][0] + (nameParts.length > 1 ? nameParts[nameParts.length-1][0] : '')).toUpperCase();
                        
                        const avatarHtml = r.client_profile_pic 
                            ? `<img src="${r.client_profile_pic}" class="review-user-avatar">`
                            : `<div class="review-user-avatar">${initials}</div>`;

                        return `
                            <div class="review-card">
                                <div class="review-card-header">
                                    <div class="review-card-stars">${stars.join('')}</div>
                                    <div class="review-card-date">${new Date(r.created_at).toLocaleDateString('en-US', {month: 'short', year: 'numeric'})}</div>
                                </div>
                                <p class="review-card-comment">"${r.comment}"</p>
                                <div class="review-card-user">
                                    ${avatarHtml}
                                    <span class="review-user-name">${r.client_name}</span>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            })
            .catch(err => {
                console.error('Failed to load reviews:', err);
                document.getElementById('reviews-grid').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">Failed to load testimonials.</div>';
            });
        });
    </script>

</body>

</html>