<?php
$title = 'Feedback – e.PLAN';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/feedback.css?v=' . time() . '">
';
include 'partials/header.php';
?>


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
            <div class="feedback-layout">

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
                                        style="position: absolute; opacity: 0; pointer-events: none;" />
                                    <label for="star5" title="5 stars"
                                        style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                            class="fas fa-star"></i></label>

                                    <input type="radio" id="star4" name="rating" value="4" style="position: absolute; opacity: 0; pointer-events: none;" />
                                    <label for="star4" title="4 stars"
                                        style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                            class="fas fa-star"></i></label>

                                    <input type="radio" id="star3" name="rating" value="3" style="position: absolute; opacity: 0; pointer-events: none;" />
                                    <label for="star3" title="3 stars"
                                        style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                            class="fas fa-star"></i></label>

                                    <input type="radio" id="star2" name="rating" value="2" style="position: absolute; opacity: 0; pointer-events: none;" />
                                    <label for="star2" title="2 stars"
                                        style="font-size: 28px; color: #ddd; cursor: pointer; transition: 0.2s;"><i
                                            class="fas fa-star"></i></label>

                                    <input type="radio" id="star1" name="rating" value="1" style="position: absolute; opacity: 0; pointer-events: none;" />
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
                                placeholder="Share your thoughts..." required></textarea>
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
                    <div id="pagination-container" style="display: flex; gap: 8px; justify-content: center; margin-top: 30px; align-items: center;"></div>
                </div>
            </div>
        </div>
    </div>

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

        let currentPage = 1;
        const itemsPerPage = 10;
        let totalItemsCount = 0;

        function loadFeedbacks() {
            window.emsApi.apiFetch(`/api/v1/feedback/my?page=${currentPage}&limit=${itemsPerPage}`)
                .then(res => {
                    const paginatedItems = res.data || [];
                    totalItemsCount = res.pagination ? res.pagination.total : paginatedItems.length;
                    
                    if (currentPage === 1) {
                        document.getElementById('feedback-count-badge').textContent = `${totalItemsCount} Feedback${totalItemsCount !== 1 ? 's' : ''} Shared`;
                    }
                    renderFeedbacks(paginatedItems);
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

        function renderFeedbacks(paginatedItems) {
            const list = document.getElementById('feedback-list');

            if (paginatedItems.length === 0 && currentPage === 1) {
                list.innerHTML = `
                <div style="background: white; padding: 40px; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                    <p style="color: #888;">No feedback history found.</p>
                </div>
            `;
                document.getElementById('pagination-container').innerHTML = '';
                return;
            }

            list.innerHTML = paginatedItems.map(fb => {
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
                            <div class="feedback-card-header">
                                <div class="rating-stars">
                                    ${stars.join('')}
                                </div>
                                <span class="feedback-date">${new Date(fb.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
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
                    renderPagination(totalItemsCount);
                }

        function renderPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const container = document.getElementById('pagination-container');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            const btnBaseStyle = "width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; cursor:pointer; transition:all 0.2s; border:1px solid #e2e8f0; background:#fff; color:#0f172a;";
            const activeStyle = "background:#004d40; color:#fff; border:1px solid #004d40; box-shadow: 0 4px 6px -1px rgba(0, 77, 64, 0.2);";
            const disabledStyle = "opacity:0.5; cursor:not-allowed; color:#cbd5e1;";
            
            let html = `<button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})" style="${btnBaseStyle} ${currentPage === 1 ? disabledStyle : ''}">
                <i class="fas fa-chevron-left"></i>
            </button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<button onclick="changePage(${i})" style="${btnBaseStyle} ${i === currentPage ? activeStyle : ''}">${i}</button>`;
                } else if ((i === 2 && currentPage > 3) || (i === totalPages - 1 && currentPage < totalPages - 2)) {
                    html += `<span style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-weight:700; font-size:15px;">...</span>`;
                    i = (i === 2) ? currentPage - 2 : totalPages - 1;
                }
            }
            
            html += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})" style="${btnBaseStyle} ${currentPage === totalPages ? disabledStyle : ''}">
                <i class="fas fa-chevron-right"></i>
            </button>`;
            
            container.innerHTML = html;
        }

        window.changePage = (page) => {
            currentPage = page;
            loadFeedbacks();
        };

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
    </script>
<?php include 'partials/footer.php'; ?>
