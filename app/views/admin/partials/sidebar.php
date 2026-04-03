<aside class="sidebar admin-sidebar">
    <div>
        <div class="brand"><img src="/EventManagementSystem/public/assets/images/logo-white.png" alt="e.PLAN" style="height: 48px; width: auto; object-fit: contain; margin-left: -5px;"> <br><small style="font-size: 10px; color: var(--accent-color);">ADMIN PANEL</small></div>
        <nav>
            <ul>
                <li><a href="/EventManagementSystem/public/admin/dashboard"
                        class="<?php echo ($activePage == 'dashboard' || empty($activePage)) ? 'active' : ''; ?>"><i
                            class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="/EventManagementSystem/public/admin/users"
                        class="<?php echo ($activePage == 'users') ? 'active' : ''; ?>"><i
                            class="fas fa-users-cog"></i> User Management</a></li>
                <li><a href="/EventManagementSystem/public/admin/events"
                        class="<?php echo ($activePage == 'events') ? 'active' : ''; ?>"><i
                            class="far fa-calendar-alt"></i> All Events</a></li>
                <li><a href="/EventManagementSystem/public/admin/bookings" class="<?php echo ($activePage == 'bookings') ? 'active' : ''; ?>"><i
                            class="far fa-calendar-check"></i> All Bookings</a></li>
            </ul>
        </nav>
    </div>
</aside>
