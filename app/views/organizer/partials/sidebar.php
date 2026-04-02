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
                <li><a href="#" class="<?php echo ($activePage == 'bookings') ? 'active' : ''; ?>"><i
                            class="far fa-calendar-check"></i> Bookings</a></li>
                <li><a href="#" class="<?php echo ($activePage == 'packages') ? 'active' : ''; ?>"><i
                            class="fas fa-box"></i> Packages</a></li>
                <li><a href="#" class="<?php echo ($activePage == 'messages') ? 'active' : ''; ?>"><i
                            class="far fa-envelope"></i> Messages</a></li>
            </ul>
        </nav>
    </div>
    <div class="sidebar-bottom">
        <a href="#" class="settings-link"><i class="fas fa-cog"></i> Settings</a>
        <div class="user-profile">
            <img src="/EventManagementSystem/public/assets/images/default-avatar.png" alt="Profile"
                onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_fullname'] ?? 'User'); ?>&background=0D8ABC&color=fff'">
            <div class="info">
                <h4>
                    <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?>
                </h4>
                <span>Event Organizer</span>
            </div>
        </div>
    </div>
</aside>