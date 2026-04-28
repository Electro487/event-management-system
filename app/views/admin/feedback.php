<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/feedback.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    $activePage = 'feedback';
    include dirname(__DIR__) . '/admin/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="header">
            <div style="flex: 1;"></div>
            <div class="header-icons">
                <div class="notifications-wrapper">
                    <div class="notification-bell-btn" id="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                    </div>
                    <div class="notifications-dropdown" id="notifications-dropdown">
                        <div class="nd-header">
                            <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 UNREAD</span></h3>
                            <a href="#" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                        </div>
                        <div class="nd-content" id="nd-list">
                            <div class="nd-empty">
                                <i class="fa-regular fa-bell-slash"></i>
                                Loading notifications...
                            </div>
                        </div>
                        <div class="nd-footer">
                            <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All
                                Notifications <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <?php include dirname(__DIR__) . '/admin/partials/header_profile.php'; ?>
            </div>
        </header>

        <!-- HERO BANNER (Matching Notification Centre) -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/admin/dashboard" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1>Client Feedback</h1>
                <p>Monitor system-wide ratings and respond to client inquiries and suggestions.</p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-star"></i>
                    <span><?php echo $stats['total']; ?> Total Reviews</span>
                </div>
            </div>
        </div>

        <!-- STATS ROW (Matching Notification Page) -->
        <div class="np-stats-row">
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-comments"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Total</div>
                    <div class="np-stat-value"><?php echo $stats['total']; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-star"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">Avg Rating</div>
                    <div class="np-stat-value"><?php echo $stats['avg']; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-face-smile"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">5 Stars</div>
                    <div class="np-stat-value"><?php echo $stats['counts'][5]; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-thumbs-up"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">4 Stars</div>
                    <div class="np-stat-value"><?php echo $stats['counts'][4]; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-face-meh"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">3 Stars</div>
                    <div class="np-stat-value"><?php echo $stats['counts'][3]; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-face-frown"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">2 Stars</div>
                    <div class="np-stat-value"><?php echo $stats['counts'][2]; ?></div>
                </div>
            </div>
            <div class="np-stat-card">
                <div class="np-stat-icon green"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div class="np-stat-info">
                    <div class="np-stat-label">1 Star</div>
                    <div class="np-stat-value"><?php echo $stats['counts'][1]; ?></div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR (Matching Notification Page) -->
        <?php $activeRating = $_GET['rating'] ?? 'all'; ?>
        <div class="np-filter-bar">
            <a href="/EventManagementSystem/public/admin/feedback"
                class="np-filter-tab <?php echo ($activeRating === 'all') ? 'active' : ''; ?>">
                <i class="fa-solid fa-border-all"></i> All
                <span class="np-filter-count"><?php echo $stats['total']; ?></span>
            </a>
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <a href="/EventManagementSystem/public/admin/feedback?rating=<?php echo $i; ?>"
                    class="np-filter-tab <?php echo ($activeRating == $i) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-star"></i> <?php echo $i; ?> Stars
                    <span class="np-filter-count"><?php echo $stats['counts'][$i]; ?></span>
                </a>
            <?php endfor; ?>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"
                style="background: #e6fcf0; color: #1a4d2e; padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; border: 1px solid #d1fae5;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="feedback-grid">
            <?php foreach ($feedbacks as $fb): ?>
                <div class="feedback-card">
                    <div class="feedback-client"
                        style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <img src="<?php echo $fb['profile_picture'] ?: '/EventManagementSystem/public/assets/images/default-avatar.png'; ?>"
                            alt="Client" class="client-img"
                            style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #e6fcf0;">
                        <div class="client-info">
                            <h4 style="margin: 0; color: #1a4d2e; font-size: 16px; font-weight: 700;">
                                <?php echo htmlspecialchars($fb['client_name']); ?>
                            </h4>
                            <span
                                style="font-size: 12px; color: #888;"><?php echo date('M d, Y', strtotime($fb['created_at'])); ?></span>
                        </div>
                    </div>

                    <div class="rating-display" style="color: #FFC24A; margin-bottom: 15px; font-size: 15px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?php echo $i <= $fb['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>

                    <p class="feedback-comment">"<?php echo htmlspecialchars($fb['comment']); ?>"</p>

                    <!-- REPLIES THREAD (TikTok Style) -->
                    <div class="replies-thread" id="thread-<?php echo $fb['id']; ?>">
                        <?php foreach ($fb['replies'] as $index => $reply): ?>
                            <div
                                class="reply-item <?php echo ($reply['user_role'] !== 'client') ? 'admin-reply' : ''; ?> <?php echo ($index >= 2) ? 'reply-hidden' : ''; ?>">
                                <img src="<?php echo $reply['profile_picture'] ?: '/EventManagementSystem/public/assets/images/default-avatar.png'; ?>"
                                    class="reply-avatar">
                                <div class="reply-content">
                                    <div class="reply-user-info">
                                        <h5><?php echo htmlspecialchars($reply['user_name']); ?></h5>
                                        <?php if ($reply['user_role'] !== 'client'): ?>
                                            <span class="role-badge"><?php echo strtoupper($reply['user_role']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="reply-text-container">
                                        <p class="reply-text" id="reply-text-<?php echo $reply['id']; ?>">
                                            <?php echo htmlspecialchars($reply['reply_text']); ?></p>
                                        <?php if ($_SESSION['user_id'] == $reply['user_id']): ?>
                                            <button onclick="toggleEditReply(<?php echo $reply['id']; ?>)" class="btn-edit-inline"
                                                style="font-size: 11px; margin-top: 5px;">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </button>
                                            <form action="/EventManagementSystem/public/feedback/editReply" method="POST" class="edit-form"
                                                id="edit-reply-<?php echo $reply['id']; ?>" style="display:none; margin-top: 10px;" onsubmit="handleAjaxForm(event)">
                                                <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
                                                <textarea name="reply_text" class="reply-textarea"
                                                    style="font-size: 13px;"><?php echo htmlspecialchars($reply['reply_text']); ?></textarea>
                                                <div class="reply-submit-row">
                                                    <button type="button" onclick="toggleEditReply(<?php echo $reply['id']; ?>)"
                                                        class="btn-mini-cancel">Cancel</button>
                                                    <button type="submit" class="btn-mini-send">Save</button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="reply-time"><?php echo date('M d, g:i A', strtotime($reply['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($fb['replies']) > 2): ?>
                        <div class="more-less-controls" style="margin-bottom: 15px;">
                            <button onclick="showMoreReplies(<?php echo $fb['id']; ?>)" id="more-btn-<?php echo $fb['id']; ?>"
                                class="btn-more-replies">
                                <i class="fa-solid fa-chevron-down"></i> Show More
                            </button>
                            <button onclick="showLessReplies(<?php echo $fb['id']; ?>)" id="less-btn-<?php echo $fb['id']; ?>"
                                class="btn-more-replies" style="display:none;">
                                <i class="fa-solid fa-chevron-up"></i> Show Less
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Main Reply Button (Admin side) -->
                    <div style="margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 15px;">
                        <button onclick="toggleReplyBox(<?php echo $fb['id']; ?>)" class="btn-inline-reply">
                            <i class="fa-solid fa-reply"></i> Reply
                        </button>

                        <div class="reply-box" id="reply-box-<?php echo $fb['id']; ?>">
                            <form action="/EventManagementSystem/public/feedback/reply" method="POST" onsubmit="handleAjaxForm(event)">
                                <input type="hidden" name="feedback_id" value="<?php echo $fb['id']; ?>">
                                <textarea name="reply" class="reply-textarea" placeholder="Type your response..."
                                    required></textarea>
                                <div class="reply-submit-row">
                                    <button type="button" onclick="toggleReplyBox(<?php echo $fb['id']; ?>)"
                                        class="btn-mini-cancel">Cancel</button>
                                    <button type="submit" class="btn-mini-send">Send Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($feedbacks)): ?>
                <div
                    style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 20px; border: 2px dashed #eee;">
                    <i class="far fa-comment-dots"
                        style="font-size: 50px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                    <p style="color: #999; font-size: 16px;">No feedback received yet. New reviews will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        function toggleReplyBox(id) {
            const box = document.getElementById('reply-box-' + id);
            if (box.classList.contains('active')) {
                box.classList.remove('active');
            } else {
                document.querySelectorAll('.reply-box, .edit-form').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.edit-form').forEach(f => f.style.display = 'none');
                box.classList.add('active');
                box.querySelector('textarea').focus();
            }
        }

        function toggleEditReply(id) {
            const form = document.getElementById('edit-reply-' + id);
            const text = document.getElementById('reply-text-' + id);
            if (form.style.display === 'none') {
                form.style.display = 'block';
                text.style.display = 'none';
            } else {
                form.style.display = 'none';
                text.style.display = 'block';
            }
        }

        function showMoreReplies(threadId) {
            const thread = document.getElementById('thread-' + threadId);
            const hiddenReplies = thread.querySelectorAll('.reply-hidden');
            const moreBtn = document.getElementById('more-btn-' + threadId);
            const lessBtn = document.getElementById('less-btn-' + threadId);

            let count = 0;
            hiddenReplies.forEach(r => {
                if (count < 3) {
                    r.classList.remove('reply-hidden');
                    count++;
                }
            });

            if (thread.querySelectorAll('.reply-hidden').length === 0) {
                moreBtn.style.display = 'none';
            }
            lessBtn.style.display = 'flex';
        }

        function showLessReplies(threadId) {
            const thread = document.getElementById('thread-' + threadId);
            const replies = thread.querySelectorAll('.reply-item');
            const moreBtn = document.getElementById('more-btn-' + threadId);
            const lessBtn = document.getElementById('less-btn-' + threadId);

            replies.forEach((r, idx) => {
                if (idx >= 2) {
                    r.classList.add('reply-hidden');
                }
            });

            moreBtn.style.display = 'flex';
            lessBtn.style.display = 'none';
        }

        function handleAjaxForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Action failed. Please try again.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
            });
        }
    </script>
</body>

</html>