<?php
/**
 * Feedback Popup Partial
 * This partial should be included at the bottom of client-side pages.
 * It provides the HTML for the rating popup modal and includes the necessary CSS/JS.
 */
if (!isset($_SESSION['user_id'])) return;
?>

<!-- ── Rating Popup Modal ── -->
<div id="rating-popup-overlay" role="dialog" aria-modal="true" aria-labelledby="rp-title">
    <div id="rating-popup">
        <button class="rp-close" id="rp-close-btn" title="Close" aria-label="Close">&times;</button>

        <!-- Form view -->
        <div id="rp-form-view">
            <div class="rp-icon"><i class="fa-solid fa-star"></i></div>
            <h2 id="rp-title">How do you rate us?</h2>
            <p class="rp-sub">Your event is complete! Share your experience to help us improve.</p>

            <!-- Stars -->
            <div class="rp-stars" id="rp-stars" role="group" aria-label="Star rating">
                <span class="rp-star" data-val="1" title="1 – Poor"><i class="fa-solid fa-star"></i></span>
                <span class="rp-star" data-val="2" title="2 – Fair"><i class="fa-solid fa-star"></i></span>
                <span class="rp-star" data-val="3" title="3 – Good"><i class="fa-solid fa-star"></i></span>
                <span class="rp-star" data-val="4" title="4 – Very Good"><i class="fa-solid fa-star"></i></span>
                <span class="rp-star" data-val="5" title="5 – Excellent"><i class="fa-solid fa-star"></i></span>
            </div>
            <div id="rp-rating-label">Tap a star to rate</div>

            <label class="rp-label" for="rp-comment">Feedback</label>
            <textarea id="rp-comment" rows="4"
                placeholder="Tell us what you liked or what we can improve..."></textarea>

            <div id="rp-error"></div>

            <button id="rp-submit" disabled>Submit</button>
            <button id="rp-not-now">Not now</button>
        </div>

        <!-- Success view -->
        <div id="rp-success">
            <div class="rp-success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h3>Thank you!</h3>
            <p>Your feedback has been submitted successfully.</p>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/EventManagementSystem/public/assets/css/feedback-popup.css?v=<?php echo time(); ?>">
<script src="/EventManagementSystem/public/assets/js/feedback-popup.js?v=<?php echo time(); ?>"></script>
