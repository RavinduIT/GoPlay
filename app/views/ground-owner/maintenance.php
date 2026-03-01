<?php
$title = 'Maintenance - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-maintenance.css'
];
$additionalJS = ['/public/js/pages/ground-owner-maintenance.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Maintenance</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <select id="mtGroundFilter" class="filter-select">
                        <option value="">All Grounds</option>
                    </select>
                    <button class="btn-secondary" id="mtBtnInspection">
                        <i class="fas fa-clipboard-check"></i> Inspection
                    </button>
                    <button class="btn-primary" id="mtBtnAdd">
                        <i class="fas fa-plus"></i> Add Task
                    </button>
                </div>
            </div>
        </header>

        <div class="dashboard-content">

            <!-- Stat Cards -->
            <div class="mt-stats-row">
                <div class="mt-stat-card">
                    <div class="mt-stat-icon mt-si-blue"><i class="fas fa-tools"></i></div>
                    <div class="mt-stat-body">
                        <div class="mt-stat-value" id="mtActiveTasks">—</div>
                        <div class="mt-stat-label">Active Tasks</div>
                        <div class="mt-stat-sub">
                            High: <strong id="mtHighPri">0</strong> &nbsp;
                            Medium: <strong id="mtMedPri">0</strong>
                        </div>
                    </div>
                </div>
                <div class="mt-stat-card">
                    <div class="mt-stat-icon mt-si-red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="mt-stat-body">
                        <div class="mt-stat-value" id="mtOverdue">—</div>
                        <div class="mt-stat-label">Overdue</div>
                        <div class="mt-stat-sub">Needs attention</div>
                    </div>
                </div>
                <div class="mt-stat-card">
                    <div class="mt-stat-icon mt-si-green"><i class="fas fa-check-circle"></i></div>
                    <div class="mt-stat-body">
                        <div class="mt-stat-value" id="mtCompleted">—</div>
                        <div class="mt-stat-label">Completed This Month</div>
                        <div class="mt-stat-sub"><span id="mtCompRate">0%</span> completion rate</div>
                    </div>
                </div>
                <div class="mt-stat-card">
                    <div class="mt-stat-icon mt-si-purple"><i class="fas fa-coins"></i></div>
                    <div class="mt-stat-body">
                        <div class="mt-stat-value" id="mtCost">—</div>
                        <div class="mt-stat-label">Monthly Cost</div>
                        <div class="mt-stat-sub">Estimated + Actual</div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="mt-calendar-card">
                <div class="mt-section-header">
                    <h3>Maintenance Calendar</h3>
                    <div class="mt-cal-nav">
                        <button id="mtPrevMonth"><i class="fas fa-chevron-left"></i></button>
                        <span id="mtCalMonth">—</span>
                        <button id="mtNextMonth"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div id="mtCalendar" class="mt-calendar"></div>
                <div class="mt-cal-legend">
                    <span class="mt-leg scheduled">Scheduled</span>
                    <span class="mt-leg in-progress">In Progress</span>
                    <span class="mt-leg overdue">Overdue</span>
                    <span class="mt-leg completed">Completed</span>
                </div>
            </div>

            <!-- Tasks + Inspections -->
            <div class="mt-content-grid">

                <!-- Tasks -->
                <div class="mt-panel">
                    <div class="mt-section-header">
                        <h3>Active Tasks</h3>
                        <div class="mt-filters">
                            <select id="mtPriorityFilter">
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            <select id="mtStatusFilter">
                                <option value="">All Status</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="in-progress">In Progress</option>
                            </select>
                        </div>
                    </div>
                    <div id="mtTasksList">
                        <div class="mt-loading"><div class="mt-spinner"></div><p>Loading tasks…</p></div>
                    </div>
                </div>

                <!-- Inspections -->
                <div class="mt-panel">
                    <div class="mt-section-header">
                        <h3>Upcoming Inspections</h3>
                        <button class="mt-btn-sm" id="mtBtnInspection2">
                            <i class="fas fa-plus"></i> Schedule
                        </button>
                    </div>
                    <div id="mtInspectionsList">
                        <div class="mt-loading"><div class="mt-spinner"></div><p>Loading…</p></div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- ══════════════════════════════════════
     MODAL: Add / Edit Task
════════════════════════════════════════ -->
<div id="mtTaskModal" class="mt-modal-backdrop" style="display:none">
    <div class="mt-modal mt-modal-lg">
        <div class="mt-modal-header">
            <h3 id="mtTaskModalTitle"><i class="fas fa-tools"></i> Add Maintenance Task</h3>
            <button class="mt-modal-close" id="mtTaskModalClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="mt-modal-body">
            <form id="mtTaskForm">
                <input type="hidden" id="mtEditTaskId">

                <div class="mt-form-row">
                    <div class="mt-form-group">
                        <label>Ground *</label>
                        <select id="mtFormGround" required>
                            <option value="">Select Ground</option>
                        </select>
                    </div>
                    <div class="mt-form-group">
                        <label>Task Type *</label>
                        <select id="mtFormType" required>
                            <option value="">Select Type</option>
                            <option value="cleaning">Cleaning</option>
                            <option value="repair">Repair</option>
                            <option value="inspection">Inspection</option>
                            <option value="equipment">Equipment</option>
                            <option value="landscaping">Landscaping</option>
                            <option value="painting">Painting</option>
                            <option value="safety">Safety Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="mt-form-group">
                    <label>Task Title *</label>
                    <input type="text" id="mtFormTitle" required placeholder="e.g., Replace damaged goalpost">
                </div>

                <div class="mt-form-group">
                    <label>Description</label>
                    <textarea id="mtFormDesc" rows="3" placeholder="Detailed description…"></textarea>
                </div>

                <div class="mt-form-row">
                    <div class="mt-form-group">
                        <label>Priority *</label>
                        <select id="mtFormPriority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mt-form-group">
                        <label>Category</label>
                        <select id="mtFormCategory">
                            <option value="routine" selected>Routine</option>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                </div>

                <div class="mt-form-row">
                    <div class="mt-form-group">
                        <label>Scheduled Date *</label>
                        <input type="date" id="mtFormDate" required>
                    </div>
                    <div class="mt-form-group">
                        <label>Duration (hours)</label>
                        <input type="number" id="mtFormDuration" step="0.5" min="0.5" placeholder="e.g., 2">
                    </div>
                </div>

                <div class="mt-form-row">
                    <div class="mt-form-group">
                        <label>Estimated Cost (LKR)</label>
                        <input type="number" id="mtFormCost" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="mt-form-group">
                        <label>Assigned To</label>
                        <input type="text" id="mtFormAssigned" placeholder="Staff or contractor">
                    </div>
                </div>

                <div class="mt-form-group">
                    <label>Required Tools / Materials</label>
                    <textarea id="mtFormTools" rows="2" placeholder="List tools and materials…"></textarea>
                </div>

                <div class="mt-checks-row">
                    <label class="mt-check-label">
                        <input type="checkbox" id="mtFormBlock">
                        Block bookings during maintenance
                    </label>
                    <label class="mt-check-label">
                        <input type="checkbox" id="mtFormNotify" checked>
                        Send notifications
                    </label>
                </div>
            </form>
        </div>
        <div class="mt-modal-footer">
            <button class="mt-btn-secondary" id="mtTaskModalCancel">Cancel</button>
            <button class="mt-btn-primary" id="mtTaskModalSave">
                <i class="fas fa-save"></i> Save Task
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     MODAL: Task Details
════════════════════════════════════════ -->
<div id="mtDetailModal" class="mt-modal-backdrop" style="display:none">
    <div class="mt-modal mt-modal-lg">
        <div class="mt-modal-header">
            <h3><i class="fas fa-info-circle"></i> Task Details</h3>
            <button class="mt-modal-close" id="mtDetailClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="mt-modal-body">
            <div class="mt-detail-grid">
                <div class="mt-detail-section">
                    <h4>Basic Info</h4>
                    <div class="mt-info-rows">
                        <div class="mt-info-row"><span>Title</span><strong id="dtTitle">—</strong></div>
                        <div class="mt-info-row"><span>Ground</span><strong id="dtGround">—</strong></div>
                        <div class="mt-info-row"><span>Type</span><strong id="dtType">—</strong></div>
                        <div class="mt-info-row"><span>Priority</span><strong id="dtPriority">—</strong></div>
                        <div class="mt-info-row"><span>Status</span><strong id="dtStatus">—</strong></div>
                    </div>
                </div>
                <div class="mt-detail-section">
                    <h4>Schedule & Cost</h4>
                    <div class="mt-info-rows">
                        <div class="mt-info-row"><span>Scheduled</span><strong id="dtDate">—</strong></div>
                        <div class="mt-info-row"><span>Duration</span><strong id="dtDuration">—</strong></div>
                        <div class="mt-info-row"><span>Est. Cost</span><strong id="dtEstCost">—</strong></div>
                        <div class="mt-info-row"><span>Actual Cost</span><strong id="dtActCost">—</strong></div>
                        <div class="mt-info-row"><span>Assigned To</span><strong id="dtAssigned">—</strong></div>
                    </div>
                </div>
            </div>
            <div class="mt-detail-full">
                <h4>Description</h4>
                <p id="dtDesc" class="mt-detail-text">—</p>
            </div>
            <div class="mt-detail-full">
                <h4>Required Tools / Materials</h4>
                <p id="dtTools" class="mt-detail-text">—</p>
            </div>
            <div class="mt-detail-full">
                <h4>Progress Updates</h4>
                <div id="dtProgressList" class="mt-progress-list"></div>
                <div class="mt-add-progress">
                    <textarea id="dtProgressText" placeholder="Add a progress note…" rows="2"></textarea>
                    <button class="mt-btn-secondary" id="mtAddProgressBtn">
                        <i class="fas fa-plus"></i> Add Note
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-modal-footer">
            <button class="mt-btn-secondary" id="mtDetailClose2">Close</button>
            <button class="mt-btn-danger" id="mtDeleteTaskBtn"><i class="fas fa-trash-alt"></i> Delete</button>
            <button class="mt-btn-secondary" id="mtEditTaskBtn"><i class="fas fa-edit"></i> Edit</button>
            <button class="mt-btn-success" id="mtCompleteTaskBtn"><i class="fas fa-check"></i> Mark Complete</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     MODAL: Schedule Inspection
════════════════════════════════════════ -->
<div id="mtInspectionModal" class="mt-modal-backdrop" style="display:none">
    <div class="mt-modal">
        <div class="mt-modal-header">
            <h3><i class="fas fa-clipboard-check"></i> Schedule Inspection</h3>
            <button class="mt-modal-close" id="mtInspectionClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="mt-modal-body">
            <form id="mtInspectionForm">
                <div class="mt-form-group">
                    <label>Ground *</label>
                    <select id="mtInspGround" required>
                        <option value="">Select Ground</option>
                    </select>
                </div>
                <div class="mt-form-group">
                    <label>Inspection Type *</label>
                    <select id="mtInspType" required>
                        <option value="routine">Routine</option>
                        <option value="safety">Safety</option>
                        <option value="quality">Quality Check</option>
                        <option value="equipment">Equipment</option>
                        <option value="compliance">Compliance</option>
                    </select>
                </div>
                <div class="mt-form-row">
                    <div class="mt-form-group">
                        <label>Date *</label>
                        <input type="date" id="mtInspDate" required>
                    </div>
                    <div class="mt-form-group">
                        <label>Time</label>
                        <input type="time" id="mtInspTime">
                    </div>
                </div>
                <div class="mt-form-group">
                    <label>Inspector</label>
                    <input type="text" id="mtInspector" placeholder="Inspector name">
                </div>
                <div class="mt-form-group">
                    <label>Notes</label>
                    <textarea id="mtInspNotes" rows="3" placeholder="Additional notes…"></textarea>
                </div>
            </form>
        </div>
        <div class="mt-modal-footer">
            <button class="mt-btn-secondary" id="mtInspectionCancel">Cancel</button>
            <button class="mt-btn-primary" id="mtInspectionSave">
                <i class="fas fa-calendar-plus"></i> Schedule
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="mtToast" class="mt-toast" aria-live="polite"></div>
<?php include __DIR__ . '/layout-foot.php'; ?>
