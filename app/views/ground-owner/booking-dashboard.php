<?php
$title = 'Booking Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-bookings.css',
];
$additionalJS = ['/public/js/pages/ground-owner-bookings.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!--  Page Header  -->
        <div class="bk-page-header">
            <div class="bk-header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="bk-page-title"><i class="fas fa-calendar-check"></i> Booking Management</h1>
                    <p class="bk-page-subtitle">View and manage all ground bookings</p>
                </div>
            </div>
            <div class="bk-header-right">
                <button class="bk-btn bk-btn-secondary" id="exportBtn">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="bk-content">

            <!--  Stat Cards  -->
            <div class="bk-stats-grid">
                <div class="bk-stat-card bk-stat-blue">
                    <div class="bk-stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="bk-stat-body">
                        <div class="bk-stat-value" id="statTotal">—</div>
                        <div class="bk-stat-label">Total Bookings</div>
                        <div class="bk-stat-sub">All time</div>
                    </div>
                </div>
                <div class="bk-stat-card bk-stat-green">
                    <div class="bk-stat-icon"><i class="fas fa-sun"></i></div>
                    <div class="bk-stat-body">
                        <div class="bk-stat-value" id="statToday">—</div>
                        <div class="bk-stat-label">Today</div>
                        <div class="bk-stat-sub">Active bookings</div>
                    </div>
                </div>
                <div class="bk-stat-card bk-stat-purple">
                    <div class="bk-stat-icon"><i class="fas fa-arrow-right"></i></div>
                    <div class="bk-stat-body">
                        <div class="bk-stat-value" id="statUpcoming">—</div>
                        <div class="bk-stat-label">Upcoming</div>
                        <div class="bk-stat-sub">Confirmed &amp; pending</div>
                    </div>
                </div>
                <div class="bk-stat-card bk-stat-gold">
                    <div class="bk-stat-icon"><i class="fas fa-check"></i></div>
                    <div class="bk-stat-body">
                        <div class="bk-stat-value" id="statCompleted">—</div>
                        <div class="bk-stat-label">Completed</div>
                        <div class="bk-stat-sub">Successfully done</div>
                    </div>
                </div>
            </div>

            <!--  Filters Card  -->
            <div class="bk-filters-card">
                <div class="bk-filters-row">
                    <div class="bk-fgroup bk-fgroup-search">
                        <label>Search Customer</label>
                        <div class="bk-search-wrap">
                            <i class="fas fa-search bk-search-icon"></i>
                            <input type="text" id="filterSearch" placeholder="Name or email…">
                        </div>
                    </div>
                    <div class="bk-fgroup">
                        <label>Ground</label>
                        <select id="filterFacility">
                            <option value="">All Grounds</option>
                        </select>
                    </div>
                    <div class="bk-fgroup">
                        <label>Status</label>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="bk-fgroup">
                        <label>From</label>
                        <input type="date" id="filterStart">
                    </div>
                    <div class="bk-fgroup">
                        <label>To</label>
                        <input type="date" id="filterEnd">
                    </div>
                    <div class="bk-fgroup bk-fgroup-btn">
                        <label>&nbsp;</label>
                        <button class="bk-btn bk-btn-primary" id="applyFilters">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </div>
                    <div class="bk-fgroup bk-fgroup-btn">
                        <label>&nbsp;</label>
                        <button class="bk-btn bk-btn-ghost" id="clearFilters">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!--  Bookings Table  -->
            <div class="bk-table-card">
                <div class="bk-table-header">
                    <h3><i class="fas fa-list-alt"></i> Bookings</h3>
                    <span class="bk-results-label" id="resultsLabel"></span>
                </div>

                <div id="tableLoading" class="bk-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading bookings…
                </div>

                <div id="tableContainer" style="display:none">
                    <div style="overflow-x:auto">
                        <table class="bk-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Ground</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="noData" class="bk-empty" style="display:none">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No bookings found</h3>
                    <p>Try adjusting your filters or date range.</p>
                </div>

                <div id="pagination" class="bk-pagination" style="display:none">
                    <button class="bk-pg-btn" id="prevPage"><i class="fas fa-chevron-left"></i></button>
                    <span id="pageInfo" class="bk-pg-info">Page 1 of 1</span>
                    <button class="bk-pg-btn" id="nextPage"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div><!-- .bk-content -->
    </main>
</div>

<!--  Booking Detail Modal  -->
<div id="detailBackdrop" class="bk-backdrop"></div>
<div id="detailModal" class="bk-modal">
    <div class="bk-modal-box">
        <div class="bk-modal-head">
            <h3><i class="fas fa-calendar-check"></i> Booking Details</h3>
            <button id="closeDetail"><i class="fas fa-times"></i></button>
        </div>
        <div class="bk-modal-body" id="detailContent"></div>
        <div class="bk-modal-footer" id="detailFooter"></div>
    </div>
</div>

<!--  Cancel Booking Modal  -->
<div id="cancelBackdrop" class="bk-backdrop"></div>
<div id="cancelModal" class="bk-modal">
    <div class="bk-modal-box bk-modal-sm">
        <div class="bk-modal-head bk-modal-head-red">
            <h3><i class="fas fa-ban"></i> Cancel Booking</h3>
            <button id="closeCancelModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="bk-modal-body">
            <p class="bk-cancel-desc">Please provide a reason for cancelling booking <strong id="cancelBookingId"></strong>.</p>
            <div class="bk-form-group">
                <label>Cancellation Reason <span class="bk-req">*</span></label>
                <textarea id="cancelReason" rows="3" placeholder="e.g. Ground maintenance, double booking…"></textarea>
            </div>
        </div>
        <div class="bk-modal-footer">
            <button class="bk-btn bk-btn-ghost" id="closeCancelBtn">Keep Booking</button>
            <button class="bk-btn bk-btn-danger" id="confirmCancelBtn">
                <i class="fas fa-ban"></i> Cancel Booking
            </button>
        </div>
    </div>
</div>

<!--  Confirm Status Modal  -->
<div id="statusBackdrop" class="bk-backdrop"></div>
<div id="statusModal" class="bk-modal">
    <div class="bk-modal-box bk-modal-sm">
        <div class="bk-modal-head" id="statusModalHead">
            <h3 id="statusModalTitle"><i class="fas fa-check"></i> Confirm Action</h3>
            <button id="closeStatusModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="bk-modal-body">
            <p class="bk-cancel-desc" id="statusModalDesc">Are you sure?</p>
        </div>
        <div class="bk-modal-footer">
            <button class="bk-btn bk-btn-ghost" id="closeStatusBtn">Go Back</button>
            <button class="bk-btn bk-btn-primary" id="confirmStatusBtn">
                <i class="fas fa-check"></i> Confirm
            </button>
        </div>
    </div>
</div>

<!--  Complete Booking + Payment Confirmation Modal  -->
<div id="payConfirmBackdrop" class="bk-backdrop"></div>
<div id="payConfirmModal" class="bk-modal">
    <div class="bk-modal-box" style="width:min(360px,95vw)">
        <div class="bk-modal-head" style="background:linear-gradient(135deg,#16a34a,#15803d);padding:14px 18px">
            <h3 style="font-size:14px"><i class="fas fa-check-circle"></i> Complete Booking <span id="payConfirmBookingId"></span></h3>
            <button id="closePayConfirmModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="bk-modal-body" style="padding:16px 18px">
            <p style="font-size:13px;color:#374151;margin:0 0 12px">How was payment received?</p>
            <div style="margin-bottom:10px">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px">Payment Method</label>
                <select id="payConfirmMethod" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1e293b">
                    <option value="cash">Walk-in — Cash</option>
                    <option value="card">Walk-in — Card (POS)</option>
                    <option value="bank_transfer">Walk-in — Bank Transfer</option>
                    <option value="online">Online Payment</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px">Amount Received (Rs.)</label>
                <input type="number" id="payConfirmAmount" min="0" step="0.01"
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1e293b;box-sizing:border-box">
            </div>
        </div>
        <div class="bk-modal-footer" style="flex-direction:column;gap:7px;align-items:stretch;padding:12px 18px">
            <button class="bk-btn bk-btn-success" id="confirmPayBtn"
                    style="width:100%;justify-content:center;padding:10px 14px;font-size:13px">
                <i class="fas fa-money-bill-wave"></i> Complete &amp; Record Payment
            </button>
            <button class="bk-btn bk-btn-ghost" id="completeNoPayBtn"
                    style="width:100%;justify-content:center;font-size:12px;padding:8px 14px">
                <i class="fas fa-check"></i> Complete Without Recording Payment
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout-foot.php'; ?>
