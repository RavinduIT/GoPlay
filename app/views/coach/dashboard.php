<?php 
$title = 'Coach Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/coach-dashboard.css', '/public/css/pages/coach-dashboard-extended.css'];
$additionalJS = ['/public/js/pages/coach-dashboard.js'];
?>
<link rel="stylesheet" href="/public/css/pages/coach-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/coach-dashboard-extended.css">
<div class="coach-dashboard">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <h1 class="page-title">Coach Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">5</span>
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
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Monthly Earnings</h3>
                        <p class="stat-number">LKR 45,800</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% from last month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card sessions">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Sessions</h3>
                        <p class="stat-number">124</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>8 sessions this week</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card students">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Clients</h3>
                        <p class="stat-number">38</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>3 new this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card rating">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Average Rating</h3>
                        <p class="stat-number">4.9</p>
                        <div class="stat-change positive">
                            <i class="fas fa-star"></i>
                            <span>From 47 reviews</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Today's Schedule -->
                <div class="dashboard-card schedule-overview">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-day"></i> Today's Schedule</h3>
                        <a href="/coach/sessions" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon upcoming">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">2</span>
                                    <span class="summary-label">Upcoming</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">1</span>
                                    <span class="summary-label">Completed</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon total">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">3</span>
                                    <span class="summary-label">Total Today</span>
                                </div>
                            </div>
                        </div>
                        <div class="next-session-highlight">
                            <div class="next-label">Next Session</div>
                            <div class="next-session-info">
                                <span class="next-time">10:00 AM</span>
                                <span class="next-client">Kamal Perera - Cricket Fundamentals</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Overview -->
                <div class="dashboard-card client-overview">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Client Overview</h3>
                        <a href="/coach/clients" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon active">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">38</span>
                                    <span class="summary-label">Active</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon new">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">3</span>
                                    <span class="summary-label">New</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon retention">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">92%</span>
                                    <span class="summary-label">Retention</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">Top Performer</span>
                            <span class="highlight-value">Dinesh Wijesinghe (92% Progress)</span>
                        </div>
                    </div>
                </div>

                <!-- Earnings Summary -->
                <div class="dashboard-card earnings-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Earnings Summary</h3>
                        <a href="/coach/earnings" class="view-all">View Details</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon primary">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">45.8K</span>
                                    <span class="summary-label">This Month</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">8.5K</span>
                                    <span class="summary-label">Pending</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">32</span>
                                    <span class="summary-label">Paid Sessions</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">Average Rate</span>
                            <span class="highlight-value">LKR 1,431 per session</span>
                        </div>
                    </div>
                </div>

                <!-- Training Programs -->
                <div class="dashboard-card programs-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-dumbbell"></i> Training Programs</h3>
                        <a href="/coach/programs" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon primary">
                                    <i class="fas fa-baseball-ball"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">3</span>
                                    <span class="summary-label">Active Programs</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon success">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">35</span>
                                    <span class="summary-label">Total Clients</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon info">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">10</span>
                                    <span class="summary-label">Avg Weeks</span>
                                </div>
                            </div>
                        </div>
                        <div class="programs-list-compact">
                            <div class="program-compact-item">
                                <span class="program-compact-name">Cricket Fundamentals</span>
                                <span class="program-compact-count">12 clients</span>
                            </div>
                            <div class="program-compact-item">
                                <span class="program-compact-name">Advanced Techniques</span>
                                <span class="program-compact-count">8 clients</span>
                            </div>
                            <div class="program-compact-item">
                                <span class="program-compact-name">Youth Development</span>
                                <span class="program-compact-count">15 clients</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessments Summary -->
                <div class="dashboard-card assessments-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-clipboard-check"></i> Assessments</h3>
                        <a href="/coach/assessments" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">2</span>
                                    <span class="summary-label">Completed</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon upcoming">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">1</span>
                                    <span class="summary-label">Scheduled</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon total">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">12</span>
                                    <span class="summary-label">This Month</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">Next Assessment</span>
                            <span class="highlight-value">Oct 25 - Saman Fernando (Strength)</span>
                        </div>
                    </div>
                </div>

                <!-- Availability Summary -->
                <div class="dashboard-card availability-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> Availability</h3>
                        <a href="/coach/availability" class="view-all">Manage</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">28</span>
                                    <span class="summary-label">Available Slots</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon primary">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">12</span>
                                    <span class="summary-label">Booked</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon info">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">5</span>
                                    <span class="summary-label">Days Active</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">This Week</span>
                            <span class="highlight-value">Mon-Fri: 4-7 slots/day • Weekend: Blocked</span>
                        </div>
                    </div>
                </div>

                <!-- Reviews Summary -->
                <div class="dashboard-card reviews-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-star"></i> Reviews & Rating</h3>
                        <a href="/coach/reviews" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon rating">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">4.9</span>
                                    <span class="summary-label">Average</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon success">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">47</span>
                                    <span class="summary-label">Total Reviews</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon new">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">5</span>
                                    <span class="summary-label">This Month</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">Latest Review</span>
                            <span class="highlight-value">⭐⭐⭐⭐⭐ Kamal Perera - "Excellent coaching!"</span>
                        </div>
                    </div>
                </div>

                <!-- Sessions Summary -->
                <div class="dashboard-card sessions-summary">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-check"></i> Sessions</h3>
                        <a href="/coach/sessions" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="summary-stats-row">
                            <div class="summary-stat-box">
                                <div class="summary-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">124</span>
                                    <span class="summary-label">Completed</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon upcoming">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">8</span>
                                    <span class="summary-label">This Week</span>
                                </div>
                            </div>
                            <div class="summary-stat-box">
                                <div class="summary-icon primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="summary-info">
                                    <span class="summary-number">65%</span>
                                    <span class="summary-label">Individual</span>
                                </div>
                            </div>
                        </div>
                        <div class="performance-highlight">
                            <span class="highlight-label">Most Active Day</span>
                            <span class="highlight-value">Wednesday - 5 sessions average</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

    <!-- Quick Session Modal -->
    <div id="quickSessionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Schedule Quick Session</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="quickSessionForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Client</label>
                            <select name="clientId" required>
                                <option value="">Select Client</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Session Type</label>
                            <select name="sessionType" required>
                                <option value="personal">Personal Training</option>
                                <option value="group">Group Session</option>
                                <option value="assessment">Assessment</option>
                                <option value="consultation">Consultation</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="sessionDate" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="sessionTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <select name="duration">
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60" selected>1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="Session objectives, special requirements, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachDashboard.closeModal('quickSessionModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachDashboard.saveQuickSession()">Schedule Session</button>
            </div>
        </div>
    </div>

    <?php foreach($additionalJS as $js): ?>
        <script src="<?= $js ?>"></script>
    <?php endforeach; ?>
</body>
</html>