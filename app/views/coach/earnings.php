<?php
$title = 'Earnings - Coach Dashboard';
$additionalCSS = ['/public/css/pages/coach-earnings.css'];
$additionalJS = ['/public/js/pages/coach-earnings.js'];
?>

<div class="coach-dashboard">
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
                <h1 class="page-title">
                    <i class="fas fa-wallet"></i> Earnings
                </h1>
            </div>
            <!-- <div class="header-right">
                <button class="btn btn-secondary" onclick="earningsManager.exportEarnings()">
                    <i class="fas fa-download"></i> Export Report
                </button>
            </div> -->
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card total-earnings">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Earnings</h3>
                        <p class="stat-number" id="totalEarnings">LKR 0</p>
                        <div class="stat-change">
                            <span id="earningsChange">This month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card pending-payments">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Pending Payments</h3>
                        <p class="stat-number" id="pendingPayments">LKR 0</p>
                        <div class="stat-change">
                            <span id="pendingCount">0 sessions</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card completed-sessions">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Completed Sessions</h3>
                        <p class="stat-number" id="completedSessions">0</p>
                        <div class="stat-change">
                            <span id="completedChange">This month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card avg-rate">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Average Rate</h3>
                        <p class="stat-number" id="avgRate">LKR 0/hr</p>
                        <div class="stat-change">
                            <span id="rateChange">Per session</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card earnings-trend">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-chart-area"></i>
                            Earnings Trend
                        </h2>
                        <div class="period-selector">
                            <button class="period-btn active" data-period="6months">6 Months</button>
                            <button class="period-btn" data-period="12months">12 Months</button>
                            <button class="period-btn" data-period="year">This Year</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="earningsTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-card session-breakdown">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-chart-pie"></i>
                            Earnings by Session Type
                        </h2>
                    </div>
                    <div class="card-body">
                        <canvas id="sessionBreakdownChart"></canvas>
                        <div class="breakdown-legend" id="breakdownLegend"></div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="content-card filters-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-filter"></i>
                        Filter Earnings
                    </h2>
                </div>
                <div class="card-body">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="dateRange">Date Range</label>
                            <select id="dateRange" class="filter-select">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="lastMonth">Last Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        <div class="filter-group" id="customDateGroup" style="display: none;">
                            <label for="startDate">Start Date</label>
                            <input type="date" id="startDate" class="filter-input">
                        </div>

                        <div class="filter-group" id="customDateGroup2" style="display: none;">
                            <label for="endDate">End Date</label>
                            <input type="date" id="endDate" class="filter-input">
                        </div>

                        <div class="filter-group">
                            <label for="sessionType">Session Type</label>
                            <select id="sessionType" class="filter-select">
                                <option value="">All Types</option>
                                <option value="individual">Individual</option>
                                <option value="group">Group</option>
                                <option value="assessment">Assessment</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="paymentStatus">Payment Status</label>
                            <select id="paymentStatus" class="filter-select">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="refunded">Refunded</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <button class="btn btn-primary" onclick="earningsManager.applyFilters()">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings List -->
            <div class="content-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-list"></i>
                        Recent Earnings
                    </h2>
                    <div class="sort-controls">
                        <select id="sortBy" class="filter-select">
                            <option value="date_desc">Latest First</option>
                            <option value="date_asc">Oldest First</option>
                            <option value="amount_desc">Highest Amount</option>
                            <option value="amount_asc">Lowest Amount</option>
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Loading State -->
                    <div id="loadingMessage" class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading earnings...</p>
                    </div>

                    <!-- Earnings Table -->
                    <div class="table-responsive" id="earningsTableContainer" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Session Type</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="earningsTableBody">
                            </tbody>
                        </table>
                    </div>

                    <!-- No Earnings State -->
                    <div id="noEarnings" class="empty-state" style="display: none;">
                        <i class="fas fa-receipt"></i>
                        <h3>No earnings found</h3>
                        <p>No earnings match your current filters.</p>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" id="pagination" style="display: none;">
                        <button class="btn btn-secondary" id="prevPage" onclick="earningsManager.previousPage()">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <span class="page-info" id="pageInfo">Page 1 of 1</span>
                        <button class="btn btn-secondary" id="nextPage" onclick="earningsManager.nextPage()">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Session Details Modal -->
<div id="sessionDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-info-circle"></i>
                Session Details
            </h3>
            <button class="modal-close" onclick="earningsManager.closeModal('sessionDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="sessionDetailsContent">
            <!-- Content loaded dynamically -->
        </div>
    </div>
</div>
