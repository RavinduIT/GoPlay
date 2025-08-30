<?php 
$title = 'Ground Owner Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/ground-owner-dashboard.css'];
$additionalJS = ['/public/js/pages/ground-owner-dashboard.js'];
?>

<div class="ground-owner-dashboard">
    <!-- Include Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
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
                        <span class="notification-count">3</span>
                    </button>
                </div>
                <div class="owner-profile">
                    <div class="profile-info">
                        <span class="profile-name">Rajesh Perera</span>
                        <span class="profile-role">Ground Owner</span>
                    </div>
                    <img src="/public/assets/images/owner-avatar.jpg" alt="Owner" class="profile-avatar">
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
                <div class="stat-card earnings">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Earnings</h3>
                        <p class="stat-number">Rs.45,670</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>15% this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card bookings">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number">156</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card occupancy">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Occupancy Rate</h3>
                        <p class="stat-number">78%</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>5% this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card rating">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Average Rating</h3>
                        <p class="stat-number">4.8</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Based on 23 reviews</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Earnings Chart -->
                <div class="dashboard-card earnings-chart">
                    <div class="card-header">
                        <h3>Earnings Overview</h3>
                        <div class="card-actions">
                            <select class="time-filter" id="earningsFilter">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="dashboard-card recent-bookings">
                    <div class="card-header">
                        <h3>Recent Bookings</h3>
                        <a href="/ground-owner/bookings" class="view-all">View All</a>
                    </div>
                    <div class="booking-list">
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Football Ground A</h4>
                                <p>Kavinda Ranasighe</p>
                                <span class="booking-details">Today, 3:00 PM - 5:00 PM</span>
                                <span class="booking-amount">₹1,200</span>
                            </div>
                            <span class="status-badge confirmed">Confirmed</span>
                        </div>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Cricket Ground B</h4>
                                <p>Sanduni Rajapakse</p>
                                <span class="booking-details">Tomorrow, 10:00 AM - 1:00 PM</span>
                                <span class="booking-amount">₹1,800</span>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>Tennis Court 1</h4>
                                <p>Dilan Wijesinghe</p>
                                <span class="booking-details">Dec 25, 4:00 PM - 5:00 PM</span>
                                <span class="booking-amount">₹600</span>
                            </div>
                            <span class="status-badge confirmed">Confirmed</span>
                        </div>
                    </div>
                </div>

                <!-- Ground Performance -->
                <div class="dashboard-card ground-performance">
                    <div class="card-header">
                        <h3>Ground Performance</h3>
                        <div class="performance-period">This Month</div>
                    </div>
                    <div class="performance-list">
                        <div class="performance-item">
                            <div class="ground-info">
                                <h4>Football Ground A</h4>
                                <div class="performance-stats">
                                    <span class="bookings">68 bookings</span>
                                    <span class="earnings">₹20,400</span>
                                </div>
                            </div>
                            <div class="performance-chart">
                                <div class="chart-bar" style="width: 85%"></div>
                                <span class="performance-rate">85%</span>
                            </div>
                        </div>
                        <div class="performance-item">
                            <div class="ground-info">
                                <h4>Cricket Ground B</h4>
                                <div class="performance-stats">
                                    <span class="bookings">45 bookings</span>
                                    <span class="earnings">₹13,500</span>
                                </div>
                            </div>
                            <div class="performance-chart">
                                <div class="chart-bar" style="width: 72%"></div>
                                <span class="performance-rate">72%</span>
                            </div>
                        </div>
                        <div class="performance-item">
                            <div class="ground-info">
                                <h4>Tennis Court 1</h4>
                                <div class="performance-stats">
                                    <span class="bookings">43 bookings</span>
                                    <span class="earnings">₹11,770</span>
                                </div>
                            </div>
                            <div class="performance-chart">
                                <div class="chart-bar" style="width: 78%"></div>
                                <span class="performance-rate">78%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card quick-actions">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="/ground-owner/grounds/create" class="action-btn primary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add New Ground</span>
                        </a>
                        <a href="/ground-owner/availability" class="action-btn secondary">
                            <i class="fas fa-clock"></i>
                            <span>Update Availability</span>
                        </a>
                        <a href="/ground-owner/pricing" class="action-btn accent">
                            <i class="fas fa-tags"></i>
                            <span>Manage Pricing</span>
                        </a>
                        <a href="/ground-owner/reports" class="action-btn success">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card recent-reviews">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                        <a href="/ground-owner/reviews" class="view-all">View All</a>
                    </div>
                    <div class="reviews-list">
                        <div class="review-item">
                            <div class="reviewer-info">
                                <img src="/public/assets/images/user1.jpg" alt="User" class="reviewer-avatar">
                                <div class="reviewer-details">
                                    <h5>Kavinda Ranasighe</h5>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">"Excellent football ground with good facilities. Well maintained and clean."</p>
                            <span class="review-time">2 hours ago</span>
                        </div>
                        <div class="review-item">
                            <div class="reviewer-info">
                                <img src="/public/assets/images/user2.jpg" alt="User" class="reviewer-avatar">
                                <div class="reviewer-details">
                                    <h5>Sanduni Rajapakse</h5>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">"Great cricket ground! Good pitch and facilities. Will book again."</p>
                            <span class="review-time">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>