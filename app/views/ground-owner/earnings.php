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

            <!-- Wallet Balance Panel -->
            <div class="go-wallet-panel" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;align-items:center;gap:1.5rem;">
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Available Balance</div>
                    <div id="walletAvailable" style="font-size:1.6rem;font-weight:700;color:#16a34a;">—</div>
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Pending (Unprocessed)</div>
                    <div id="walletPending" style="font-size:1.6rem;font-weight:700;color:#d97706;">—</div>
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Total Withdrawn</div>
                    <div id="walletWithdrawn" style="font-size:1.6rem;font-weight:700;color:#6b7280;">—</div>
                </div>
                <div style="display:flex;gap:.75rem;flex-shrink:0;">
                    <button class="go-btn" onclick="openPayoutModal()" style="background:#16a34a;color:#fff;border:none;padding:.55rem 1.1rem;border-radius:8px;cursor:pointer;font-weight:600;font-size:.875rem;">
                        <i class="fas fa-money-bill-wave"></i> Request Payout
                    </button>
                    <button class="go-btn go-btn-secondary" onclick="togglePayoutHistory()" style="padding:.55rem 1.1rem;border-radius:8px;cursor:pointer;font-weight:600;font-size:.875rem;">
                        <i class="fas fa-history"></i> History
                    </button>
                </div>
            </div>

            <!-- Payout History (hidden by default) -->
            <div id="payoutHistory" style="display:none;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
                <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:700;">Payout Requests</h3>
                <div id="payoutList" style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="padding:.5rem .75rem;text-align:left;color:#6b7280;font-weight:600;">Date</th>
                                <th style="padding:.5rem .75rem;text-align:left;color:#6b7280;font-weight:600;">Amount</th>
                                <th style="padding:.5rem .75rem;text-align:left;color:#6b7280;font-weight:600;">Bank</th>
                                <th style="padding:.5rem .75rem;text-align:left;color:#6b7280;font-weight:600;">Status</th>
                                <th style="padding:.5rem .75rem;text-align:left;color:#6b7280;font-weight:600;"></th>
                            </tr>
                        </thead>
                        <tbody id="payoutRows"><tr><td colspan="5" style="padding:.75rem;color:#9ca3af;text-align:center;">Loading…</td></tr></tbody>
                    </table>
                </div>
            </div>

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

