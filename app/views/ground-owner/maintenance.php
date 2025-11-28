<?php 
$title = 'Maintenance Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-maintenance.css'
];
$additionalJS = ['/public/js/pages/ground-owner-maintenance.js'];
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
                <h1 class="page-title">Maintenance Management</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-primary" onclick="addMaintenanceTask()">
                        <i class="fas fa-plus"></i>
                        Add Task
                    </button>
                    <button class="btn-secondary" onclick="scheduleInspection()">
                        <i class="fas fa-search"></i>
                        Schedule Inspection
                    </button>
                    <select id="groundFilter" class="filter-select">
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

        <!-- Maintenance Content -->
        <div class="dashboard-content">
            <!-- Maintenance Overview -->
            <div class="maintenance-overview">
                <div class="overview-card active-tasks">
                    <div class="card-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="card-content">
                        <h3>Active Tasks</h3>
                        <p class="stat-number" id="activeTasks">0</p>
                        <div class="priority-breakdown">
                            <span class="high-priority">High: <strong id="highPriorityTasks">0</strong></span>
                            <span class="medium-priority">Medium: <strong id="mediumPriorityTasks">0</strong></span>
                        </div>
                    </div>
                </div>

                <div class="overview-card overdue-tasks">
                    <div class="card-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-content">
                        <h3>Overdue Tasks</h3>
                        <p class="stat-number warning" id="overdueTasks">0</p>
                        <span class="urgency-indicator">Needs immediate attention</span>
                    </div>
                </div>

                <div class="overview-card completed-month">
                    <div class="card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-content">
                        <h3>Completed This Month</h3>
                        <p class="stat-number" id="completedMonth">0</p>
                        <div class="completion-rate">
                            <span id="completionRate">0%</span> completion rate
                        </div>
                    </div>
                </div>

                <div class="overview-card maintenance-cost">
                    <div class="card-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="card-content">
                        <h3>Monthly Cost</h3>
                        <p class="stat-number" id="monthlyCost">LKR 0</p>
                        <div class="cost-breakdown">
                            <span class="cost-change" id="costChange">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Calendar -->
            <div class="maintenance-calendar-section">
                <div class="section-header">
                    <h3>Maintenance Calendar</h3>
                    <div class="calendar-controls">
                        <button class="calendar-nav" id="prevCalendarMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h4 id="calendarMonth">December 2024</h4>
                        <button class="calendar-nav" id="nextCalendarMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="maintenance-calendar" id="maintenanceCalendar">
                    <!-- Calendar will be generated dynamically -->
                </div>
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color scheduled"></div>
                        <span>Scheduled</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color in-progress"></div>
                        <span>In Progress</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color overdue"></div>
                        <span>Overdue</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color completed"></div>
                        <span>Completed</span>
                    </div>
                </div>
            </div>

            <!-- Tasks and Inspections -->
            <div class="maintenance-content-grid">
                <!-- Active Tasks -->
                <div class="maintenance-section active-tasks-section">
                    <div class="section-header">
                        <h3>Active Maintenance Tasks</h3>
                        <div class="section-filters">
                            <select id="taskPriorityFilter" class="filter-select">
                                <option value="">All Priorities</option>
                                <option value="high">High Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="low">Low Priority</option>
                            </select>
                            <select id="taskStatusFilter" class="filter-select">
                                <option value="">All Status</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="in-progress">In Progress</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </div>
                    <div class="tasks-list" id="activeTasksList">
                        <!-- Dynamic tasks will be loaded here -->
                    </div>
                </div>

                <!-- Inspection Schedule -->
                <div class="maintenance-section inspection-section">
                    <div class="section-header">
                        <h3>Upcoming Inspections</h3>
                        <button class="btn-secondary" onclick="scheduleInspection()">
                            <i class="fas fa-plus"></i>
                            Schedule New
                        </button>
                    </div>
                    <div class="inspections-list" id="inspectionsList">
                        <!-- Dynamic inspections will be loaded here -->
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Add Maintenance Task Modal -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3 id="maintenanceModalTitle">Add Maintenance Task</h3>
                <button class="modal-close" onclick="closeMaintenanceModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="maintenanceForm">
                    <input type="hidden" id="taskId" name="task_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="taskGround">Ground *</label>
                            <select id="taskGround" name="ground_id" required>
                                <option value="">Select Ground</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="taskType">Task Type *</label>
                            <select id="taskType" name="task_type" required>
                                <option value="">Select Type</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="repair">Repair</option>
                                <option value="inspection">Inspection</option>
                                <option value="equipment">Equipment Maintenance</option>
                                <option value="landscaping">Landscaping</option>
                                <option value="painting">Painting</option>
                                <option value="safety">Safety Check</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taskTitle">Task Title *</label>
                        <input type="text" id="taskTitle" name="title" required placeholder="e.g., Replace damaged goalpost">
                    </div>

                    <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea id="taskDescription" name="description" rows="3" placeholder="Detailed description of the maintenance task..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="taskPriority">Priority *</label>
                            <select id="taskPriority" name="priority" required>
                                <option value="low">Low Priority</option>
                                <option value="medium" selected>Medium Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="taskCategory">Category</label>
                            <select id="taskCategory" name="category">
                                <option value="">Select Category</option>
                                <option value="preventive">Preventive</option>
                                <option value="corrective">Corrective</option>
                                <option value="emergency">Emergency</option>
                                <option value="routine">Routine</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="scheduledDate">Scheduled Date *</label>
                            <input type="date" id="scheduledDate" name="scheduled_date" required>
                        </div>
                        <div class="form-group">
                            <label for="estimatedDuration">Est. Duration (hours)</label>
                            <input type="number" id="estimatedDuration" name="estimated_duration" step="0.5" min="0.5">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimatedCost">Estimated Cost (LKR)</label>
                            <input type="number" id="estimatedCost" name="estimated_cost" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label for="assignedTo">Assigned To</label>
                            <input type="text" id="assignedTo" name="assigned_to" placeholder="Staff member or contractor">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="requiredTools">Required Tools/Materials</label>
                        <textarea id="requiredTools" name="required_tools" rows="2" placeholder="List tools and materials needed..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="blockBookings" name="block_bookings">
                            Block bookings during maintenance
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="sendNotifications" name="send_notifications" checked>
                            Send notifications to relevant parties
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeMaintenanceModal()">Cancel</button>
                <button type="submit" class="btn-primary" form="maintenanceForm">Save Task</button>
            </div>
        </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskDetailsModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Task Details</h3>
                <button class="modal-close" onclick="closeTaskDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="task-details-content">
                    <div class="task-info-grid">
                        <div class="info-section basic-info">
                            <h4>Basic Information</h4>
                            <div class="info-items">
                                <div class="info-item">
                                    <label>Title:</label>
                                    <span id="detailTaskTitle">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Ground:</label>
                                    <span id="detailTaskGround">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Type:</label>
                                    <span id="detailTaskType">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Priority:</label>
                                    <span id="detailTaskPriority" class="priority-badge">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Status:</label>
                                    <span id="detailTaskStatus" class="status-badge">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-section schedule-info">
                            <h4>Schedule & Cost</h4>
                            <div class="info-items">
                                <div class="info-item">
                                    <label>Scheduled:</label>
                                    <span id="detailScheduledDate">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Duration:</label>
                                    <span id="detailDuration">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Estimated Cost:</label>
                                    <span id="detailEstimatedCost">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Actual Cost:</label>
                                    <span id="detailActualCost">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Assigned To:</label>
                                    <span id="detailAssignedTo">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="task-description">
                        <h4>Description</h4>
                        <p id="detailTaskDescription">-</p>
                    </div>

                    <div class="task-tools">
                        <h4>Required Tools/Materials</h4>
                        <p id="detailRequiredTools">-</p>
                    </div>

                    <div class="task-progress">
                        <h4>Progress Updates</h4>
                        <div id="taskProgressList">
                            <!-- Progress updates will be shown here -->
                        </div>
                        <div class="add-progress">
                            <textarea id="progressUpdate" placeholder="Add progress update..." rows="2"></textarea>
                            <button class="btn-secondary" onclick="addProgressUpdate()">Add Update</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeTaskDetailsModal()">Close</button>
                <button type="button" class="btn-success" onclick="completeTask()" id="completeTaskBtn">Mark Complete</button>
                <button type="button" class="btn-primary" onclick="editTask()">Edit Task</button>
            </div>
        </div>
    </div>

    <!-- Inspection Modal -->
    <div id="inspectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Schedule Inspection</h3>
                <button class="modal-close" onclick="closeInspectionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="inspectionForm">
                    <div class="form-group">
                        <label for="inspectionGround">Ground *</label>
                        <select id="inspectionGround" name="ground_id" required>
                            <option value="">Select Ground</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inspectionType">Inspection Type *</label>
                        <select id="inspectionType" name="inspection_type" required>
                            <option value="routine">Routine Inspection</option>
                            <option value="safety">Safety Inspection</option>
                            <option value="quality">Quality Check</option>
                            <option value="equipment">Equipment Check</option>
                            <option value="compliance">Compliance Check</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="inspectionDate">Inspection Date *</label>
                            <input type="date" id="inspectionDate" name="inspection_date" required>
                        </div>
                        <div class="form-group">
                            <label for="inspectionTime">Time</label>
                            <input type="time" id="inspectionTime" name="inspection_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inspector">Inspector</label>
                        <input type="text" id="inspector" name="inspector" placeholder="Inspector name">
                    </div>
                    <div class="form-group">
                        <label for="inspectionNotes">Notes</label>
                        <textarea id="inspectionNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeInspectionModal()">Cancel</button>
                <button type="submit" class="btn-primary" form="inspectionForm">Schedule Inspection</button>
            </div>
        </div>
    </div>
</div>