<aside class="sidebar">
    <div>
        <div class="brand">e-Plan</div>
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

                <li><a href="/EventManagementSystem/public/organizer/messages" class="<?php echo ($activePage == 'messages') ? 'active' : ''; ?>"><i
                            class="far fa-envelope"></i> Messages</a></li>
            </ul>
        </nav>
    </div>
</aside>