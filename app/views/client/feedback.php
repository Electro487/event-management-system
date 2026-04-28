<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback — e.PLAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/client-home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
        href="/EventManagementSystem/public/assets/css/all-notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/feedback.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: #f4f7f6;
        }

        .feedback-page-client {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }
    </style>
</head>

<body>
    <?php
    $initials = '';
    $nameParts = explode(' ', $_SESSION['user_fullname'] ?? 'User');
    foreach ($nameParts as $p) {
        if (!empty($p))
            $initials .= strtoupper(substr($p, 0, 1));
    }
    if (strlen($initials) > 2)
        $initials = substr($initials, 0, 2);
    $displayName = $_SESSION['user_fullname'] ?? 'User';
    $firstName = $nameParts[0] ?? '';
    $lastName = count($nameParts) > 1 ? end($nameParts) : '';
    ?>

    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/client/home" class="logo">
            <img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;">
        </a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <a href="/EventManagementSystem/public/client/events#my-bookings">My Bookings</a>
        </nav>
        <div class="nav-icons">
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
                                <input type="file" id="profile_picture_upload" accept="image/*" style="display:none;"
                                    onchange="uploadProfilePicture(this)">
                            </div>
                            <h3 class="pd-name"><?php echo htmlspecialchars($displayName); ?></h3>
                            <p class="pd-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            <span class="pd-role">Client</span>
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
            <?php endif; ?>
        </div>
    </header>

    <div class="feedback-page-client">
        <!-- HERO (Matching All Notifications Style) -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/client/home" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Home
                </a>
                <h1>Share Your Experience</h1>
                <p>Help us improve our curation by providing your honest feedback and rating about our event services.
                </p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-regular fa-star"></i>
                    <span><?php echo count($feedbacks); ?> Feedback<?php echo count($feedbacks) !== 1 ? 's' : ''; ?>
                        Shared</span>
                </div>
            </div>
        </div>

        <div class="feedback-container">
            <div class="feedback-layout"
                style="display: grid; grid-template-columns: 350px 1fr; gap: 40px; align-items: start;">

                <!-- Left: Feedback Form -->
                <div
                    style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 100px;">
                    <h2 style="color: #1a4d2e; margin-bottom: 10px; font-weight: 700; font-size: 20px;">Submit Feedback
                    </h2>
                    <p style="color: #666; margin-bottom: 30px; font-size: 14px;">How would you rate our planning?</p>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div
                            style="background: #e6fcf0; color: #1a4d2e; padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; border: 1px solid #d1fae5;">
                            <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/EventManagementSystem/public/client/feedback/store" method="POST">
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label
                                style="display: block; font-weight: 600; color: #333; margin-bottom: 12px; font-size: 15px;">Rating</label>
                            <div class="star-rating"
                                style="display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 10px;">
                                <input type="radio" id="star5" name="rating" value="5" required style="display:none;" />
                                <label for="star5" title="5 stars"
                                    style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                        class="fas fa-star"></i></label>

                                <input type="radio" id="star4" name="rating" value="4" style="display:none;" />
                                <label for="star4" title="4 stars"
                                    style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                        class="fas fa-star"></i></label>

                                <input type="radio" id="star3" name="rating" value="3" style="display:none;" />
                                <label for="star3" title="3 stars"
                                    style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                        class="fas fa-star"></i></label>

                                <input type="radio" id="star2" name="rating" value="2" style="display:none;" />
                                <label for="star2" title="2 stars"
                                    style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                        class="fas fa-star"></i></label>

                                <input type="radio" id="star1" name="rating" value="1" style="display:none;" />
                                <label for="star1" title="1 star"
                                    style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                        class="fas fa-star"></i></label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="comment"
                                style="display: block; font-weight: 600; color: #333; margin-bottom: 12px; font-size: 15px;">Comments</label>
                            <textarea name="comment" id="comment" rows="4" class="form-control"
                                placeholder="Share your thoughts..." required
                                style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #fcfcfc; font-family: inherit; font-size: 14px; resize: none;"></textarea>
                        </div>

                        <button type="submit"
                            style="width: 100%; background: #1a4d2e; color: white; border: none; padding: 16px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.3s;">
                            Submit Feedback
                        </button>
                    </form>
                </div>

                <!-- Right: Past Feedbacks -->
                <div class="feedback-grid">
                    <h2 style="color: #1a4d2e; font-weight: 700; margin-bottom: 10px; font-size: 18px;">Review History
                    </h2>
                    <?php if (empty($feedbacks)): ?>
                        <div
                            style="background: white; padding: 40px; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                            <p style="color: #888;">No feedback history found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="feedback-card">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <div style="color: #ffcf96; font-size: 14px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= $fb['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span
                                        style="font-size: 12px; color: #999;"><?php echo date('M d, Y', strtotime($fb['created_at'])); ?></span>
                                </div>
                                <div class="feedback-text-container">
                                    <p class="feedback-comment" id="fb-comment-<?php echo $fb['id']; ?>">"<?php echo htmlspecialchars($fb['comment']); ?>"</p>
                                    <?php if ($_SESSION['user_id'] == $fb['client_id']): ?>
                                        <button onclick="toggleEditFeedback(<?php echo $fb['id']; ?>)" class="btn-edit-inline">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        <form action="/EventManagementSystem/public/feedback/edit" method="POST" class="edit-form"
                                            id="edit-fb-<?php echo $fb['id']; ?>" style="display:none; margin-top: 15px;" onsubmit="handleAjaxForm(event)">
                                            <input type="hidden" name="feedback_id" value="<?php echo $fb['id']; ?>">
                                            <textarea name="comment"
                                                class="reply-textarea"><?php echo htmlspecialchars($fb['comment']); ?></textarea>
                                            <div class="reply-submit-row">
                                                <button type="button" onclick="toggleEditFeedback(<?php echo $fb['id']; ?>)"
                                                    class="btn-mini-cancel">Cancel</button>
                                                <button type="submit" class="btn-mini-send">Save</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <!-- REPLIES THREAD -->
                                <div class="replies-thread" id="thread-<?php echo $fb['id']; ?>">
                                    <?php foreach ($fb['replies'] as $index => $reply): ?>
                                        <div class="reply-item <?php echo ($reply['user_role'] !== 'client') ? 'admin-reply' : ''; ?> <?php echo ($index >= 2) ? 'reply-hidden' : ''; ?>"
                                            data-index="<?php echo $index; ?>">
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
                                                        <button onclick="toggleEditReply(<?php echo $reply['id']; ?>)"
                                                            class="btn-edit-inline" style="font-size: 11px; margin-top: 5px;">
                                                            <i class="fa-solid fa-pen"></i> Edit
                                                        </button>
                                                        <form action="/EventManagementSystem/public/feedback/editReply" method="POST"
                                                            class="edit-form" id="edit-reply-<?php echo $reply['id']; ?>"
                                                            style="display:none; margin-top: 10px;" onsubmit="handleAjaxForm(event)">
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
                                        <button onclick="showMoreReplies(<?php echo $fb['id']; ?>)"
                                            id="more-btn-<?php echo $fb['id']; ?>" class="btn-more-replies">
                                            <i class="fa-solid fa-chevron-down"></i> Show More
                                        </button>
                                        <button onclick="showLessReplies(<?php echo $fb['id']; ?>)"
                                            id="less-btn-<?php echo $fb['id']; ?>" class="btn-more-replies" style="display:none;">
                                            <i class="fa-solid fa-chevron-up"></i> Show Less
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <!-- Client Reply Action -->
                                <div style="margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 10px;">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        function toggleProfileDropdown() {
            document.getElementById('profile-dropdown').classList.toggle('show');
        }

        document.addEventListener('click', function (e) {
            const profileC = document.getElementById('profile-container');
            if (profileC && !profileC.contains(e.target)) {
                document.getElementById('profile-dropdown').classList.remove('show');
            }
        });

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

        function toggleEditFeedback(id) {
            const form = document.getElementById('edit-fb-' + id);
            const text = document.getElementById('fb-comment-' + id);
            if (form.style.display === 'none') {
                form.style.display = 'block';
                text.style.display = 'none';
            } else {
                form.style.display = 'none';
                text.style.display = 'block';
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

            // Show 3 more
            let count = 0;
            hiddenReplies.forEach(r => {
                if (count < 3) {
                    r.classList.remove('reply-hidden');
                    count++;
                }
            });

            // If no more hidden, hide "More" btn
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

        function uploadProfilePicture(input) {
            if (!input.files || !input.files[0]) return;
            const fd = new FormData();
            fd.append('profile_picture', input.files[0]);
            fetch('/EventManagementSystem/public/client/profile/update', {
                method: 'POST',
                body: fd
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Upload failed.');
                    }
                }).catch(() => alert('An error occurred during upload.'));
        }

        function deleteProfilePicture() {
            if (!confirm('Remove your profile picture?')) return;
            fetch('/EventManagementSystem/public/client/profile/delete-picture', {
                method: 'POST'
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error removing image.');
                    }
                }).catch(() => alert('An error occurred.'));
        }
    </script>
</body>

</html>