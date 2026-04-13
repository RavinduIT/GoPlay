<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Dashboard - GoPlay Ground Owner';
$additionalCSS = ['/public/css/pages/ground-owner-dashboard.css'];
$additionalJS = [
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
    '/public/js/pages/ground-owner-dashboard.js'
];

?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="header-right">
            </div>
        </header>

        <div class="dashboard-content">

            <!-- Stats Row -->
            <div class="stats-grid">
                <div class="stat-card earnings">
                    <div class="stat-icon si-green"><i class="fas fa-coins"></i></div>
                    <div class="stat-content">
                        <h3>Monthly Earnings</h3>
                        <p class="stat-number" id="dbEarnings">—</p>
                        <div class="stat-change" id="dbEarningsChange"><span>Loading…</span></div>
                    </div>
                </div>
                <div class="stat-card bookings">
                    <div class="stat-icon si-blue"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number" id="dbBookings">—</p>
                        <div class="stat-change" id="dbBookingsChange"><span>Loading…</span></div>
                    </div>
                </div>
                <div class="stat-card occupancy">
                    <div class="stat-icon si-purple"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="stat-content">
                        <h3>Active Grounds</h3>
                        <p class="stat-number" id="dbGrounds">—</p>
                        <div class="stat-change" id="dbGroundsChange"><span>Loading…</span></div>
                    </div>
                </div>
                <div class="stat-card rating">
                    <div class="stat-icon si-amber"><i class="fas fa-star"></i></div>
                    <div class="stat-content">
                        <h3>Avg Rating</h3>
                        <p class="stat-number" id="dbRating">—</p>
                        <div class="stat-change" id="dbRatingChange"><span>Loading…</span></div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">

                <!-- Earnings Chart -->
                <div class="dashboard-card earnings-chart">
                    <div class="card-header">
                        <h3>Earnings Overview</h3>
                        <select id="dbChartPeriod" class="filter-select">
                            <option value="7">7 Days</option>
                            <option value="30" selected>30 Days</option>
                            <option value="90">90 Days</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="dbEarningsChart"></canvas>
                    </div>
                </div>

                <!-- Today's Bookings -->
                <div class="dashboard-card recent-bookings">
                    <div class="card-header">
                        <h3>Today's Bookings</h3>
                        <a href="<?= $_base ?>/ground-owner/bookings" class="view-all">View All</a>
                    </div>
                    <div class="booking-list" id="dbTodayBookings">
                        <div class="db-loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    </div>
                </div>

                <!-- Ground Performance -->
                <div class="dashboard-card ground-performance">
                    <div class="card-header">
                        <h3>Ground Performance</h3>
                        <span class="performance-period">This Month</span>
                    </div>
                    <div class="performance-list" id="dbPerformance">
                        <div class="db-loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card quick-actions">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="<?= $_base ?>/ground-owner/grounds" class="action-btn primary">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>My Grounds</span>
                        </a>
                        <a href="<?= $_base ?>/ground-owner/bookings" class="action-btn secondary">
                            <i class="fas fa-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                        <a href="<?= $_base ?>/ground-owner/schedule" class="action-btn accent">
                            <i class="fas fa-clock"></i>
                            <span>Schedule</span>
                        </a>
                        <a href="<?= $_base ?>/ground-owner/maintenance" class="action-btn success">
                            <i class="fas fa-tools"></i>
                            <span>Maintenance</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card recent-reviews">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                        <a href="<?= $_base ?>/ground-owner/reviews" class="view-all">View All</a>
                    </div>
                    <div class="reviews-list" id="dbReviews">
                        <div class="db-loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    </div>
                </div>

            </div><!-- /dashboard-grid -->
        </div><!-- /dashboard-content -->
    </main>
</div>
