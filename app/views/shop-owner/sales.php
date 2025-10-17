<?php
$title = 'Sales Analytics - Shop Owner Dashboard';
$additionalCSS = ['/public/css/pages/shop-owner-sales.css'];
$additionalJS = [
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
    '/public/js/pages/shop-owner-sales.js'
];
?>
<link rel="stylesheet" href="/public/css/pages/shop-owner-sales.css">
<div class="shop-owner-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Sales Analytics</h1>
            </div>
            <div class="header-right">
                <div class="date-range-selector">
                    <button class="date-range-btn active" data-range="7">7 Days</button>
                    <button class="date-range-btn" data-range="30">30 Days</button>
                    <button class="date-range-btn" data-range="90">90 Days</button>
                    <button class="date-range-btn" data-range="365">1 Year</button>
                </div>
                <button class="btn-export">
                    <i class="fas fa-download"></i>
                    Export Report
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content sales-analytics">
            <!-- Overview Stats -->
            <div class="overview-stats">
                <div class="stat-card revenue">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3>Total Revenue</h3>
                    </div>
                    <div class="stat-body">
                        <p class="stat-value">LKR 3,45,780</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+24.5% vs last period</span>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-detail">Target: LKR 4,00,000</span>
                        <div class="progress-mini">
                            <div class="progress-fill" style="width: 86.4%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card orders">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h3>Total Orders</h3>
                    </div>
                    <div class="stat-body">
                        <p class="stat-value">1,247</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+18.2% vs last period</span>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-detail">Avg: 41.5 orders/day</span>
                    </div>
                </div>

                <div class="stat-card conversion">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Conversion Rate</h3>
                    </div>
                    <div class="stat-body">
                        <p class="stat-value">3.8%</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+0.5% vs last period</span>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-detail">Industry avg: 2.5%</span>
                    </div>
                </div>

                <div class="stat-card avg-order">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h3>Avg Order Value</h3>
                    </div>
                    <div class="stat-body">
                        <p class="stat-value">LKR 2,773</p>
                        <div class="stat-trend negative">
                            <i class="fas fa-arrow-down"></i>
                            <span>-2.1% vs last period</span>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-detail">Previous: LKR 2,832</span>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <!-- Revenue Trend -->
                <div class="chart-card large">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Revenue Trend</h3>
                            <p class="card-subtitle">Daily revenue over time</p>
                        </div>
                        <div class="header-actions">
                            <select class="chart-period-select">
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot revenue"></span>
                            <span>Current Period</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot previous"></span>
                            <span>Previous Period</span>
                        </div>
                    </div>
                </div>

                <!-- Sales by Category -->
                <div class="chart-card medium">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Sales by Category</h3>
                            <p class="card-subtitle">Product category breakdown</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="category-stats">
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-color cricket"></span>
                                <span class="category-name">Cricket</span>
                            </div>
                            <span class="category-value">LKR 1,24,560 (36%)</span>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-color football"></span>
                                <span class="category-name">Football</span>
                            </div>
                            <span class="category-value">LKR 98,230 (28%)</span>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-color tennis"></span>
                                <span class="category-name">Tennis</span>
                            </div>
                            <span class="category-value">LKR 67,450 (20%)</span>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-color other"></span>
                                <span class="category-name">Others</span>
                            </div>
                            <span class="category-value">LKR 55,540 (16%)</span>
                        </div>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="chart-card medium">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Order Status</h3>
                            <p class="card-subtitle">Current order distribution</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="status-stats">
                        <div class="status-item">
                            <span class="status-badge delivered">Delivered</span>
                            <span class="status-count">856 (68.6%)</span>
                        </div>
                        <div class="status-item">
                            <span class="status-badge shipped">Shipped</span>
                            <span class="status-count">234 (18.8%)</span>
                        </div>
                        <div class="status-item">
                            <span class="status-badge processing">Processing</span>
                            <span class="status-count">112 (9.0%)</span>
                        </div>
                        <div class="status-item">
                            <span class="status-badge cancelled">Cancelled</span>
                            <span class="status-count">45 (3.6%)</span>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Products -->
                <div class="chart-card large">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Top Selling Products</h3>
                            <p class="card-subtitle">Products ranked by revenue</p>
                        </div>
                        <a href="/shop-owner/products" class="view-all-link">View All Products →</a>
                    </div>
                    <div class="products-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="rank-badge gold">1</span></td>
                                    <td>
                                        <div class="product-cell">
                                            <img src="/public/assets/images/products/cricket-bat-pro.jpg" alt="Cricket Bat">
                                            <span>Cricket Bat - Professional Grade</span>
                                        </div>
                                    </td>
                                    <td><span class="category-tag cricket">Cricket</span></td>
                                    <td>156 units</td>
                                    <td><strong>LKR 54,600</strong></td>
                                    <td>
                                        <div class="trend-indicator up">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>+12%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="rank-badge silver">2</span></td>
                                    <td>
                                        <div class="product-cell">
                                            <img src="/public/assets/images/products/football-official.jpg" alt="Football">
                                            <span>Football - Official Match Ball</span>
                                        </div>
                                    </td>
                                    <td><span class="category-tag football">Football</span></td>
                                    <td>234 units</td>
                                    <td><strong>LKR 46,800</strong></td>
                                    <td>
                                        <div class="trend-indicator up">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>+8%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="rank-badge bronze">3</span></td>
                                    <td>
                                        <div class="product-cell">
                                            <img src="/public/assets/images/products/tennis-racket.jpg" alt="Tennis Racket">
                                            <span>Tennis Racket - Carbon Fiber</span>
                                        </div>
                                    </td>
                                    <td><span class="category-tag tennis">Tennis</span></td>
                                    <td>89 units</td>
                                    <td><strong>LKR 37,380</strong></td>
                                    <td>
                                        <div class="trend-indicator up">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>+15%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="rank-badge">4</span></td>
                                    <td>
                                        <div class="product-cell">
                                            <img src="/public/assets/images/products/cricket-ball.jpg" alt="Cricket Ball">
                                            <span>Cricket Ball - Leather (Pack of 6)</span>
                                        </div>
                                    </td>
                                    <td><span class="category-tag cricket">Cricket</span></td>
                                    <td>178 units</td>
                                    <td><strong>LKR 26,700</strong></td>
                                    <td>
                                        <div class="trend-indicator down">
                                            <i class="fas fa-arrow-down"></i>
                                            <span>-3%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="rank-badge">5</span></td>
                                    <td>
                                        <div class="product-cell">
                                            <img src="/public/assets/images/products/badminton-set.jpg" alt="Badminton Set">
                                            <span>Badminton Racket Set (2 Rackets)</span>
                                        </div>
                                    </td>
                                    <td><span class="category-tag badminton">Badminton</span></td>
                                    <td>145 units</td>
                                    <td><strong>LKR 21,750</strong></td>
                                    <td>
                                        <div class="trend-indicator up">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>+5%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Customer Demographics -->
                <div class="chart-card medium">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Customer Demographics</h3>
                            <p class="card-subtitle">Age group distribution</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="demographicsChart"></canvas>
                    </div>
                </div>

                <!-- Sales Performance by Hour -->
                <div class="chart-card large">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Sales by Time of Day</h3>
                            <p class="card-subtitle">Hourly sales distribution</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>

                <!-- Regional Sales Map -->
                <div class="chart-card large">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Sales by Region</h3>
                            <p class="card-subtitle">Geographic distribution of sales</p>
                        </div>
                    </div>
                    <div class="regional-stats">
                        <div class="region-item">
                            <div class="region-header">
                                <h4>Western Province</h4>
                                <span class="region-percentage">42%</span>
                            </div>
                            <div class="region-bar">
                                <div class="region-fill" style="width: 42%"></div>
                            </div>
                            <div class="region-details">
                                <span>LKR 1,45,227 • 524 orders</span>
                            </div>
                        </div>
                        <div class="region-item">
                            <div class="region-header">
                                <h4>Central Province</h4>
                                <span class="region-percentage">23%</span>
                            </div>
                            <div class="region-bar">
                                <div class="region-fill" style="width: 23%"></div>
                            </div>
                            <div class="region-details">
                                <span>LKR 79,529 • 287 orders</span>
                            </div>
                        </div>
                        <div class="region-item">
                            <div class="region-header">
                                <h4>Southern Province</h4>
                                <span class="region-percentage">18%</span>
                            </div>
                            <div class="region-bar">
                                <div class="region-fill" style="width: 18%"></div>
                            </div>
                            <div class="region-details">
                                <span>LKR 62,240 • 224 orders</span>
                            </div>
                        </div>
                        <div class="region-item">
                            <div class="region-header">
                                <h4>Other Provinces</h4>
                                <span class="region-percentage">17%</span>
                            </div>
                            <div class="region-bar">
                                <div class="region-fill" style="width: 17%"></div>
                            </div>
                            <div class="region-details">
                                <span>LKR 58,784 • 212 orders</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="chart-card medium">
                    <div class="card-header">
                        <div class="header-left">
                            <h3>Payment Methods</h3>
                            <p class="card-subtitle">Preferred payment options</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="paymentChart"></canvas>
                    </div>
                    <div class="payment-stats">
                        <div class="payment-item">
                            <i class="fas fa-credit-card"></i>
                            <span class="payment-name">Credit/Debit Card</span>
                            <span class="payment-value">58%</span>
                        </div>
                        <div class="payment-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <span class="payment-name">Cash on Delivery</span>
                            <span class="payment-value">28%</span>
                        </div>
                        <div class="payment-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span class="payment-name">Mobile Wallet</span>
                            <span class="payment-value">14%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="insights-section">
                <div class="section-header">
                    <h2>Performance Insights</h2>
                    <p>AI-powered recommendations to improve your sales</p>
                </div>
                <div class="insights-grid">
                    <div class="insight-card positive">
                        <div class="insight-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="insight-content">
                            <h4>Strong Growth Trend</h4>
                            <p>Your sales have increased by 24.5% this month. Cricket equipment is performing exceptionally well.</p>
                        </div>
                    </div>
                    <div class="insight-card warning">
                        <div class="insight-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="insight-content">
                            <h4>Peak Hour Opportunity</h4>
                            <p>Sales spike between 6 PM - 9 PM. Consider running flash sales during these hours.</p>
                        </div>
                    </div>
                    <div class="insight-card info">
                        <div class="insight-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="insight-content">
                            <h4>Customer Retention</h4>
                            <p>32% of customers are repeat buyers. Implement a loyalty program to increase this further.</p>
                        </div>
                    </div>
                    <div class="insight-card negative">
                        <div class="insight-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="insight-content">
                            <h4>Average Order Value Dip</h4>
                            <p>AOV decreased by 2.1%. Consider product bundling or upselling strategies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Load sales analytics JavaScript -->
<script src="/public/js/pages/shop-owner-sales.js"></script>