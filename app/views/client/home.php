<?php
/** @var array $recentBookings @var array $featuredEvents @var array|null $nextEvent @var int $daysLeft
 *  @var int $totalBookings @var int $upcomingCount @var int $completedCount @var int $pendingCount */

$initials = '';
$nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
foreach ($nameParts as $p) {
    if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
}
if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
$firstName   = $nameParts[0] ?? '';
$lastName    = count($nameParts) > 1 ? end($nameParts) : '';
$displayName = $_SESSION['user_fullname'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home – e-Plan</title>
    <meta name="description" content="Your e-Plan client dashboard. View upcoming events, recent bookings, and featured events.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/client-home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- ═══════════════════════════════ NAVBAR ═══════════════════════════════ -->
<header class="header">
    <a href="/EventManagementSystem/public/client/home" class="logo">
        <img src="/EventManagementSystem/public/assets/images/logo.png" alt="e-Plan"
             style="height:26px;width:auto;object-fit:contain;transform:scale(1.7);transform-origin:left center;">
    </a>

    <nav class="nav-links">
        <a href="/EventManagementSystem/public/client/home" class="active">Home</a>
        <a href="/EventManagementSystem/public/client/events">Browse Events</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
        <?php endif; ?>
    </nav>

    <div class="nav-icons">
        <!-- Notification Bell -->
        <div class="notifications-wrapper">
            <div class="notification-bell-btn" id="notification-bell">
                <i class="fa-regular fa-bell"></i>
                <span class="unread-badge" id="unread-badge" style="display:none;">0</span>
            </div>
            <div class="notifications-dropdown" id="notifications-dropdown">
                <div class="nd-header">
                    <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 UNREAD</span></h3>
                    <a href="#" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                </div>
                <div class="nd-content" id="nd-list">
                    <div class="nd-empty">
                        <i class="fa-regular fa-bell-slash"></i> Loading notifications...
                    </div>
                </div>
                <div class="nd-footer">
                    <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">
                        View All Notifications <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Profile Icon -->
        <div style="position:relative;" id="profile-container">
            <div onclick="toggleProfileDropdown()" id="profile-icon" class="header-profile-icon">
                <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>"
                         style="width:100%;height:100%;object-fit:cover;" id="header-avatar">
                <?php else: ?>
                    <span id="header-initials"><?php echo htmlspecialchars($initials); ?></span>
                <?php endif; ?>
            </div>

            <!-- Profile Dropdown -->
            <div id="profile-dropdown" class="profile-dropdown">
                <div class="pd-top">
                    <div class="pd-avatar-container">
                        <div class="pd-avatar">
                            <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>"
                                     style="width:100%;height:100%;object-fit:cover;" id="dropdown-avatar">
                            <?php else: ?>
                                <span id="dropdown-initials"><?php echo htmlspecialchars($initials); ?></span>
                            <?php endif; ?>
                        </div>
                        <label for="profile_picture_upload" class="pd-edit-icon" title="Change Photo">
                            <i class="fa-solid fa-pen"></i>
                        </label>
                        <?php if (!empty($_SESSION['user_profile_pic'])): ?>
                            <div class="pd-delete-icon" onclick="deleteProfilePicture()" title="Remove Photo">
                                <i class="fa-solid fa-trash"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="profile_picture_upload" accept="image/*"
                               style="display:none;" onchange="uploadProfilePicture(this)">
                    </div>
                    <h3 class="pd-name"><?php echo htmlspecialchars($displayName); ?></h3>
                    <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                    <span class="pd-role"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Client')); ?></span>
                </div>
                <div class="pd-bottom">
                    <div class="pd-detail">
                        <label>FIRST NAME</label>
                        <div><?php echo htmlspecialchars($firstName); ?></div>
                    </div>
                    <div class="pd-detail">
                        <label>LAST NAME</label>
                        <div><?php echo htmlspecialchars($lastName); ?></div>
                    </div>
                    <div class="pd-detail">
                        <label>EMAIL ADDRESS</label>
                        <div><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
                    </div>
                    <a href="/EventManagementSystem/public/logout" class="pd-logout-btn">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
            <a href="/EventManagementSystem/public/login"
               style="color:#1f6f59;font-weight:600;text-decoration:none;">Login</a>
        <?php endif; ?>
    </div>
</header>

<!-- ═══════════════════════════════ HERO ═══════════════════════════════════ -->
<div class="hero-banner">
    <!-- Hero content now overlays the background image set in CSS -->
    <div class="hero-content">
        <h1>Welcome back,<br><?php echo htmlspecialchars($firstName ?: $displayName); ?>! 👋</h1>
        <p>What milestone are you planning next?</p>
        <a href="/EventManagementSystem/public/client/events" class="btn-browse-hero">
            Browse Events <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- ═══════════════════════════════ MAIN ═══════════════════════════════════ -->
<div class="main-container">

    <!-- ── Stats Row ───────────────────────────────────────────────────── -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value-row">
                <span class="stat-number"><?php echo $totalBookings; ?></span>
                <?php if ($upcomingCount > 0): ?>
                    <span class="stat-badge badge-upcoming"><?php echo $upcomingCount; ?> UPCOMING</span>
                <?php elseif ($totalBookings === 0): ?>
                    <span class="stat-badge badge-review">NO BOOKINGS</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Completed Events</div>
            <div class="stat-value-row">
                <span class="stat-number"><?php echo $completedCount; ?></span>
                <?php if ($completedCount > 0): ?>
                    <span class="stat-badge badge-success">ALL SUCCESSFUL</span>
                <?php else: ?>
                    <span class="stat-badge badge-review">NONE YET</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value-row">
                <span class="stat-number"><?php echo $pendingCount; ?></span>
                <?php if ($pendingCount > 0): ?>
                    <span class="stat-badge badge-warning">AWAITING REVIEW</span>
                <?php else: ?>
                    <span class="stat-badge badge-success">ALL CLEAR</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Two-Column: Bookings Table + Next Event ──────────────────────── -->
    <div class="content-grid">

        <!-- Recent Bookings -->
        <div class="section-card">
            <div class="card-header">Recent Bookings</div>
            <?php if (!empty($recentBookings)): ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Event Type</th>
                        <th>Package</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentBookings as $bk):
                    $ds = $bk['display_status'];
                    $badgeClass = match($ds) {
                        'confirmed'  => 'badge-confirmed',
                        'pending'    => 'badge-pending',
                        'completed'  => 'badge-completed',
                        'cancelled'  => 'badge-cancelled',
                        default      => 'badge-review',
                    };
                ?>
                <tr>
                    <td class="col-event"><?php echo htmlspecialchars($bk['event_title'] ?? '–'); ?></td>
                    <td class="col-package"><?php echo htmlspecialchars(ucfirst($bk['package_tier'] ?? '–')); ?></td>
                    <td>
                        <span class="stat-badge <?php echo $badgeClass; ?>">
                            <?php echo strtoupper($ds); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="table-empty">
                <i class="fa-regular fa-calendar-xmark"></i>
                No bookings yet. <a href="/EventManagementSystem/public/client/events" style="color:#1f6f59;font-weight:600;">Browse events</a> to get started!
            </div>
            <?php endif; ?>

            <a href="/EventManagementSystem/public/client/events#my-bookings" class="view-all-link">
                View All My Bookings <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i>
            </a>
        </div>

        <!-- Next Event -->
        <div class="next-event-card">
            <div class="card-header">Next Event</div>
            <?php if ($nextEvent): ?>
                <?php
                    $evImg = !empty($nextEvent['event_image'])
                        ? htmlspecialchars($nextEvent['event_image'])
                        : '/EventManagementSystem/public/assets/images/placeholder.png';
                    $evCat   = htmlspecialchars($nextEvent['event_category'] ?? 'Event');
                    $evTitle = htmlspecialchars($nextEvent['event_title'] ?? 'Upcoming Event');
                    $evDate  = $nextEvent['event_date'] ?: ($nextEvent['event_start_date'] ?? '');
                    $evDateFormatted = $evDate ? date('M j, Y', strtotime($evDate)) : '';
                    $evLocation = htmlspecialchars($nextEvent['venue_location'] ?? '');
                    $bookingId  = $nextEvent['id'] ?? null;
                ?>
                <div class="next-event-img-wrap">
                    <img src="<?php echo $evImg; ?>" alt="<?php echo $evTitle; ?>">
                    <span class="next-event-cat-tag"><?php echo $evCat; ?></span>
                </div>
                <div class="next-event-body">
                    <h3><?php echo $evTitle; ?></h3>
                    <div class="next-event-meta">
                        <?php if ($evDateFormatted): ?>
                            <i class="fa-regular fa-calendar" style="color:#1f6f59;"></i>
                            <?php echo $evDateFormatted; ?>
                        <?php endif; ?>
                        <?php if ($evLocation): ?>
                            &nbsp;&bull;&nbsp;
                            <i class="fa-solid fa-location-dot" style="color:#1f6f59;"></i>
                            <?php echo $evLocation; ?>
                        <?php endif; ?>
                    </div>
                    <div class="countdown-label">Countdown to the big day</div>
                    <div class="countdown-days">
                        <?php echo $daysLeft; ?><span class="days-word"><?php echo ($daysLeft == 1 ? 'DAY' : 'DAYS'); ?></span>
                    </div>
                </div>
                <?php if ($bookingId): ?>
                <a href="/EventManagementSystem/public/client/booking/details?id=<?php echo $bookingId; ?>"
                   class="btn-view-details">View Details</a>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-next-event">
                    <i class="fa-regular fa-calendar-check"></i>
                    No upcoming confirmed events yet.<br>
                    <a href="/EventManagementSystem/public/client/events"
                       style="color:#1f6f59;font-weight:600;text-decoration:none;font-size:13px;">
                        Browse Events &rarr;
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- .content-grid -->

    <!-- ── Featured Events ──────────────────────────────────────────────── -->
    <div class="section-header">
        <h2>Featured Events</h2>
        <a href="/EventManagementSystem/public/client/events">View All</a>
    </div>

    <div class="featured-grid">
        <?php if (!empty($featuredEvents)): ?>
            <?php foreach ($featuredEvents as $fe):
                $feImg = !empty($fe['image_path'])
                    ? htmlspecialchars($fe['image_path'])
                    : '/EventManagementSystem/public/assets/images/placeholder.png';
                $feTitle    = htmlspecialchars($fe['title'] ?? 'Event');
                $feCat      = htmlspecialchars($fe['category'] ?? '');
                $feLocation = htmlspecialchars($fe['venue_location'] ?? '');
                $feId       = $fe['id'];
            ?>
            <a href="/EventManagementSystem/public/client/events/view?id=<?php echo $feId; ?>"
               class="featured-card">
                <div class="featured-card-img">
                    <img src="<?php echo $feImg; ?>" alt="<?php echo $feTitle; ?>">
                    <?php if ($feCat): ?>
                        <span class="feat-cat-tag"><?php echo $feCat; ?></span>
                    <?php endif; ?>
                </div>
                <div class="featured-card-body">
                    <h3><?php echo $feTitle; ?></h3>
                </div>
                <div class="featured-card-footer">
                    <span class="feat-location">
                        <i class="fa-solid fa-location-dot" style="color:#1f6f59;font-size:11px;"></i>
                        <?php echo $feLocation ?: 'TBA'; ?>
                    </span>
                    <span class="feat-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-featured-events">
                <i class="fa-regular fa-calendar" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.4;"></i>
                No events available at the moment.
                <a href="/EventManagementSystem/public/client/events" style="color:#1f6f59;font-weight:600;">Check again soon</a>
            </div>
        <?php endif; ?>
    </div>

