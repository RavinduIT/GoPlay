<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-analytics.css">
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-dashboard.css">
<div class="admin-dashboard">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">Analytics & Reports</h1>
            </div>
            <div class="header-right">
                <select class="time-range-selector" id="timeRange">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
                <button class="export-btn" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Export Report
                </button>
            </div>
        </header>

        <!-- Analytics Content -->
        <div class="analytics-content">
            <!-- Overview Stats -->
            <div class="stats-grid" id="overviewStats">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Users</h3>
                        <p class="stat-number" id="totalUsers">-</p>
                        <div class="stat-change positive" id="userGrowth">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Revenue</h3>
                        <p class="stat-number" id="totalRevenue">-</p>
                        <div class="stat-change positive" id="revenueGrowth">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number" id="totalBookings">-</p>
                        <div class="stat-change positive" id="bookingGrowth">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-number" id="totalOrders">-</p>
                        <div class="stat-change positive" id="orderGrowth">
                            <i class="fas fa-arrow-up"></i>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Revenue Trends</h3>
                        <div class="card-actions">
                            <button class="icon-btn" onclick="refreshChart('revenue')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- User Distribution -->
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> User Distribution</h3>
                        <div class="card-actions">
                            <button class="icon-btn" onclick="refreshChart('users')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Booking Analytics -->
                <div class="chart-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> Booking Analytics by Sport</h3>
                        <div class="card-actions">
                            <button class="icon-btn" onclick="refreshChart('bookings')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="bookingChart"></canvas>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-trophy"></i> Top Selling Products</h3>
                        <a href="<?= $_base ?>/admin/products" class="view-all">View All</a>
                    </div>
                    <div class="table-container">
                        <table class="data-table" id="topProductsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="loading-cell">Loading data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Recent Activity</h3>
                        <button class="icon-btn" onclick="refreshActivity()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="activity-list" id="activityList">
                        <div class="loading-cell">Loading activities...</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports Section -->
            <div class="reports-section">
                <h2>Detailed Reports</h2>
                <div class="reports-grid">
                    <div class="report-card" onclick="generateReport('users')">
                        <div class="report-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>User Report</h4>
                        <p>Complete user statistics and demographics</p>
                        <button class="report-btn">Generate Report</button>
                    </div>

                    <div class="report-card" onclick="generateReport('bookings')">
                        <div class="report-icon green">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <h4>Booking Report</h4>
                        <p>All bookings with detailed information</p>
                        <button class="report-btn">Generate Report</button>
                    </div>

                    <div class="report-card" onclick="generateReport('revenue')">
                        <div class="report-icon purple">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h4>Revenue Report</h4>
                        <p>Financial overview and transactions</p>
                        <button class="report-btn">Generate Report</button>
                    </div>

                    <div class="report-card" onclick="generateReport('products')">
                        <div class="report-icon orange">
                            <i class="fas fa-box"></i>
                        </div>
                        <h4>Product Report</h4>
                        <p>Product performance and inventory</p>
                        <button class="report-btn">Generate Report</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= $_base ?>/public/js/pages/admin-analytics.js"></script>