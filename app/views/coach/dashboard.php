<?php 
$title = 'Coach Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/coach-dashboard.css'];
$additionalJS = ['/public/js/pages/coach-dashboard.js'];
?>
<link rel="stylesheet" href="/public/css/pages/coach-dashboard.css">
<div class="coach-dashboard">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Coach Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">5</span>
                    </button>
                </div>
                <div class="coach-profile">
                    <div class="profile-info">
                        <span class="profile-name">Lasith Malinga</span>
                        <span class="profile-role">Cricket Coach</span>
                    </div>
                    <img src="/public/assets/images/coach-avatar.jpg" alt="Coach" class="profile-avatar">
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
                        <h3>Monthly Earnings</h3>
                        <p class="stat-number">₹28,450</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>12% this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card sessions">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Sessions</h3>
                        <p class="stat-number">87</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>6 this week</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card students">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Students</h3>
                        <p class="stat-number">45</p>
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
                            <span>Based on 23 reviews</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Schedule Overview -->
                <div class="dashboard-card schedule-overview">
                    <div class="card-header">
                        <h3>Today's Schedule</h3>
                        <div class="schedule-date">December 24, 2024</div>
                    </div>
                    <div class="schedule-list">
                        <div class="schedule-item upcoming">
                            <div class="schedule-time">
                                <span class="time">10:00 AM</span>
                                <span class="duration">1 hour</span>
                            </div>
                            <div class="schedule-info">
                                <h4>Cricket Fundamentals</h4>
                                <p>Kavinda Ranasighe</p>
                                <span class="session-type individual">Individual</span>
                            </div>
                            <div class="schedule-actions">
                                <button class="btn-start">Start Session</button>
                            </div>
                        </div>
                        
                        <div class="schedule-item upcoming">
                            <div class="schedule-time">
                                <span class="time">2:00 PM</span>
                                <span class="duration">2 hours</span>
                            </div>
                            <div class="schedule-info">
                                <h4>Bowling Technique</h4>
                                <p>Group Session (5 students)</p>
                                <span class="session-type group">Group</span>
                            </div>
                            <div class="schedule-actions">
                                <button class="btn-prepare">Prepare</button>
                            </div>
                        </div>

                        <div class="schedule-item completed">
                            <div class="schedule-time">
                                <span class="time">8:00 AM</span>
                                <span class="duration">1 hour</span>
                            </div>
                            <div class="schedule-info">
                                <h4>Batting Practice</h4>
                                <p>Dilan Wijesinghe</p>
                                <span class="session-type individual">Individual</span>
                            </div>
                            <div class="schedule-actions">
                                <span class="status completed">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="dashboard-card recent-sessions">
                    <div class="card-header">
                        <h3>Recent Sessions</h3>
                        <a href="/coach/sessions" class="view-all">View All</a>
                    </div>
                    <div class="sessions-list">
                        <div class="session-item">
                            <div class="session-info">
                                <h4>Cricket Fundamentals</h4>
                                <p>Kavinda Ranasighe</p>
                                <span class="session-date">Dec 23, 2024</span>
                            </div>
                            <div class="session-payment">
                                <span class="amount">₹800</span>
                                <span class="status-badge paid">Paid</span>
                            </div>
                        </div>
                        
                        <div class="session-item">
                            <div class="session-info">
                                <h4>Bowling Technique</h4>
                                <p>Sanduni Rajapakse</p>
                                <span class="session-date">Dec 22, 2024</span>
                            </div>
                            <div class="session-payment">
                                <span class="amount">₹1,200</span>
                                <span class="status-badge paid">Paid</span>
                            </div>
                        </div>
                        
                        <div class="session-item">
                            <div class="session-info">
                                <h4>Batting Practice</h4>
                                <p>Tharushi Amarasinghe</p>
                                <span class="session-date">Dec 21, 2024</span>
                            </div>
                            <div class="session-payment">
                                <span class="amount">₹600</span>
                                <span class="status-badge pending">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Progress -->
                <div class="dashboard-card student-progress">
                    <div class="card-header">
                        <h3>Student Progress</h3>
                        <a href="/coach/students" class="view-all">View All</a>
                    </div>
                    <div class="progress-list">
                        <div class="progress-item">
                            <div class="student-info">
                                <img src="/public/assets/images/student1.jpg" alt="Student" class="student-avatar">
                                <div class="student-details">
                                    <h4>Kavinda Ranasighe</h4>
                                    <p>12 sessions completed</p>
                                </div>
                            </div>
                            <div class="progress-chart">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 85%"></div>
                                </div>
                                <span class="progress-score">85%</span>
                            </div>
                        </div>
                        
                        <div class="progress-item">
                            <div class="student-info">
                                <img src="/public/assets/images/student2.jpg" alt="Student" class="student-avatar">
                                <div class="student-details">
                                    <h4>Sanduni Rajapakse</h4>
                                    <p>8 sessions completed</p>
                                </div>
                            </div>
                            <div class="progress-chart">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 72%"></div>
                                </div>
                                <span class="progress-score">72%</span>
                            </div>
                        </div>
                        
                        <div class="progress-item">
                            <div class="student-info">
                                <img src="/public/assets/images/student3.jpg" alt="Student" class="student-avatar">
                                <div class="student-details">
                                    <h4>Dilan Wijesinghe</h4>
                                    <p>15 sessions completed</p>
                                </div>
                            </div>
                            <div class="progress-chart">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 92%"></div>
                                </div>
                                <span class="progress-score">92%</span>
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
                        <a href="/coach/availability" class="action-btn primary">
                            <i class="fas fa-clock"></i>
                            <span>Update Availability</span>
                        </a>
                        <a href="/coach/sessions/create" class="action-btn secondary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Schedule Session</span>
                        </a>
                        <a href="/coach/students" class="action-btn accent">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Student</span>
                        </a>
                        <a href="/coach/reports" class="action-btn success">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card recent-reviews">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                        <a href="/coach/reviews" class="view-all">View All</a>
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
                            <p class="review-text">"Excellent coaching! Malinga sir helped me improve my bowling technique significantly."</p>
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
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">"Great coach with excellent knowledge and patience. Highly recommended!"</p>
                            <span class="review-time">1 day ago</span>
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