</div><!-- .main-container -->

<!-- ═══════════════════════════════ FOOTER ══════════════════════════════════ -->
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <img src="/EventManagementSystem/public/assets/images/logo-white.png"
                 alt="e-Plan" style="height:22px;width:auto;object-fit:contain;">
            <p>Curating experiences through architectural and aesthetic precision.<br>Your vision, our blueprint.</p>
        </div>
        <div class="footer-links-group">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Us</a>
        </div>
    </div>
    <div class="footer-copy">
        &copy; 2026 e.Plan. All rights reserved.
    </div>
</footer>

<script>
function toggleProfileDropdown() {
    document.getElementById('profile-dropdown').classList.toggle('show');
}
document.addEventListener('click', function(e) {
    const c = document.getElementById('profile-container');
    if (c && !c.contains(e.target)) {
        document.getElementById('profile-dropdown').classList.remove('show');
    }
});

function uploadProfilePicture(input) {
    if (!input.files || !input.files[0]) return;
    const fd = new FormData();
    fd.append('profile_picture', input.files[0]);
    fetch('/EventManagementSystem/public/client/profile/update', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profile-icon').innerHTML =
                    '<img src="' + data.path + '" style="width:100%;height:100%;object-fit:cover;" id="header-avatar">';
                document.querySelector('.pd-avatar').innerHTML =
                    '<img src="' + data.path + '" style="width:100%;height:100%;object-fit:cover;">';
                if (!document.querySelector('.pd-delete-icon')) {
                    const ac = document.querySelector('.pd-avatar-container');
                    const db = document.createElement('div');
                    db.className = 'pd-delete-icon'; db.title = 'Remove Photo';
                    db.onclick = deleteProfilePicture;
                    db.innerHTML = '<i class="fa-solid fa-trash"></i>';
                    ac.appendChild(db);
                }
            } else { alert(data.message || 'Upload failed.'); }
        }).catch(() => alert('An error occurred during upload.'));
}

function deleteProfilePicture() {
    if (!confirm('Remove your profile picture?')) return;
    fetch('/EventManagementSystem/public/client/profile/delete-picture', { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const initials = '<?php echo addslashes(htmlspecialchars($initials)); ?>';
                document.getElementById('profile-icon').innerHTML = '<span id="header-initials">' + initials + '</span>';
                document.querySelector('.pd-avatar').innerHTML = '<span>' + initials + '</span>';
                const di = document.querySelector('.pd-delete-icon');
                if (di) di.remove();
            } else { alert('Error removing image.'); }
        }).catch(() => alert('An error occurred.'));
}
</script>
<script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
