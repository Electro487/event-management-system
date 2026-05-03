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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback – e.PLAN</title>
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
    <!-- Navbar -->
    <header class="header">
        <a href="/EventManagementSystem/public/client/home" class="logo">
            <img src="/EventManagementSystem/public/assets/images/logo.png" alt="e.PLAN"
                style="height: 26px; width: auto; object-fit: contain; transform: scale(1.7); transform-origin: left center;">
        </a>
        <nav class="nav-links">
            <a href="/EventManagementSystem/public/client/home">Home</a>
            <a href="/EventManagementSystem/public/client/events">Browse Events</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/EventManagementSystem/public/client/bookings">My Bookings</a>
                <a href="/EventManagementSystem/public/client/tickets">My Tickets</a>
            <?php endif; ?>
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
                            <a href="/EventManagementSystem/public/client/feedback" class="pd-rating-btn">
                                <i class="fa-solid fa-star"></i> Rating &amp; Feedback
                            </a>
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
        <!-- HERO -->
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
                    <span id="feedback-count-badge">... Feedbacks Shared</span>
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

                    <div id="alert-container"></div>

                    <form id="main-feedback-form" action="/api/v1/feedback" method="POST">
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label
                                style="display: block; font-weight: 600; color: #333; margin-bottom: 12px; font-size: 15px;">Rating</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="star-rating"
                                    style="display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 10px;">
                                    <input type="radio" id="star5" name="rating" value="5" required
                                        style="display:none;" />
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
                                <span id="fb-rating-text"
                                    style="font-size: 14px; font-weight: 700; color: #94a3b8; transition: color 0.2s;">Tap
                                    a star</span>
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
                <div class="feedback-grid-container">
                    <h2 style="color: #1a4d2e; font-weight: 700; margin-bottom: 10px; font-size: 18px;">Review History
                    </h2>
                    <div id="feedback-list" class="feedback-grid">
                        <!-- Loading state -->
                        <div
                            style="background: white; padding: 40px; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                            <p style="color: #888;"><i class="fa-solid fa-spinner fa-spin"></i> Loading review
                                history...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/mentions.js?v=<?php echo time(); ?>"></script>
    <script>
        const currentUserId = <?php echo (int) ($_SESSION['user_id'] ?? 0); ?>;

        function getAvatarHtml(user, className = "reply-avatar") {
            if (user.profile_picture) {
                return `<img src="${user.profile_picture}" class="${className}">`;
            }
            const nameParts = (user.user_name || user.client_name || "User").trim().split(" ");
            const initials = (nameParts[0]?.[0] || "") + (nameParts.length > 1 ? nameParts[nameParts.length - 1][0] : "");
            const roleClass = (user.user_role === 'client' || !user.user_role) ? 'client' : 'staff';
            const fontSize = className.includes('client-img') ? '16px' : '12px';
            return `<div class="default-avatar ${roleClass} ${className}" style="font-size: ${fontSize};">${initials.toUpperCase() || "??"}</div>`;
        }

        function toggleProfileDropdown() {
            document.getElementById('profile-dropdown').classList.toggle('show');
        }
        document.addEventListener('click', function (e) {
            const c = document.getElementById('profile-container');
            if (c && !c.contains(e.target)) {
                document.getElementById('profile-dropdown').classList.remove('show');
            }
        });

        // Dynamic Rating Text Logic
        document.addEventListener('DOMContentLoaded', () => {
            const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            const labelColors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e', '#1a4d2e'];

            const starInputs = document.querySelectorAll('.star-rating input');
            const starLabels = document.querySelectorAll('.star-rating label');
            const textEl = document.getElementById('fb-rating-text');

            let selectedValue = 0;

            function updateText(val) {
                if (val > 0) {
                    textEl.textContent = labels[val];
                    textEl.style.color = labelColors[val];
                } else {
                    textEl.textContent = 'Tap a star';
                    textEl.style.color = '#94a3b8';
                }
            }

            starLabels.forEach(label => {
                label.addEventListener('mouseenter', () => {
                    const input = document.getElementById(label.getAttribute('for'));
                    updateText(input.value);
                });
                label.addEventListener('mouseleave', () => {
                    updateText(selectedValue);
                });
            });

            starInputs.forEach(input => {
                input.addEventListener('change', () => {
                    selectedValue = input.value;
                    updateText(selectedValue);
                });
            });
        });

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

        function loadFeedbacks() {
            window.emsApi.apiFetch('/api/v1/feedback/my')
                .then(res => {
                    const feedbacks = res.data || [];
                    const list = document.getElementById('feedback-list');
                    document.getElementById('feedback-count-badge').textContent = `${feedbacks.length} Feedback${feedbacks.length !== 1 ? 's' : ''} Shared`;

                    if (feedbacks.length === 0) {
                        list.innerHTML = `
                        <div style="background: white; padding: 40px; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                            <p style="color: #888;">No feedback history found.</p>
                        </div>
                    `;
                        return;
                    }

                    list.innerHTML = feedbacks.map(fb => {
                        const stars = [];
                        for (let i = 1; i <= 5; i++) {
                            stars.push(`<i class="${i <= fb.rating ? 'fas' : 'far'} fa-star"></i>`);
                        }

                        const repliesHtml = fb.replies.map((reply, index) => `
                        <div class="reply-item ${reply.user_role !== 'client' ? 'admin-reply' : ''} ${index >= 2 ? 'reply-hidden' : ''}" data-index="${index}">
                            ${getAvatarHtml(reply, 'reply-avatar')}
                            <div class="reply-content">
                                <div class="reply-user-info">
                                    <h5>${reply.user_name}</h5>
                                    ${reply.user_role !== 'client' ? `<span class="role-badge">${reply.user_role.toUpperCase()}</span>` : ''}
                                </div>
                                <div class="reply-text-container">
                                    <p class="reply-text" id="reply-text-${reply.id}">${reply.reply_text}</p>
                                    ${currentUserId == reply.user_id ? `
                                        <button onclick="toggleEditReply(${reply.id})" class="btn-edit-inline" style="font-size: 11px; margin-top: 5px;">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        <form action="/api/v1/feedback/reply" method="PATCH" class="edit-form" id="edit-reply-${reply.id}" style="display:none; margin-top: 10px;" onsubmit="handleAjaxForm(event, 'PATCH')">
                                            <input type="hidden" name="reply_id" value="${reply.id}">
                                            <textarea name="reply_text" class="reply-textarea" style="font-size: 13px;">${reply.reply_text}</textarea>
                                            <div class="reply-submit-row">
                                                <button type="button" onclick="toggleEditReply(${reply.id})" class="btn-mini-cancel">Cancel</button>
                                                <button type="submit" class="btn-mini-send">Save</button>
                                            </div>
                                        </form>
                                    ` : ''}
                                </div>
                                <span class="reply-time">${new Date(reply.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                            </div>
                        </div>
                    `).join('');

                        return `
                        <div class="feedback-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                <div style="color: #ffcf96; font-size: 14px;">
                                    ${stars.join('')}
                                </div>
                                <span style="font-size: 12px; color: #999;">${new Date(fb.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                            </div>
                            <div class="feedback-text-container">
                                <p class="feedback-comment" id="fb-comment-${fb.id}">"${fb.comment}"</p>
                                ${currentUserId == fb.client_id ? `
                                    <button onclick="toggleEditFeedback(${fb.id})" class="btn-edit-inline">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <form action="/api/v1/feedback" method="PATCH" class="edit-form" id="edit-fb-${fb.id}" style="display:none; margin-top: 15px;" onsubmit="handleAjaxForm(event, 'PATCH')">
                                        <input type="hidden" name="feedback_id" value="${fb.id}">
                                        <textarea name="comment" class="reply-textarea">${fb.comment}</textarea>
                                        <div class="reply-submit-row">
                                            <button type="button" onclick="toggleEditFeedback(${fb.id})" class="btn-mini-cancel">Cancel</button>
                                            <button type="submit" class="btn-mini-send">Save</button>
                                        </div>
                                    </form>
                                ` : ''}
                            </div>
                            
                            <div class="replies-thread" id="thread-${fb.id}">
                                ${repliesHtml}
                            </div>

                            ${fb.replies.length > 2 ? `
                                <div class="more-less-controls" style="margin-bottom: 15px;">
                                    <button onclick="showMoreReplies(${fb.id})" id="more-btn-${fb.id}" class="btn-more-replies">
                                        <i class="fa-solid fa-chevron-down"></i> Show More
                                    </button>
                                    <button onclick="showLessReplies(${fb.id})" id="less-btn-${fb.id}" class="btn-more-replies" style="display:none;">
                                        <i class="fa-solid fa-chevron-up"></i> Show Less
                                    </button>
                                </div>
                            ` : ''}

                            <div style="margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 10px;">
                                <button onclick="toggleReplyBox(${fb.id})" class="btn-inline-reply">
                                    <i class="fa-solid fa-reply"></i> Reply
                                </button>
                                <div class="reply-box" id="reply-box-${fb.id}">
                                    <form action="/api/v1/feedback/reply" method="POST" onsubmit="handleAjaxForm(event)">
                                        <input type="hidden" name="feedback_id" value="${fb.id}">
                                        <textarea name="reply" class="reply-textarea" placeholder="Type your response..." required></textarea>
                                        <div class="reply-submit-row">
                                            <button type="button" onclick="toggleReplyBox(${fb.id})" class="btn-mini-cancel">Cancel</button>
                                            <button type="submit" class="btn-mini-send">Send Reply</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    }).join('');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('feedback-list').innerHTML = `
                    <div style="background: white; padding: 40px; border-radius: 20px; text-align: center; border: 1px solid #fee2e2;">
                        <p style="color: #b91c1c;">Failed to load feedback history. Please try again later.</p>
                    </div>
                `;
                });
        }

        function handleAjaxForm(event, method = 'POST') {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const actionPath = form.getAttribute('action') || '/api/v1/feedback';

            window.emsApi.apiFetch(actionPath, {
                method: method,
                body: data
            })
                .then(res => {
                    if (res.success) {
                        loadFeedbacks();
                        if (form.id === 'main-feedback-form') {
                            form.reset();
                            document.getElementById('alert-container').innerHTML = `
                            <div style="background: #e6fcf0; color: #1a4d2e; padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; border: 1px solid #d1fae5;">
                                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>
                                Feedback submitted successfully!
                            </div>
                        `;
                        }
                    } else {
                        alert(res.message || 'Action failed.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred: ' + err.message);
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadFeedbacks();

            document.getElementById('main-feedback-form').addEventListener('submit', (e) => {
                handleAjaxForm(e);
            });
        });

        function uploadProfilePicture(input) {
            if (!input.files || !input.files[0]) return;
            const fd = new FormData();
            fd.append('profile_picture', input.files[0]);
            window.emsApi.apiFetch('/api/v1/auth/profile/picture', {
                method: 'POST',
                body: fd
            })
                .then(data => {
                    if (data.success) location.reload();
                    else alert(data.message || 'Upload failed.');
                })
                .catch(err => alert('Upload failed: ' + err.message));
        }

        function deleteProfilePicture() {
            if (!confirm('Remove your profile picture?')) return;
            window.emsApi.apiFetch('/api/v1/auth/profile/picture', {
                method: 'DELETE'
            })
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error removing image.');
                })
                .catch(err => alert('Delete failed: ' + err.message));
        }
    </script>
</body>

</html>