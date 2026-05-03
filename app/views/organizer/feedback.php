<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Organizer Panel</title>
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
    include_once __DIR__ . '/partials/sidebar.php';
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
                <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
            </div>
        </header>

        <!-- HERO BANNER -->
        <div class="np-hero">
            <div class="np-hero-left">
                <a href="/EventManagementSystem/public/organizer/dashboard" class="np-hero-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1>Client Feedback</h1>
                <p>Review what clients are saying about your events and respond to their feedback.</p>
            </div>
            <div class="np-hero-right">
                <div class="np-hero-badge">
                    <i class="fa-solid fa-star"></i>
                    <span id="total-reviews-badge">... Reviews Received</span>
                </div>
            </div>
        </div>

        <!-- STATS ROW -->
        <div class="np-stats-row" id="stats-row">
            <!-- Loading placeholders -->
            <div class="np-stat-card"><div class="np-stat-info"><div class="np-stat-label">Loading...</div></div></div>
        </div>

        <!-- FILTER BAR -->
        <div class="np-filter-bar" id="filter-bar">
            <a href="#" class="np-filter-tab active" data-rating="all">
                <i class="fa-solid fa-border-all"></i> All
                <span class="np-filter-count" id="count-all">0</span>
            </a>
            <a href="#" class="np-filter-tab" data-rating="5">
                <i class="fa-solid fa-star"></i> 5 Stars
                <span class="np-filter-count" id="count-5">0</span>
            </a>
            <a href="#" class="np-filter-tab" data-rating="4">
                <i class="fa-solid fa-star"></i> 4 Stars
                <span class="np-filter-count" id="count-4">0</span>
            </a>
            <a href="#" class="np-filter-tab" data-rating="3">
                <i class="fa-solid fa-star"></i> 3 Stars
                <span class="np-filter-count" id="count-3">0</span>
            </a>
            <a href="#" class="np-filter-tab" data-rating="2">
                <i class="fa-solid fa-star"></i> 2 Stars
                <span class="np-filter-count" id="count-2">0</span>
            </a>
            <a href="#" class="np-filter-tab" data-rating="1">
                <i class="fa-solid fa-star"></i> 1 Star
                <span class="np-filter-count" id="count-1">0</span>
            </a>
        </div>

        <div id="feedback-list" class="feedback-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 20px; border: 2px dashed #eee;">
                <p style="color: #999;"><i class="fa-solid fa-spinner fa-spin"></i> Loading feedback...</p>
            </div>
        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/mentions.js?v=<?php echo time(); ?>"></script>
    <script>
        const currentUserId = <?php echo (int)$_SESSION['user_id']; ?>;
        let currentFilter = 'all';

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

        function loadStats() {
            window.emsApi.apiFetch('/api/v1/feedback/stats')
            .then(res => {
                if (res.success) {
                    const stats = res.data;
                    document.getElementById('total-reviews-badge').textContent = `${stats.total} Reviews Received`;
                    document.getElementById('count-all').textContent = stats.total;
                    for (let i = 1; i <= 5; i++) {
                        document.getElementById(`count-${i}`).textContent = stats.counts[i];
                    }

                    document.getElementById('stats-row').innerHTML = `
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-comments"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">Total</div>
                                <div class="np-stat-value">${stats.total}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-star"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">Avg Rating</div>
                                <div class="np-stat-value">${stats.avg}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-face-smile"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">5 Stars</div>
                                <div class="np-stat-value">${stats.counts[5]}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-thumbs-up"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">4 Stars</div>
                                <div class="np-stat-value">${stats.counts[4]}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-face-meh"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">3 Stars</div>
                                <div class="np-stat-value">${stats.counts[3]}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-face-frown"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">2 Stars</div>
                                <div class="np-stat-value">${stats.counts[2]}</div>
                            </div>
                        </div>
                        <div class="np-stat-card">
                            <div class="np-stat-icon green"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <div class="np-stat-info">
                                <div class="np-stat-label">1 Star</div>
                                <div class="np-stat-value">${stats.counts[1]}</div>
                            </div>
                        </div>
                    `;
                }
            });
        }

        function loadFeedbacks() {
            const url = currentFilter === 'all' ? '/api/v1/feedback' : `/api/v1/feedback?rating=${currentFilter}`;
            window.emsApi.apiFetch(url)
            .then(res => {
                const feedbacks = res.data || [];
                const list = document.getElementById('feedback-list');
                
                if (feedbacks.length === 0) {
                    list.innerHTML = `
                        <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 20px; border: 2px dashed #eee;">
                            <i class="far fa-comment-dots" style="font-size: 50px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                            <p style="color: #999; font-size: 16px;">No feedback received yet.</p>
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
                        <div class="reply-item ${reply.user_role !== 'client' ? 'admin-reply' : ''} ${index >= 2 ? 'reply-hidden' : ''}">
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
                                <span class="reply-time">${new Date(reply.created_at).toLocaleString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true})}</span>
                            </div>
                        </div>
                    `).join('');

                    return `
                        <div class="feedback-card">
                            <div class="feedback-client" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                                ${getAvatarHtml(fb, 'client-img')}
                                <div class="client-info">
                                    <h4 style="margin: 0; color: #1a4d2e; font-size: 16px; font-weight: 700;">${fb.client_name}</h4>
                                    <span style="font-size: 12px; color: #888;">${new Date(fb.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>
                                </div>
                            </div>
                            <div class="rating-display" style="color: #FFC24A; margin-bottom: 15px; font-size: 15px;">
                                ${stars.join('')}
                            </div>
                            <p class="feedback-comment">"${fb.comment}"</p>
                            
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

                            <div style="margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 15px;">
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
            });
        }

        function handleAjaxForm(event, method = 'POST') {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const actionPath = form.getAttribute('action') || '/api/v1/feedback/reply';

            window.emsApi.apiFetch(actionPath, {
                method: method,
                body: data
            })
            .then(res => {
                if (res.success) {
                    loadFeedbacks();
                    loadStats();
                } else {
                    alert(res.message || 'Action failed.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred: ' + err.message);
            });
        }

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

        document.addEventListener('DOMContentLoaded', () => {
            loadStats();
            loadFeedbacks();

            document.querySelectorAll('.np-filter-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelectorAll('.np-filter-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    currentFilter = tab.dataset.rating;
                    loadFeedbacks();
                });
            });
        });
    </script>
</body>

</html>