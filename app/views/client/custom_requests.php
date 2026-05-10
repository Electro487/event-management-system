<?php
$title = "My Custom Requests - e.PLAN";
$activePage = "requests";
require_once __DIR__ . '/partials/header.php';
?>

<div class="dashboard-container" style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <div class="main-content">
        <div class="content-header">
            <h1 class="page-title">My Custom Requests</h1>
            <p class="page-subtitle">Track your package negotiation requests</p>
        </div>

        <div class="bookings-grid">
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-code-pull-request"></i>
                    <h3>No requests found</h3>
                    <p>You haven't made any custom package requests yet.</p>
                    <a href="/EventManagementSystem/public/client/events" class="btn-primary" style="text-decoration:none; display:inline-block; margin-top:20px;">Browse Events</a>
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
                                <span><i class="fa-solid fa-user-tie"></i> Organizer: <?php echo htmlspecialchars($request['organizer_name']); ?></span>
                                <span><i class="fa-regular fa-clock"></i> Date: <?php echo date('M d, Y', strtotime($request['created_at'])); ?></span>
                            </div>
                            <div class="card-actions">
                                <a href="/EventManagementSystem/public/client/requests/view?id=<?php echo $request['id']; ?>" class="btn-secondary">View Thread</a>
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
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 48px; color: var(--primary-color); margin-bottom: 16px; opacity: 0.5; }
</style>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
