<link rel="stylesheet" href="/public/css/pages/admin-dashboard.css">
<div class="admin-dashboard">
    <?php 
    // Set active page for sidebar
    $activePage = 'dashboard';
    
    // Include the reusable sidebar component
    include __DIR__ . '/../components/admin-sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">Dashboard Overview</h1>
            </div>
            <div class="header-right">
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count" style="display: none;">0</span>
                    </button>
                </div>
                <div class="admin-profile">
                    <div class="profile-info">
                        <span class="profile-name">Loading...</span>
                        <span class="profile-role">Super Admin</span>
                    </div>
                    <img src="/public/assets/images/default-avatar.png" alt="Admin" class="profile-avatar">
                    <button class="profile-dropdown">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Users</h3>
                        <p class="stat-number">0</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Revenue</h3>
                        <p class="stat-number">Rs.0</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Grounds</h3>
                        <p class="stat-number">0</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Coaches</h3>
                        <p class="stat-number">0</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Revenue Chart -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Revenue Trend</h3>
                        <div class="card-actions">
                            <select class="time-filter">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Registrations</h3>
                        <a href="/admin/users" class="view-all">View All</a>
                    </div>
                    <div class="booking-list">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading registrations...</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="quick-actions">
                        <a href="/admin/grounds/create" class="action-btn blue">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Ground</span>
                        </a>
                        <a href="/admin/coaches/create" class="action-btn green">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Coach</span>
                        </a>
                        <a href="/admin/products/create" class="action-btn purple">
                            <i class="fas fa-box"></i>
                            <span>Add Product</span>
                        </a>
                        <a href="/admin/news/create" class="action-btn orange">
                            <i class="fas fa-newspaper"></i>
                            <span>Add News</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Content Additions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Content</h3>
                        <a href="/admin/content" class="view-all">View All</a>
                    </div>
                    <div class="order-list">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading content...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: #64748b;
    gap: 0.5rem;
}

.loading-state i {
    font-size: 2rem;
    color: #3b82f6;
}

.empty-state {
    padding: 2rem;
    text-align: center;
    color: #64748b;
}

.stat-change.negative {
    color: #ef4444;
}

.stat-change.negative i {
    color: #ef4444;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.user {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.admin {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.ground-owner {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.coach {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.shop-owner {
    background: #e9d5ff;
    color: #6b21a8;
}

.status-badge.ground {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.product {
    background: #e9d5ff;
    color: #6b21a8;
}

.booking-item h4 i,
.order-item h4 i {
    margin-right: 0.5rem;
    font-size: 0.875rem;
}
</style>

<script src="/public/js/pages/admin-dashboard.js"></script>
<script>
// Load pending applications count
async function loadPendingApplicationsCount() {
    try {
        const response = await fetch('/admin/provider-applications/statistics');
        const data = await response.json();

        if (data.success && data.stats) {
            const pendingCount = data.stats.pending || 0;
            const badge = document.querySelector('.pending-applications');
            if (badge) {
                badge.textContent = pendingCount;
                if (pendingCount > 0) {
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Error loading pending applications count:', error);
    }
}

// Load count on page load
document.addEventListener('DOMContentLoaded', loadPendingApplicationsCount);
</script>