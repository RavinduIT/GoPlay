<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-map-marker-alt"></i>
            <span>Ground Manager</span>
        </div>
        <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="<?= $_SERVER['REQUEST_URI'] == '/ground-owner/dashboard' ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/grounds') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/grounds">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>My Grounds</span>
                    <span class="badge" id="groundsCount">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/bookings') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/bookings">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Bookings</span>
                    <span class="badge new" id="bookingsCount">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/earnings') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/earnings">
                    <i class="fas fa-chart-line"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/reviews') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                    <span class="badge" id="reviewsCount">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/schedule') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/schedule">
                    <i class="fas fa-clock"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/availability') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/availability">
                    <i class="fas fa-calendar-check"></i>
                    <span>Availability</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/maintenance') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/maintenance">
                    <i class="fas fa-tools"></i>
                    <span>Maintenance</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/coaches') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/coaches">
                    <i class="fas fa-user-tie"></i>
                    <span>Coaches</span>
                    <span class="badge new" id="coachesPendingCount" style="display:none;">0</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/profile') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <!--<li class="<?= strpos($_SERVER['REQUEST_URI'], '/ground-owner/settings') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/ground-owner/settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>-->
            <li>
                <a href="<?= $_base ?>/logout" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>