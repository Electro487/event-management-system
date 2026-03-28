<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">e.PLAN</div>
        <p class="role-tag">ARCHITECTURAL CURATOR</p>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/EventManagementSystem/public/organizer/dashboard" class="<?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">
                    <span class="icon">⊞</span> Dashboard
                </a>
            </li>
            <li>
                <a href="/EventManagementSystem/public/organizer/events" class="<?php echo ($activePage == 'events') ? 'active' : ''; ?>">
                    <span class="icon">📅</span> Events
                </a>
            </li>
            <li>
                <a href="/EventManagementSystem/public/organizer/bookings" class="<?php echo ($activePage == 'bookings') ? 'active' : ''; ?>">
                    <span class="icon">🎫</span> Bookings
                </a>
            </li>
            <li>
                <a href="/EventManagementSystem/public/organizer/messages" class="<?php echo ($activePage == 'messages') ? 'active' : ''; ?>">
                    <span class="icon">✉</span> Messages
                </a>
            </li>
            <li>
                <a href="/EventManagementSystem/public/organizer/settings" class="<?php echo ($activePage == 'settings') ? 'active' : ''; ?>">
                    <span class="icon">⚙</span> Settings
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="/EventManagementSystem/public/logout" class="logout-link">
            <span class="icon">🔓</span> Logout
        </a>
    </div>
</aside>
