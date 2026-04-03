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

                <li><a href="/EventManagementSystem/public/organizer/messages" class="<?php echo ($activePage == 'messages') ? 'active' : ''; ?>"><i
                            class="far fa-envelope"></i> Messages</a></li>
            </ul>
        </nav>
    </div>
    <div class="sidebar-bottom">

        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_fullname'] ?? 'User'); ?>&background=0D8ABC&color=fff" alt="Profile" style="border-radius:50%; object-fit:cover;">
            <div class="info">
                <h4>
                    <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?>
                </h4>
                <span>Event Organizer</span>
            </div>
        </div>
    </div>
</aside>