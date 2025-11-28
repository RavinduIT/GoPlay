<?php 
$title = 'Earnings Analytics - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-earnings.css'
];
$additionalJS = [
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
    '/public/js/pages/ground-owner-earnings.js'
];
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
                <h1 class="page-title">Earnings Analytics</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <!--<button class="btn-secondary" onclick="generateReport()">
                        <i class="fas fa-file-pdf"></i>
                        Generate Report
                    </button>-->
                    <select id="timeRange" class="filter-select">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
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

        <!-- Earnings Content -->
        <div class="dashboard-content">
            <!-- Earnings Overview Cards -->
            <div class="earnings-overview">
                <div class="earning-card total-revenue">
                    <div class="card-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="card-content">
                        <h3>Total Revenue</h3>
                        <p class="earning-amount" id="totalRevenue">LKR 0</p>
                        <div class="earning-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="revenueChange">0%</span>
                            <small>vs last period</small>
                        </div>
                    </div>
                </div>

                <div class="earning-card monthly-earnings">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3>This Month</h3>
                        <p class="earning-amount" id="monthlyEarnings">LKR 0</p>
                        <div class="earning-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="monthlyChange">0%</span>
                            <small>vs last month</small>
                        </div>
                    </div>
                </div>

                <div class="earning-card average-booking">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h3>Avg. per Booking</h3>
                        <p class="earning-amount" id="averageBooking">LKR 0</p>
                        <div class="earning-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="avgChange">0%</span>
                            <small>vs last period</small>
                        </div>
                    </div>
                </div>

                <div class="earning-card pending-payments">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3>Pending Payments</h3>
                        <p class="earning-amount" id="pendingPayments">LKR 0</p>
                        <div class="pending-count">
                            <span id="pendingCount">0</span>
                            <small>transactions</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-container revenue-chart">
                    <div class="chart-header">
                        <h3>Revenue Trends</h3>
                        <div class="chart-controls">
                            <button class="chart-type-btn active" data-type="line">Line</button>
                            <button class="chart-type-btn" data-type="bar">Bar</button>
                            <button class="chart-type-btn" data-type="area">Area</button>
                        </div>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="chart-container grounds-performance">
                    <div class="chart-header">
                        <h3>Ground Performance</h3>
                        <select id="performanceMetric" class="metric-select">
                            <option value="revenue">Revenue</option>
                            <option value="bookings">Bookings</option>
                            <option value="occupancy">Occupancy</option>
                        </select>
                    </div>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Detailed Analytics -->
            <div class="analytics-grid">
                <!-- Earnings Breakdown -->
                <div class="analytics-card earnings-breakdown">
                    <div class="card-header">
                        <h3>Earnings Breakdown</h3>
                        <select id="breakdownPeriod" class="period-select">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                        </select>
                    </div>
                    <div class="breakdown-list" id="earningsBreakdown">
                        <!-- Dynamic content -->
                    </div>
                </div>

                <!-- Top Performing Grounds -->
                <div class="analytics-card top-grounds">
                    <div class="card-header">
                        <h3>Top Performing Grounds</h3>
                        <span class="period-indicator">This Month</span>
                    </div>
                    <div class="grounds-ranking" id="topGrounds">
                        <!-- Dynamic content -->
                    </div>
                </div>

                <!-- Revenue Sources -->
                <div class="analytics-card revenue-sources">
                    <div class="card-header">
                        <h3>Revenue Sources</h3>
                    </div>
                    <canvas id="revenueSourceChart"></canvas>
                    <div class="revenue-legend" id="revenueLegend">
                        <!-- Dynamic legend -->
                    </div>
                </div>

                <!-- Payment Analytics -->
                <div class="analytics-card payment-analytics">
                    <div class="card-header">
                        <h3>Payment Analytics</h3>
                    </div>
                    <div class="payment-metrics">
                        <div class="metric-item">
                            <div class="metric-label">Success Rate</div>
                            <div class="metric-value" id="paymentSuccessRate">0%</div>
                            <div class="metric-bar">
                                <div class="metric-fill" id="paymentSuccessBar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">Avg. Processing Time</div>
                            <div class="metric-value" id="avgProcessingTime">0s</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">Failed Payments</div>
                            <div class="metric-value" id="failedPayments">0</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">Refunds Issued</div>
                            <div class="metric-value" id="refundsIssued">LKR 0</div>
                        </div>
                    </div>
                </div>

                <!-- Booking Patterns -->
                <div class="analytics-card booking-patterns">
                    <div class="card-header">
                        <h3>Booking Patterns</h3>
                    </div>
                    <div class="patterns-grid">
                        <div class="pattern-item">
                            <h4>Peak Hours</h4>
                            <div class="hours-chart" id="peakHours">
                                <!-- Dynamic hourly breakdown -->
                            </div>
                        </div>
                        <div class="pattern-item">
                            <h4>Weekly Trends</h4>
                            <div class="days-chart" id="weeklyTrends">
                                <!-- Dynamic daily breakdown -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="analytics-card financial-summary">
                    <div class="card-header">
                        <h3>Financial Summary</h3>
                        <button class="btn-primary" onclick="exportFinancials()">Export</button>
                    </div>
                    <div class="financial-items">
                        <div class="financial-item">
                            <span class="label">Gross Revenue</span>
                            <span class="amount positive" id="grossRevenue">LKR 0</span>
                        </div>
                        <div class="financial-item">
                            <span class="label">Platform Commission</span>
                            <span class="amount negative" id="platformCommission">-LKR 0</span>
                        </div>
                        <div class="financial-item">
                            <span class="label">Payment Processing</span>
                            <span class="amount negative" id="processingFees">-LKR 0</span>
                        </div>
                        <div class="financial-item">
                            <span class="label">Taxes</span>
                            <span class="amount negative" id="taxes">-LKR 0</span>
                        </div>
                        <div class="financial-item total">
                            <span class="label">Net Earnings</span>
                            <span class="amount positive" id="netEarnings">LKR 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="transactions-section">
                <div class="section-header">
                    <h3>Recent Transactions</h3>
                    <div class="section-actions">
                        <button class="btn-secondary" onclick="viewAllTransactions()">View All</button>
                        <select id="transactionFilter" class="filter-select">
                            <option value="all">All Transactions</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                <div class="transactions-table">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Ground</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Fee</th>
                                <th>Net</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Custom Date Range Modal
    <div id="dateRangeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Custom Date Range</h3>
                <button class="modal-close" onclick="closeDateRangeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="date-inputs">
                    <div class="input-group">
                        <label for="customStartDate">Start Date</label>
                        <input type="date" id="customStartDate" class="date-input">
                    </div>
                    <div class="input-group">
                        <label for="customEndDate">End Date</label>
                        <input type="date" id="customEndDate" class="date-input">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDateRangeModal()">Cancel</button>
                <button type="button" class="btn-primary" onclick="applyDateRange()">Apply Range</button>
            </div>
        </div>
    </div> -->
</div>