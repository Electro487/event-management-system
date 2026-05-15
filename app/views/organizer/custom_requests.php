<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Custom Requests - Organizer Dashboard'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
</head>
<body>
<?php
$activePage = 'requests';
include_once __DIR__ . '/partials/sidebar.php';
?>
    <div class="main-content">
        <div class="content-header">
            <h1 class="page-title">Custom Event Requests</h1>
            <p class="page-subtitle">Manage client negotiation requests</p>
        </div>

        <div class="bookings-grid">
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-code-pull-request"></i>
                    <h3>No requests found</h3>
                    <p>You don't have any custom package requests yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): 
                    $statusClass = 'status-' . strtolower($request['status']);
                    $imagePath = $request['event_image'];
                    if (!empty($imagePath)) {
                        $imagePath = ($imagePath[0] === '/') ? $imagePath : '/EventManagementSystem/public/assets/images/events/' . $imagePath;
                    } else {
                        $imagePath = '/EventManagementSystem/public/assets/images/default-event.jpg';
                    }
                ?>
                    <div class="booking-card">
                        <div class="booking-image">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Event">
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </div>
                        <div class="booking-details">
                            <div class="booking-header">
                                <h3 class="booking-title"><?php echo htmlspecialchars($request['event_title']); ?></h3>
                            </div>
                            <div class="booking-meta">
                                <span><i class="fa-solid fa-box"></i> <?php echo ucfirst($request['base_package_tier']); ?> Tier Base</span>
                                <span><i class="fa-solid fa-money-bill"></i> Proposed: Rs. <?php echo number_format($request['proposed_price'], 2); ?></span>
                                <span><i class="fa-solid fa-user"></i> Client: <?php echo htmlspecialchars($request['client_name']); ?></span>
                                <span><i class="fa-regular fa-clock"></i> Date: <?php echo date('M d, Y', strtotime($request['created_at'])); ?></span>
                            </div>
                            <div class="card-actions">
                                <a href="/EventManagementSystem/public/organizer/requests/view?id=<?php echo $request['id']; ?>" class="btn-secondary">Manage Request</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Mimic the booking layout styles */
.bookings-grid { display: grid; gap: 24px; }
.booking-card { display: flex; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border-color); }
.booking-image { width: 280px; position: relative; }
.booking-image img { width: 100%; height: 100%; object-fit: cover; }
.status-badge { position: absolute; top: 16px; right: 16px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: white; }
.status-pending { background: #f59e0b; }
.status-negotiating { background: #3b82f6; }
.status-approved { background: #10b981; }
.status-rejected { background: #ef4444; }
.status-booked { background: #8b5cf6; }
.booking-details { padding: 24px; flex: 1; display: flex; flex-direction: column; }
.booking-title { font-size: 20px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
.booking-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; flex: 1; }
.booking-meta span { display: flex; align-items: center; gap: 8px; color: var(--text-gray); font-size: 14px; }
.card-actions { margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end; }
.btn-secondary { background: #f8fafc; color: var(--text-dark); border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 20px; text-decoration: none; font-weight: 600; transition: all 0.2s; }
.btn-secondary:hover { background: #e2e8f0; }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 48px; color: var(--primary-color); margin-bottom: 16px; opacity: 0.5; }

/* Mobile Responsiveness for Custom Requests */
@media (max-width: 768px) {
    .booking-card { flex-direction: column; }
    .booking-image { width: 100%; height: 180px; }
    .booking-details { padding: 20px; }
    .booking-title { font-size: 18px; }
    .booking-meta { grid-template-columns: 1fr; gap: 8px; }
    .card-actions { justify-content: stretch; }
    .btn-secondary { text-align: center; width: 100%; }
}

@media (max-width: 480px) {
    .main-content { padding: 15px; }
    .page-title { font-size: 20px; }
    .page-subtitle { font-size: 13px; }
}
</style>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
