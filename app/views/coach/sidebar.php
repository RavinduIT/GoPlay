<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-whistle"></i>
            <span>Coach Panel</span>
        </div>
        <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/dashboard') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/profile') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/profile">
                    <i class="fas fa-user-edit"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/sessions') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/sessions">
                    <i class="fas fa-dumbbell"></i>
                    <span>Training Sessions</span>
                    <span class="badge" id="sessionsCount" style="display:none">0</span>
                </a>
            </li>
            <!-- <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/book-session') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/book-session">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Book Session</span>
                </a>
            </li> -->
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/clients') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/clients">
                    <i class="fas fa-users"></i>
                    <span>My Clients</span>
                    <span class="badge" id="clientsCount" style="display:none">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/earnings') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/earnings">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/availability') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/availability">
                    <i class="fas fa-clock"></i>
                    <span>Availability</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/reviews') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                    <span class="badge" id="reviewsCount" style="display:none">0</span>
                </a>
            </li>
            <!-- <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/notifications') !== false ? 'active' : '' ?>">
                <a href="<?= $_base ?>/coach/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <span class="badge new" id="notificationCount">0</span>
                </a>
            </li> -->
            <li class="nav-divider"></li>
            <li>
                <a href="<?= $_base ?>/logout" class="logout-link" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
<script>
// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.coach-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Save preference
            localStorage.setItem('coachSidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Load saved preference
        const isCollapsed = localStorage.getItem('coachSidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }
    }
    
    // Load sidebar stats
    loadSidebarStats();
    
    // Update badges periodically
    setInterval(updateNotificationBadges, 30000); // Every 30 seconds
});

async function loadSidebarStats() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/coach/sidebar-stats');
        if (!response.ok) return;
        const stats = await response.json();
        if (stats.error) return;

        // Helper: set badge text and show/hide based on count
        const setBadge = (id, val) => {
            const el = document.getElementById(id);
            if (!el) return;
            const count = parseInt(val) || 0;
            el.textContent = count;
            el.style.display = count > 0 ? 'inline-flex' : 'none';
        };

        // Map API response to sidebar badge elements
        setBadge('sessionsCount',  stats.upcomingSessions || 0);   // Training Sessions badge
        setBadge('clientsCount',   stats.totalClients || 0);       // My Clients badge
        setBadge('reviewsCount',   stats.totalReviews || 0);       // Reviews badge
    } catch (error) {
        // Sidebar stats are non-critical; fail silently
    }
}

async function updateNotificationBadges() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/coach/notifications/count');
        const counts = await response.json();
        
        const notificationBadge = document.getElementById('notificationCount');
        if (notificationBadge) {
            notificationBadge.textContent = counts.unread || 0;
            notificationBadge.style.display = counts.unread > 0 ? 'inline' : 'none';
        }
    } catch (error) {
        console.error('Error updating notification badges:', error);
    }
}
</script>