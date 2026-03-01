<?php
$title = 'Availability Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-availability.css'
];
$additionalJS = ['/public/js/pages/ground-owner-availability.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Availability Management</h1>
            </div>
        </header>

        <div class="dashboard-content">

            <!-- Ground selector banner -->
            <div class="av-ground-banner">
                <div class="av-banner-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="av-banner-text">
                    <h2>Managing Ground</h2>
                    <p>Select a ground to configure its operating hours and block specific dates</p>
                </div>
                <div class="av-ground-select-wrap">
                    <i class="fas fa-chevron-down"></i>
                    <select id="groundSelector">
                        <option value="">Choose a ground…</option>
                    </select>
                </div>
            </div>

            <!-- No ground selected -->
            <div id="noGroundNotice">
                <div class="av-empty-state av-empty-select">
                    <div class="av-empty-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4>No ground selected</h4>
                    <p>Choose a ground from the dropdown above to view and manage its availability settings.</p>
                </div>
            </div>

            <!-- Content shown after ground is selected -->
            <div id="availContent" style="display:none;">

                <!-- Summary stat cards -->
                <div class="av-stats-row">
                    <div class="av-stat-card open">
                        <div class="av-stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="av-stat-info">
                            <h4>Open Days</h4>
                            <span id="statOpenDays">–</span>
                        </div>
                    </div>
                    <div class="av-stat-card closed">
                        <div class="av-stat-icon"><i class="fas fa-moon"></i></div>
                        <div class="av-stat-info">
                            <h4>Closed Days</h4>
                            <span id="statClosedDays">–</span>
                        </div>
                    </div>
                    <div class="av-stat-card blocked">
                        <div class="av-stat-icon"><i class="fas fa-ban"></i></div>
                        <div class="av-stat-info">
                            <h4>Blocked Periods</h4>
                            <span id="statBlockedPeriods">–</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="av-tab-nav">
                    <button class="av-tab-btn active" data-tab="weekly" onclick="switchTab('weekly')">
                        <i class="fas fa-calendar-week"></i>
                        Weekly Schedule
                    </button>
                    <button class="av-tab-btn" data-tab="blocked" onclick="switchTab('blocked')">
                        <i class="fas fa-ban"></i>
                        Blocked Dates
                        <span class="av-tab-badge" id="blockedCount" style="display:none;"></span>
                    </button>
                </div>

                <!-- ─── Weekly Schedule Tab ─── -->
                <div id="tab-weekly" class="av-panel">
                    <div class="av-panel-header">
                        <div class="av-panel-header-text">
                            <h3><i class="fas fa-clock"></i> Weekly Operating Hours</h3>
                            <p>Toggle each day open or closed and set the hours users can book. Changes take effect immediately after saving.</p>
                        </div>
                        <button class="av-btn av-btn-ghost av-btn-sm" onclick="resetSchedule()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>

                    <div id="weeklyScheduleTable">
                        <!-- Skeleton loader -->
                        <div class="av-loading-rows">
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                        </div>
                    </div>

                    <div class="av-schedule-footer">
                        <span class="av-schedule-footer-hint">
                            <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
                            Booking requests outside these hours will be rejected automatically.
                        </span>
                        <div class="av-schedule-footer-actions">
                            <button class="av-btn av-btn-primary" onclick="saveSchedule()" id="saveScheduleBtn">
                                <i class="fas fa-save"></i> Save Schedule
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ─── Blocked Dates Tab ─── -->
                <div id="tab-blocked" class="av-panel" style="display:none;">
                    <div class="av-panel-header">
                        <div class="av-panel-header-text">
                            <h3><i class="fas fa-ban"></i> Blocked Dates</h3>
                            <p>Prevent bookings on specific dates or date ranges — for maintenance, private events, or any other reason.</p>
                        </div>
                        <button class="av-btn av-btn-primary" onclick="openBlockModal()">
                            <i class="fas fa-plus"></i> Block Dates
                        </button>
                    </div>

                    <div id="blockedDatesList">
                        <div class="av-loading-rows">
                            <div class="av-skel-row"></div>
                            <div class="av-skel-row"></div>
                        </div>
                    </div>
                </div>

            </div><!-- /availContent -->
        </div><!-- /dashboard-content -->
    </main>
</div>

<!-- ─── Add Block Modal ─── -->
<div id="blockModal" class="av-modal-backdrop" style="display:none;" onclick="handleModalBackdrop(event)">
    <div class="av-modal">
        <div class="av-modal-header">
            <h3><i class="fas fa-ban"></i> Block Dates</h3>
            <button class="av-modal-close" onclick="closeBlockModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="av-modal-form-grid">
            <div class="av-mfield">
                <label>Start Date *</label>
                <input type="date" id="newBlockStart" required>
            </div>
            <div class="av-mfield">
                <label>End Date *</label>
                <input type="date" id="newBlockEnd" required>
            </div>
            <div class="av-mfield">
                <label>Start Time <span>(optional)</span></label>
                <input type="time" id="newBlockStartTime">
            </div>
            <div class="av-mfield">
                <label>End Time <span>(optional)</span></label>
                <input type="time" id="newBlockEndTime">
            </div>
            <div class="av-mfield">
                <label>Reason *</label>
                <select id="newBlockReason" required>
                    <option value="">Select reason…</option>
                    <option value="maintenance">🔧 Maintenance</option>
                    <option value="personal">👤 Personal Use</option>
                    <option value="event">🎉 Private Event</option>
                    <option value="weather">🌧 Weather Concerns</option>
                    <option value="other">📌 Other</option>
                </select>
            </div>
            <div class="av-mfield">
                <label>Notes <span>(optional)</span></label>
                <input type="text" id="newBlockNotes" placeholder="Additional details…">
            </div>
        </div>
        <div class="av-time-optional-note">
            <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
            Leave time fields empty to block the entire day(s).
        </div>

        <div class="av-modal-footer">
            <button class="av-btn av-btn-ghost" onclick="closeBlockModal()">Cancel</button>
            <button class="av-btn av-btn-primary" onclick="submitBlock()" id="submitBlockBtn">
                <i class="fas fa-ban"></i> Block Dates
            </button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout-foot.php'; ?>
