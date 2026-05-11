/**
 * Feedback Popup JS
 * Handles the logic for showing the rating popup on all client pages except the feedback page.
 */
(function () {
    // 1. Check if we are on the feedback page - avoid showing the popup there
    if (window.location.pathname.includes('/client/feedback')) return;

    if (!window.emsApi) return;

    const STORAGE_KEY = 'ems_rating_popup';
    const SNOOZE_DAYS = 7;

    function initPopup(currentCompletedCount) {
        if (currentCompletedCount <= 0) return;

        /* ── Decide whether to show ── */
        function shouldShow() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return true;
                const { action, ts, completedCountAtTime } = JSON.parse(raw);

                // Show again if a NEW event was completed since last action
                if (currentCompletedCount > (completedCountAtTime || 0)) {
                    return true;
                }

                if (action === 'submitted') return false;          // submitted for current events = hide
                if (action === 'snoozed') {
                    const days = (Date.now() - ts) / 86400000;
                    return days >= SNOOZE_DAYS;                    // snoozed = re-show after 7 days
                }
            } catch (_) { }
            return true;
        }

        if (!shouldShow()) return;

        function saveState(action) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                action,
                ts: Date.now(),
                completedCountAtTime: currentCompletedCount
            }));
        }

        /* ── Elements ── */
        const overlay = document.getElementById('rating-popup-overlay');
        const closeBtn = document.getElementById('rp-close-btn');
        const notNow = document.getElementById('rp-not-now');
        const submitBtn = document.getElementById('rp-submit');
        const comment = document.getElementById('rp-comment');
        const errEl = document.getElementById('rp-error');
        const formView = document.getElementById('rp-form-view');
        const successView = document.getElementById('rp-success');
        const ratingLabel = document.getElementById('rp-rating-label');
        const stars = document.querySelectorAll('.rp-star');

        if (!overlay || !submitBtn || !comment || !stars.length) return;

        let selectedRating = 0;
        const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        const labelColors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e', '#1f6f59'];

        /* ── Star interactions ── */
        stars.forEach(star => {
            star.addEventListener('mouseenter', () => {
                const v = +star.dataset.val;
                stars.forEach(s => s.classList.toggle('hover', +s.dataset.val <= v));
            });
            star.addEventListener('mouseleave', () => {
                stars.forEach(s => s.classList.remove('hover'));
            });
            star.addEventListener('click', () => {
                selectedRating = +star.dataset.val;
                stars.forEach(s => s.classList.toggle('active', +s.dataset.val <= selectedRating));
                ratingLabel.textContent = labels[selectedRating];
                ratingLabel.style.color = labelColors[selectedRating];
                submitBtn.disabled = false;
            });
        });

        /* ── Open / close ── */
        function openPopup() { overlay.classList.add('show'); }
        function closePopup() { overlay.classList.remove('show'); }

        closeBtn.addEventListener('click', () => { saveState('snoozed'); closePopup(); });
        notNow.addEventListener('click', () => { saveState('snoozed'); closePopup(); });
        overlay.addEventListener('click', (e) => { if (e.target === overlay) { saveState('snoozed'); closePopup(); } });

        /* ── Submit ── */
        submitBtn.addEventListener('click', () => {
            if (!selectedRating) return;
            const commentText = comment.value.trim();
            if (!commentText) {
                errEl.textContent = 'Please add a short comment before submitting.';
                errEl.style.display = 'block';
                comment.focus();
                return;
            }
            errEl.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

            window.emsApi.apiFetch('/api/v1/feedback', {
                method: 'POST',
                body: { rating: selectedRating, comment: commentText }
            })
                .then(res => {
                    if (res.success) {
                        saveState('submitted');
                        formView.style.display = 'none';
                        successView.style.display = 'flex';
                        setTimeout(closePopup, 2600);
                    } else {
                        errEl.textContent = res.message || 'Submission failed. Please try again.';
                        errEl.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit';
                    }
                })
                .catch(err => {
                    errEl.textContent = 'Network error: ' + err.message;
                    errEl.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit';
                });
        });

        setTimeout(openPopup, 1800);
    }

    // Fetch dashboard stats to get completed count
    window.emsApi.apiFetch('/api/v1/dashboard/client')
        .then(data => {
            if (data && data.data) {
                const completed = data.data.completed_count || 0;
                initPopup(completed);
            }
        })
        .catch(err => console.warn('[Feedback Popup] Failed to fetch stats:', err.message));
})();