<!-- Payout Request Modal -->
<div id="payoutModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:2rem;width:100%;max-width:440px;margin:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
            <h3 style="margin:0;font-size:1.125rem;font-weight:700;">Request Payout</h3>
            <button onclick="closePayoutModal()" style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:#6b7280;">&times;</button>
        </div>
        <p style="color:#6b7280;font-size:.875rem;margin-bottom:1.25rem;">
            Available balance: <strong id="modalAvailable" style="color:#16a34a;">—</strong>
        </p>
        <form id="payoutForm" onsubmit="submitPayout(event)">
            <div style="display:grid;gap:.875rem;">
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Amount (LKR) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="payoutAmount" min="1" step="0.01" required placeholder="e.g. 5000"
                           style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Bank Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="payoutBank" required placeholder="e.g. Sampath Bank"
                           style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Account Number <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="payoutAccNo" required placeholder="e.g. 1234567890"
                           style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Account Holder <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="payoutHolder" required placeholder="Name on account"
                           style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Branch</label>
                    <input type="text" id="payoutBranch" placeholder="e.g. Colombo 03"
                           style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;">
                </div>
            </div>
            <div id="payoutError" style="display:none;color:#ef4444;font-size:.8125rem;margin-top:.75rem;"></div>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
                <button type="button" onclick="closePayoutModal()"
                        style="flex:1;padding:.6rem;border:1px solid #d1d5db;border-radius:8px;background:#fff;cursor:pointer;font-weight:600;color:#374151;">
                    Cancel
                </button>
                <button type="submit" id="payoutSubmitBtn"
                        style="flex:1;padding:.6rem;border:none;border-radius:8px;background:#16a34a;color:#fff;cursor:pointer;font-weight:600;">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    // ── Wallet balance ───────────────────────────────────────
    function fmt(n) { return 'Rs. ' + Number(n || 0).toLocaleString('en-LK', {minimumFractionDigits:2, maximumFractionDigits:2}); }

    function loadBalance() {
        fetch('/api/ground-owner/balance')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const b = data.balance;
                document.getElementById('walletAvailable').textContent  = fmt(b.available_balance);
                document.getElementById('walletPending').textContent    = fmt(b.pending_balance);
                document.getElementById('walletWithdrawn').textContent  = fmt(b.total_withdrawn);
                document.getElementById('modalAvailable').textContent   = fmt(b.available_balance);
            })
            .catch(() => {});
    }

    loadBalance();

    // ── Payout modal ─────────────────────────────────────────
    window.openPayoutModal = function () {
        loadBalance();
        document.getElementById('payoutModal').style.display = 'flex';
    };
    window.closePayoutModal = function () {
        document.getElementById('payoutModal').style.display = 'none';
        document.getElementById('payoutError').style.display = 'none';
    };

    window.submitPayout = function (e) {
        e.preventDefault();
        const btn = document.getElementById('payoutSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Submitting…';

        const body = {
            amount:         parseFloat(document.getElementById('payoutAmount').value),
            bank_name:      document.getElementById('payoutBank').value.trim(),
            account_number: document.getElementById('payoutAccNo').value.trim(),
            account_holder: document.getElementById('payoutHolder').value.trim(),
            branch_name:    document.getElementById('payoutBranch').value.trim(),
        };

        fetch('/api/ground-owner/payouts', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closePayoutModal();
                loadBalance();
                loadPayoutHistory();
                alert('✅ ' + data.message);
                document.getElementById('payoutForm').reset();
            } else {
                const err = document.getElementById('payoutError');
                err.textContent = data.error || data.message || 'Request failed';
                err.style.display = 'block';
            }
        })
        .catch(() => {
            const err = document.getElementById('payoutError');
            err.textContent = 'Network error. Please try again.';
            err.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Submit Request';
        });
    };

    // ── Payout history ───────────────────────────────────────
    let historyVisible = false;

    window.togglePayoutHistory = function () {
        historyVisible = !historyVisible;
        const el = document.getElementById('payoutHistory');
        el.style.display = historyVisible ? 'block' : 'none';
        if (historyVisible) loadPayoutHistory();
    };

    function statusBadge(status) {
        const map = {
            pending:    ['#d97706','#fef3c7','Pending'],
            processing: ['#2563eb','#dbeafe','Processing'],
            completed:  ['#16a34a','#dcfce7','Completed'],
            rejected:   ['#dc2626','#fee2e2','Rejected'],
        };
        const [c, bg, label] = map[status] || ['#6b7280','#f3f4f6', status];
        return `<span style="background:${bg};color:${c};padding:.2rem .6rem;border-radius:99px;font-size:.75rem;font-weight:600;">${label}</span>`;
    }

    function loadPayoutHistory() {
        fetch('/api/ground-owner/payouts')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const rows = data.payouts;
                const tbody = document.getElementById('payoutRows');
                if (!rows.length) {
                    tbody.innerHTML = '<tr><td colspan="5" style="padding:.75rem;color:#9ca3af;text-align:center;">No payout requests yet.</td></tr>';
                    return;
                }
                tbody.innerHTML = rows.map(p => `
                    <tr style="border-top:1px solid #f3f4f6;">
                        <td style="padding:.5rem .75rem;">${new Date(p.requested_at).toLocaleDateString()}</td>
                        <td style="padding:.5rem .75rem;font-weight:600;">${fmt(p.amount)}</td>
                        <td style="padding:.5rem .75rem;">${p.bank_name || '—'} ${p.account_number ? '•••' + p.account_number.slice(-4) : ''}</td>
                        <td style="padding:.5rem .75rem;">${statusBadge(p.status)}</td>
                        <td style="padding:.5rem .75rem;">
                            ${p.status === 'pending' ? `<button onclick="cancelPayout(${p.id})" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:.75rem;font-weight:600;">Cancel</button>` : ''}
                        </td>
                    </tr>`).join('');
            })
            .catch(() => {});
    }

    window.cancelPayout = function (id) {
        if (!confirm('Cancel this payout request? The amount will be returned to your available balance.')) return;
        fetch('/api/ground-owner/payouts/' + id, {method:'DELETE'})
            .then(r => r.json())
            .then(data => {
                if (data.success) { loadBalance(); loadPayoutHistory(); }
                else alert(data.error || data.message || 'Failed to cancel');
            });
    };
})();
</script>

<?php include __DIR__ . '/layout-foot.php'; ?>
