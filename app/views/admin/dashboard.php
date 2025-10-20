<link rel="stylesheet" href="/public/css/pages/admin-dashboard.css">
<div class="admin-dashboard">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-tachometer-alt"></i>
                <span>GoPlay Admin</span>
            </div>
            <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="active">
                    <a href="/admin/dashboard">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                        <span class="badge">892</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/grounds">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Grounds</span>
                        <span class="badge">45</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/coaches">
                        <i class="fas fa-user-tie"></i>
                        <span>Coaches</span>
                        <span class="badge">23</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/shop">
                        <i class="fas fa-store"></i>
                        <span>Shop</span>
                        <span class="badge">156</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/news">
                        <i class="fas fa-newspaper"></i>
                        <span>Manage News</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/news">
                        <i class="fas fa-newspaper"></i>
                        <span>Manage News</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/payments">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/analytics">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-divider"></li>
                <li>
                    <a href="/admin/settings">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="/logout" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Dashboard Overview</h1>
            </div>
            <div class="header-right">
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">5</span>
                    </button>
                </div>
                <div class="admin-profile">
                    <div class="profile-info">
                        <span class="profile-name">John Smith</span>
                        <span class="profile-role">Super Admin</span>
                    </div>
                    <img src="/public/assets/images/admin-avatar.jpg" alt="Admin" class="profile-avatar">
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
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number">1,247</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>12% from last month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Revenue</h3>
                        <p class="stat-number">Rs.34,567</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% from last month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Users</h3>
                        <p class="stat-number">892</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>15% from last month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Grounds</h3>
                        <p class="stat-number">45</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>3 new this month</span>
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
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Bookings</h3>
                        <a href="/admin/bookings" class="view-all">View All</a>
                    </div>
                    <div class="booking-list">
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Football Ground A</h4>
                                <p>John Doe - 2 hours</p>
                                <span class="booking-time">2 hours ago</span>
                            </div>
                            <span class="status-badge confirmed">Confirmed</span>
                        </div>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Cricket Ground B</h4>
                                <p>Jane Smith - 3 hours</p>
                                <span class="booking-time">4 hours ago</span>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Tennis Court 1</h4>
                                <p>Mike Johnson - 1 hour</p>
                                <span class="booking-time">6 hours ago</span>
                            </div>
                            <span class="status-badge confirmed">Confirmed</span>
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

                <!-- Recent Orders -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                        <a href="/admin/orders" class="view-all">View All</a>
                    </div>
                    <div class="order-list">
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1001</h4>
                                <p>Kavinda Ranasighe</p>
                                <span class="order-amount">₹3,450</span>
                            </div>
                            <span class="status-badge processing">Processing</span>
                        </div>
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1002</h4>
                                <p>Sanduni Rajapakse</p>
                                <span class="order-amount">₹1,850</span>
                            </div>
                            <span class="status-badge completed">Completed</span>
                        </div>
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1003</h4>
                                <p>Dilan Wijesinghe</p>
                                <span class="order-amount">₹2,100</span>
                            </div>
                            <span class="status-badge shipped">Shipped</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="/public/js/pages/admin-dashboard.js"></script>