<aside class="sidebar">
    <div>
        <div class="brand"><img src="/EventManagementSystem/public/assets/images/logo-white.png" alt="e.PLAN" style="height: 48px; width: auto; object-fit: contain; margin-left: -5px;"></div>
        <nav>
            <ul>
                <li><a href="/EventManagementSystem/public/organizer/dashboard"
                        class="<?php echo ($activePage == 'dashboard' || empty($activePage)) ? 'active' : ''; ?>"><i
                            class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="/EventManagementSystem/public/organizer/events"
                        class="<?php echo ($activePage == 'events') ? 'active' : ''; ?>"><i
                            class="far fa-calendar-alt"></i> Events</a></li>
                <li><a href="/EventManagementSystem/public/organizer/bookings" class="<?php echo ($activePage == 'bookings') ? 'active' : ''; ?>"><i
                            class="far fa-calendar-check"></i> Bookings</a></li>
                <li><a href="/EventManagementSystem/public/organizer/tickets" class="<?php echo ($activePage == 'tickets') ? 'active' : ''; ?>"><i
                            class="fas fa-ticket-alt"></i> Tickets</a></li>

                <li><a href="/EventManagementSystem/public/organizer/requests" class="<?php echo ($activePage == 'requests') ? 'active' : ''; ?>"><i
                            class="fas fa-file-signature"></i> Requests</a></li>
                <li><a href="/EventManagementSystem/public/organizer/promo-codes" class="<?php echo ($activePage == 'promo_codes') ? 'active' : ''; ?>"><i
                            class="fas fa-tags"></i> Promo Codes</a></li>
                <li><a href="/EventManagementSystem/public/organizer/payment_details" class="<?php echo ($activePage == 'payment_details') ? 'active' : ''; ?>"><i
                            class="fas fa-wallet"></i> Payment Details</a></li>
                <li><a href="/EventManagementSystem/public/organizer/feedback" class="<?php echo ($activePage == 'feedback') ? 'active' : ''; ?>"><i
                            class="far fa-comment-dots"></i> Feedback</a></li>
            </ul>
        </nav>
    </div>
</aside>
