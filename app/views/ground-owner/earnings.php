<?php
$title = 'Earnings - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-earnings.css'
];
$additionalJS = ['/public/js/pages/ground-owner-earnings.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!--  Page Header  -->
        <div class="go-page-header">
            <div class="go-header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="go-page-title"><i class="fas fa-wallet"></i> Earnings</h1>
                    <p class="go-page-subtitle">Track revenue from all your grounds</p>
                </div>
            </div>
            <div class="go-header-right">
                <button class="go-btn go-btn-walkin" onclick="openWalkInModal()">
                    <i class="fas fa-plus"></i> Add Walk-in
                </button>
                <button class="go-btn go-btn-secondary" id="exportBtn">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="go-earnings-content">

            <!--  Stat Cards  -->
            <div class="go-stats-grid">
                <div class="go-stat-card go-stat-gold">
                    <div class="go-stat-icon"><i class="fas fa-coins"></i></div>
                    <div class="go-stat-body">
                        <div class="go-stat-value" id="statTotal">—</div>
                        <div class="go-stat-label">Total Revenue</div>
                        <div class="go-stat-sub" id="statTotalSub">All time</div>
                    </div>
                </div>
                <div class="go-stat-card go-stat-green">
                    <div class="go-stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="go-stat-body">
                        <div class="go-stat-value" id="statNet">—</div>
                        <div class="go-stat-label">Net Earnings</div>
                        <div class="go-stat-sub">After 10% commission</div>
                    </div>
                </div>
                <div class="go-stat-card go-stat-blue">
                    <div class="go-stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="go-stat-body">
                        <div class="go-stat-value" id="statMonth">—</div>
                        <div class="go-stat-label">This Month</div>
                        <div class="go-stat-sub" id="statMonthSub">Net earnings</div>
                    </div>
                </div>
                <div class="go-stat-card go-stat-orange">
                    <div class="go-stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="go-stat-body">
                        <div class="go-stat-value" id="statPending">—</div>
                        <div class="go-stat-label">Pending Payments</div>
                        <div class="go-stat-sub" id="statPendingSub">Awaiting clearance</div>
                    </div>
                </div>
            </div>

            <!--  Filters  -->
            <div class="go-filters-card">
                <div class="go-filters-row">
                    <div class="go-fgroup">
                        <label>Date Range</label>
                        <select id="filterDate">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="last30">Last 30 Days</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="go-fgroup">
                        <label>Ground</label>
                        <select id="filterGround">
                            <option value="">All Grounds</option>
                        </select>
                    </div>
                    <div class="go-fgroup">
                        <label>Status</label>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="go-fgroup">
                        <label>Sort By</label>
                        <select id="sortBy">
                            <option value="date_desc">Latest First</option>
                            <option value="date_asc">Oldest First</option>
                            <option value="amount_desc">Highest Amount</option>
                            <option value="amount_asc">Lowest Amount</option>
                        </select>
                    </div>
                    <div class="go-fgroup go-fgroup-btn">
                        <label>&nbsp;</label>
                        <button class="go-btn go-btn-primary" id="applyFilters">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </div>
                </div>
            </div>

            <!--  Transactions Table  -->
            <div class="go-table-card">
                <div class="go-table-header">
                    <h3><i class="fas fa-receipt"></i> Transactions</h3>
                    <div class="go-table-controls">
                        <span class="go-results-label" id="resultsLabel"></span>
                    </div>
                </div>

                <div id="tableLoading" class="go-table-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading transactions…
                </div>

                <div id="tableContainer" style="display:none">
                    <div style="overflow-x:auto">
                        <table class="go-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Ground</th>
                                    <th>Notes / Customer</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Commission</th>
                                    <th>Net</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="noData" class="go-empty-state" style="display:none">
                    <i class="fas fa-receipt"></i>
                    <h3>No transactions found</h3>
                    <p>Try adjusting your filters or add a walk-in transaction.</p>
                </div>

                <div id="pagination" class="go-pagination" style="display:none">
                    <button class="go-pg-btn" id="prevPage"><i class="fas fa-chevron-left"></i></button>
                    <span id="pageInfo" class="go-pg-info">Page 1 of 1</span>
                    <button class="go-pg-btn" id="nextPage"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div><!-- .go-earnings-content -->
    </main>
</div>

<!--  Transaction Detail Modal  -->
<div id="detailBackdrop" class="go-backdrop"></div>
<div id="detailModal" class="go-modal">
    <div class="go-modal-box">
        <div class="go-modal-head">
            <h3><i class="fas fa-receipt"></i> Transaction Details</h3>
            <button id="closeDetail"><i class="fas fa-times"></i></button>
        </div>
        <div class="go-modal-body" id="detailContent"></div>
    </div>
</div>

<!--  Walk-in Modal  -->
<div id="walkInBackdrop" class="go-backdrop"></div>
<div id="walkInModal" class="go-modal">
    <div class="go-modal-box">
        <div class="go-modal-head go-modal-head-green">
            <h3><i class="fas fa-user-plus"></i> Add Walk-in Transaction</h3>
            <button id="closeWalkIn"><i class="fas fa-times"></i></button>
        </div>
        <form id="walkInForm">
            <div class="go-modal-body">
                <div class="go-form-group">
                    <label>Ground <span class="go-req">*</span></label>
                    <select id="wiGround" required>
                        <option value="">Select a ground...</option>
                    </select>
                </div>
                <div class="go-form-row">
                    <div class="go-form-group">
                        <label>Amount (LKR) <span class="go-req">*</span></label>
                        <input type="number" id="wiAmount" min="1" step="0.01" placeholder="e.g. 3500" required>
                    </div>
                    <div class="go-form-group">
                        <label>Date <span class="go-req">*</span></label>
                        <input type="date" id="wiDate" required>
                    </div>
                </div>
                <div class="go-form-group">
                    <label>Payment Method <span class="go-req">*</span></label>
                    <select id="wiPayMethod" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="go-form-group">
                    <label>Notes <span class="go-opt">(optional)</span></label>
                    <textarea id="wiNotes" rows="2" placeholder="Customer name, booking details, etc."></textarea>
                </div>
            </div>
            <div class="go-modal-footer">
                <button type="button" class="go-btn go-btn-secondary" id="cancelWalkIn">Cancel</button>
                <button type="submit" class="go-btn go-btn-walkin" id="wiSubmitBtn">
                    <i class="fas fa-check"></i> Save Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layout-foot.php'; ?>
