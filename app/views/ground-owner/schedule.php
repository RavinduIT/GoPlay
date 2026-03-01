<?php
$title = 'Schedule – GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-schedule.css'
];
$additionalJS = ['/public/js/pages/ground-owner-schedule.js'];
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
                <h1 class="page-title">Schedule</h1>
            </div>
            <div class="header-right">
                <div class="sc-header-actions">
                    <select id="groundSelector" class="sc-ground-select">
                        <option value="">All Grounds</option>
                    </select>
                    <button class="sc-btn sc-btn-outline" onclick="window.location='/ground-owner/availability'">
                        <i class="fas fa-clock"></i> Availability Settings
                    </button>
                    <button class="sc-btn sc-btn-primary" onclick="openBlockModal()">
                        <i class="fas fa-ban"></i> Block Dates
                    </button>
                </div>
            </div>
        </header>

        <div class="dashboard-content">

            <!-- Stats Strip -->
            <div class="sc-stats-strip">
                <div class="sc-stat">
                    <div class="sc-stat-icon blue"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="sc-stat-num" id="statTotal">0</div>
                        <div class="sc-stat-label">Total Bookings</div>
                    </div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-icon green"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <div class="sc-stat-num" id="statActive">0</div>
                        <div class="sc-stat-label">Active</div>
                    </div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-icon yellow"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="sc-stat-num" id="statPending">0</div>
                        <div class="sc-stat-label">Pending</div>
                    </div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-icon red"><i class="fas fa-ban"></i></div>
                    <div>
                        <div class="sc-stat-num" id="statBlocked">0</div>
                        <div class="sc-stat-label">Blocked Days</div>
                    </div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-icon purple"><i class="fas fa-percentage"></i></div>
                    <div>
                        <div class="sc-stat-num" id="statOccupancy">0%</div>
                        <div class="sc-stat-label">Occupancy</div>
                    </div>
                </div>
            </div>

            <!-- Calendar Card -->
            <div class="sc-calendar-card">

                <!-- Calendar toolbar -->
                <div class="sc-toolbar">
                    <div class="sc-nav-group">
                        <button class="sc-icon-btn" id="prevPeriod" title="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="sc-today-btn" onclick="goToToday()">Today</button>
                        <button class="sc-icon-btn" id="nextPeriod" title="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <h2 class="sc-period-label" id="periodLabel">–</h2>
                    </div>
                    <div class="sc-view-tabs">
                        <button class="sc-view-tab active" data-view="month">Month</button>
                        <button class="sc-view-tab" data-view="week">Week</button>
                        <button class="sc-view-tab" data-view="day">Day</button>
                    </div>
                </div>

                <!-- Loading overlay -->
                <div class="sc-loading" id="calLoading">
                    <div class="sc-spinner"></div>
                    <span>Loading schedule…</span>
                </div>

                <!-- Views -->
                <div id="viewMonth" class="sc-view"></div>
                <div id="viewWeek"  class="sc-view" style="display:none;"></div>
                <div id="viewDay"   class="sc-view" style="display:none;"></div>
            </div>

            <!-- Legend -->
            <div class="sc-legend">
                <div class="sc-legend-item"><span class="sc-dot confirmed"></span>Confirmed</div>
                <div class="sc-legend-item"><span class="sc-dot pending"></span>Pending</div>
                <div class="sc-legend-item"><span class="sc-dot cancelled"></span>Cancelled</div>
                <div class="sc-legend-item"><span class="sc-dot completed"></span>Completed</div>
                <div class="sc-legend-item"><span class="sc-dot blocked"></span>Blocked</div>
            </div>

            <!-- Blocked Dates Panel -->
            <div class="sc-blocked-panel" id="blockedPanel">
                <div class="sc-blocked-header">
                    <div>
                        <h3><i class="fas fa-ban"></i> Blocked Dates</h3>
                        <p>Dates this ground is unavailable for booking</p>
                    </div>
                    <button class="sc-btn sc-btn-primary sc-btn-sm" onclick="openBlockModal()">
                        <i class="fas fa-plus"></i> Block Dates
                    </button>
                </div>
                <div id="blockedDatesList">
                    <div class="sc-empty-small">Select a ground to see blocked dates</div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Booking Detail Modal -->
<div id="bookingModal" class="sc-modal-backdrop" style="display:none;" onclick="closeBookingModal(event)">
    <div class="sc-modal">
        <div class="sc-modal-header">
            <h3 id="bookingModalTitle">Booking Details</h3>
            <button class="sc-modal-close" onclick="document.getElementById('bookingModal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="bookingModalBody"></div>
    </div>
</div>

<!-- Block Dates Modal -->
<div id="blockModal" class="sc-modal-backdrop" style="display:none;" onclick="closeBlockModal(event)">
    <div class="sc-modal">
        <div class="sc-modal-header">
            <h3><i class="fas fa-ban" style="color:#ef4444;margin-right:8px;"></i>Block Dates</h3>
            <button class="sc-modal-close" onclick="document.getElementById('blockModal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sc-modal-body">
            <div class="sc-form-grid">
                <div class="sc-field">
                    <label>Ground *</label>
                    <select id="blockGround" required>
                        <option value="">Select ground…</option>
                    </select>
                </div>
                <div class="sc-field">
                    <label>Reason *</label>
                    <select id="blockReason" required>
                        <option value="">Select reason…</option>
                        <option value="maintenance">🔧 Maintenance</option>
                        <option value="personal">👤 Personal Use</option>
                        <option value="event">🎉 Private Event</option>
                        <option value="weather">🌧 Weather</option>
                        <option value="other">📌 Other</option>
                    </select>
                </div>
                <div class="sc-field">
                    <label>Start Date *</label>
                    <input type="date" id="blockStart" required>
                </div>
                <div class="sc-field">
                    <label>End Date *</label>
                    <input type="date" id="blockEnd" required>
                </div>
                <div class="sc-field">
                    <label>Start Time <span class="sc-optional">(leave blank = all day)</span></label>
                    <input type="time" id="blockStartTime">
                </div>
                <div class="sc-field">
                    <label>End Time</label>
                    <input type="time" id="blockEndTime">
                </div>
                <div class="sc-field sc-field-full">
                    <label>Notes</label>
                    <input type="text" id="blockNotes" placeholder="Optional details…">
                </div>
            </div>
        </div>
        <div class="sc-modal-footer">
            <button class="sc-btn sc-btn-outline" onclick="document.getElementById('blockModal').style.display='none'">Cancel</button>
            <button class="sc-btn sc-btn-danger" id="submitBlockBtn" onclick="submitBlock()">
                <i class="fas fa-ban"></i> Block Dates
            </button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout-foot.php'; ?>
