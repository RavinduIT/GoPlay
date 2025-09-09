<?php 
$title = 'Schedule Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-schedule.css'
];
$additionalJS = ['/public/js/pages/ground-owner-schedule.js'];
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
                <h1 class="page-title">Schedule Management</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-primary" onclick="addAvailability()">
                        <i class="fas fa-plus"></i>
                        Add Availability
                    </button>
                    <button class="btn-secondary" onclick="bulkUpdate()">
                        <i class="fas fa-edit"></i>
                        Bulk Update
                    </button>
                    <select id="groundSelector" class="filter-select">
                        <option value="">All Grounds</option>
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

        <!-- Schedule Content -->
        <div class="dashboard-content">
            <!-- Quick Stats -->
            <div class="schedule-stats">
                <div class="stat-card available-slots">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Available Slots</h3>
                        <p class="stat-number" id="availableSlots">0</p>
                        <span class="stat-period">Next 7 days</span>
                    </div>
                </div>

                <div class="stat-card booked-slots">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Booked Slots</h3>
                        <p class="stat-number" id="bookedSlots">0</p>
                        <span class="stat-period">Next 7 days</span>
                    </div>
                </div>

                <div class="stat-card occupancy-rate">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Occupancy Rate</h3>
                        <p class="stat-number" id="occupancyRate">0%</p>
                        <span class="stat-period">This week</span>
                    </div>
                </div>

                <div class="stat-card maintenance-hours">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Maintenance</h3>
                        <p class="stat-number" id="maintenanceHours">0</p>
                        <span class="stat-period">Scheduled hours</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Navigation -->
            <div class="calendar-navigation">
                <div class="nav-controls">
                    <button class="nav-btn" id="prevWeek">
                        <i class="fas fa-chevron-left"></i>
                        Previous Week
                    </button>
                    <div class="current-period">
                        <h3 id="currentWeek">December 16 - 22, 2024</h3>
                    </div>
                    <button class="nav-btn" id="nextWeek">
                        Next Week
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="view-controls">
                    <button class="view-btn active" data-view="week">Week</button>
                    <button class="view-btn" data-view="day">Day</button>
                    <button class="view-btn" data-view="month">Month</button>
                </div>
            </div>

            <!-- Schedule Calendar -->
            <div class="schedule-calendar">
                <!-- Week View -->
                <div class="calendar-view week-view active" id="weekView">
                    <div class="calendar-header">
                        <div class="time-column">Time</div>
                        <div class="day-columns">
                            <div class="day-column">Mon</div>
                            <div class="day-column">Tue</div>
                            <div class="day-column">Wed</div>
                            <div class="day-column">Thu</div>
                            <div class="day-column">Fri</div>
                            <div class="day-column">Sat</div>
                            <div class="day-column">Sun</div>
                        </div>
                    </div>
                    <div class="calendar-body" id="weekCalendarBody">
                        <!-- Dynamic schedule grid will be generated here -->
                    </div>
                </div>

                <!-- Day View -->
                <div class="calendar-view day-view" id="dayView">
                    <div class="day-header">
                        <h3 id="selectedDay">Today</h3>
                        <div class="day-controls">
                            <button class="day-nav" id="prevDay">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="day-nav" id="nextDay">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="day-schedule" id="daySchedule">
                        <!-- Day schedule will be generated here -->
                    </div>
                </div>

                <!-- Month View -->
                <div class="calendar-view month-view" id="monthView">
                    <div class="month-header">
                        <button class="month-nav" id="prevMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h3 id="currentMonth">December 2024</h3>
                        <button class="month-nav" id="nextMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="month-calendar" id="monthCalendar">
                        <!-- Month calendar will be generated here -->
                    </div>
                </div>
            </div>

            <!-- Schedule Legend -->
            <div class="schedule-legend">
                <div class="legend-item">
                    <div class="legend-color available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color booked"></div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color maintenance"></div>
                    <span>Maintenance</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color blocked"></div>
                    <span>Blocked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color pending"></div>
                    <span>Pending</span>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="quick-actions-panel">
                <h3>Quick Actions</h3>
                <div class="actions-grid">
                    <button class="action-item" onclick="setWeeklyHours()">
                        <i class="fas fa-clock"></i>
                        Set Weekly Hours
                    </button>
                    <button class="action-item" onclick="blockDates()">
                        <i class="fas fa-ban"></i>
                        Block Dates
                    </button>
                    <button class="action-item" onclick="copySchedule()">
                        <i class="fas fa-copy"></i>
                        Copy Schedule
                    </button>
                    <button class="action-item" onclick="scheduleMaintenance()">
                        <i class="fas fa-tools"></i>
                        Schedule Maintenance
                    </button>
                    <button class="action-item" onclick="setSpecialPricing()">
                        <i class="fas fa-tag"></i>
                        Special Pricing
                    </button>
                    <button class="action-item" onclick="importSchedule()">
                        <i class="fas fa-file-import"></i>
                        Import Schedule
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Availability Modal -->
    <div id="availabilityModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Add Availability</h3>
                <button class="modal-close" onclick="closeAvailabilityModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="availabilityForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="availabilityGround">Ground *</label>
                            <select id="availabilityGround" name="ground_id" required>
                                <option value="">Select Ground</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="availabilityType">Availability Type *</label>
                            <select id="availabilityType" name="type" required>
                                <option value="recurring">Recurring Weekly</option>
                                <option value="specific">Specific Dates</option>
                                <option value="range">Date Range</option>
                            </select>
                        </div>
                    </div>

                    <!-- Recurring Weekly Options -->
                    <div id="recurringOptions" class="form-section">
                        <h4>Weekly Schedule</h4>
                        <div class="days-selection">
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="monday">
                                <span>Monday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="tuesday">
                                <span>Tuesday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="wednesday">
                                <span>Wednesday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="thursday">
                                <span>Thursday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="friday">
                                <span>Friday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="saturday">
                                <span>Saturday</span>
                            </label>
                            <label class="day-checkbox">
                                <input type="checkbox" name="days[]" value="sunday">
                                <span>Sunday</span>
                            </label>
                        </div>
                    </div>

                    <!-- Specific Dates Options -->
                    <div id="specificOptions" class="form-section" style="display: none;">
                        <h4>Specific Dates</h4>
                        <div class="dates-selection">
                            <input type="date" id="specificDate" name="specific_date">
                            <button type="button" class="btn-secondary" onclick="addSpecificDate()">Add Date</button>
                        </div>
                        <div id="selectedDates" class="selected-dates"></div>
                    </div>

                    <!-- Date Range Options -->
                    <div id="rangeOptions" class="form-section" style="display: none;">
                        <h4>Date Range</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="startDate">Start Date *</label>
                                <input type="date" id="startDate" name="start_date">
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date *</label>
                                <input type="date" id="endDate" name="end_date">
                            </div>
                        </div>
                    </div>

                    <!-- Time Settings -->
                    <div class="form-section">
                        <h4>Time Settings</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="openTime">Opening Time *</label>
                                <input type="time" id="openTime" name="open_time" required>
                            </div>
                            <div class="form-group">
                                <label for="closeTime">Closing Time *</label>
                                <input type="time" id="closeTime" name="close_time" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="slotDuration">Slot Duration (minutes) *</label>
                                <select id="slotDuration" name="slot_duration" required>
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>1 hour</option>
                                    <option value="90">1.5 hours</option>
                                    <option value="120">2 hours</option>
                                    <option value="180">3 hours</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="buffer">Buffer Time (minutes)</label>
                                <select id="buffer" name="buffer_time">
                                    <option value="0" selected>No buffer</option>
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                    <option value="60">1 hour</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Settings -->
                    <div class="form-section">
                        <h4>Pricing (Optional)</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="specialPrice">Special Price (LKR)</label>
                                <input type="number" id="specialPrice" name="special_price" step="0.01" min="0">
                                <small>Leave empty to use default pricing</small>
                            </div>
                            <div class="form-group">
                                <label for="discount">Discount %</label>
                                <input type="number" id="discount" name="discount" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAvailabilityModal()">Cancel</button>
                <button type="submit" class="btn-primary" form="availabilityForm">Save Availability</button>
            </div>
        </div>
    </div>

    <!-- Block Dates Modal -->
    <div id="blockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Block Dates</h3>
                <button class="modal-close" onclick="closeBlockModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="blockForm">
                    <div class="form-group">
                        <label for="blockGround">Ground *</label>
                        <select id="blockGround" name="ground_id" required>
                            <option value="">Select Ground</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="blockStartDate">Start Date *</label>
                            <input type="date" id="blockStartDate" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="blockEndDate">End Date *</label>
                            <input type="date" id="blockEndDate" name="end_date" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="blockStartTime">Start Time</label>
                            <input type="time" id="blockStartTime" name="start_time">
                        </div>
                        <div class="form-group">
                            <label for="blockEndTime">End Time</label>
                            <input type="time" id="blockEndTime" name="end_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blockReason">Reason for Blocking *</label>
                        <select id="blockReason" name="reason" required>
                            <option value="">Select Reason</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="personal">Personal Use</option>
                            <option value="event">Private Event</option>
                            <option value="weather">Weather Concerns</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="blockNotes">Additional Notes</label>
                        <textarea id="blockNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeBlockModal()">Cancel</button>
                <button type="submit" class="btn-primary" form="blockForm">Block Dates</button>
            </div>
        </div>
    </div>
</div>