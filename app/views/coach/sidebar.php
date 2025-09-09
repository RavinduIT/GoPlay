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
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/clients') !== false ? 'active' : '' ?>">
                <a href="/coach/clients">
                    <i class="fas fa-users"></i>
                    <span>My Clients</span>
                    <span class="badge" id="clientsCount">0</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/programs') !== false ? 'active' : '' ?>">
                <a href="/coach/programs">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Training Programs</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/assessments') !== false ? 'active' : '' ?>">
                <a href="/coach/assessments">
                    <i class="fas fa-chart-line"></i>
                    <span>Assessments</span>
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
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/notifications') !== false ? 'active' : '' ?>">
                <a href="/coach/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <span class="badge new" id="notificationCount">0</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/coach/settings') !== false ? 'active' : '' ?>">
                <a href="/coach/settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
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
        
        document.getElementById('activeClients').textContent = stats.activeClients || 0;
        document.getElementById('monthSessions').textContent = stats.monthSessions || 0;
        document.getElementById('scheduleCount').textContent = stats.upcomingSchedule || 0;
        document.getElementById('clientsCount').textContent = stats.totalClients || 0;
        document.getElementById('reviewsCount').textContent = stats.pendingReviews || 0;
    } catch (error) {
        console.error('Error loading sidebar stats:', error);
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