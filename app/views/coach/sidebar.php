<link rel="stylesheet" href="/public/css/coach/sidebar.css">
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
                <a href="/coach/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/profile') !== false ? 'active' : '' ?>">
                <a href="/coach/profile">
                    <i class="fas fa-user-edit"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/sessions') !== false ? 'active' : '' ?>">
                <a href="/coach/sessions">
                    <i class="fas fa-dumbbell"></i>
                    <span>Training Sessions</span>
                    <span class="badge" id="sessionsCount">0</span>
                </a>
            </li>
            <!-- <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/book-session') !== false ? 'active' : '' ?>">
                <a href="/coach/book-session">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Book Session</span>
                </a>
            </li> -->
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/clients') !== false ? 'active' : '' ?>">
                <a href="/coach/clients">
                    <i class="fas fa-users"></i>
                    <span>My Clients</span>
                    <span class="badge" id="clientsCount">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/earnings') !== false ? 'active' : '' ?>">
                <a href="/coach/earnings">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/availability') !== false ? 'active' : '' ?>">
                <a href="/coach/availability">
                    <i class="fas fa-clock"></i>
                    <span>Availability</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/reviews') !== false ? 'active' : '' ?>">
                <a href="/coach/reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                    <span class="badge" id="reviewsCount">0</span>
                </a>
            </li>
            <!-- <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/notifications') !== false ? 'active' : '' ?>">
                <a href="/coach/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <span class="badge new" id="notificationCount">0</span>
                </a>
            </li> -->
            <li class="nav-divider"></li>
            <li>
                <a href="/logout" class="logout-link" onclick="return confirm('Are you sure you want to logout?')">
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
        const response = await fetch('/api/coach/sidebar-stats');
        const stats = await response.json();

        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        set('activeClients', stats.activeClients || 0);
        set('monthSessions', stats.monthSessions || 0);
        set('scheduleCount', stats.upcomingSchedule || 0);
        set('clientsCount',  stats.totalClients || 0);
        set('reviewsCount',  stats.pendingReviews || 0);
    } catch (error) {
        // Sidebar stats are non-critical; fail silently
    }
}

async function updateNotificationBadges() {
    try {
        const response = await fetch('/api/coach/notifications/count');
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