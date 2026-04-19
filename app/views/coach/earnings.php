<?php
$_base = defined('BASE_URL') ? BASE_URL : ''; $currentPage = 'earnings'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings - Coach Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/coach-earnings.css">

</head>
<body>
<div class="coach-dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!--  Page Header  -->
        <div class="page-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title"><i class="fas fa-wallet"></i> Earnings</h1>
                    <p class="page-subtitle">Track your income and session revenue</p>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-secondary" id="exportBtn">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="earnings-content">

            <!-- Wallet Balance Panel -->
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;align-items:center;gap:1.5rem;">
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Available Balance</div>
                    <div id="walletAvailable" style="font-size:1.6rem;font-weight:700;color:#16a34a;">—</div>
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Payout Pending</div>
                    <div id="walletPending" style="font-size:1.6rem;font-weight:700;color:#d97706;">—</div>
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Total Withdrawn</div>
                    <div id="walletWithdrawn" style="font-size:1.6rem;font-weight:700;color:#6b7280;">—</div>
                </div>
                <div style="display:flex;gap:.75rem;flex-shrink:0;">
                    <button onclick="openPayoutModal()" style="background:#3b82f6;color:#fff;border:none;padding:.55rem 1.1rem;border-radius:8px;cursor:pointer;font-weight:600;font-size:.875rem;">
                        <i class="fas fa-money-bill-wave"></i> Request Payout
                    </button>
                    <button onclick="togglePayoutHistory()" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:.55rem 1.1rem;border-radius:8px;cursor:pointer;font-weight:600;font-size:.875rem;">
                        <i class="fas fa-history"></i> History
                    </button>
                </div>
            </div>

            <!-- Payout History (hidden by default) -->
            <div id="payoutHistory" style="display:none;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
                <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:700;">Payout Requests</h3>
                <div style="overflow-x:auto;">
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
            <div class="stats-grid">
                <div class="stat-card total-earnings">
                    <div class="stat-icon"><i class="fas fa-coins"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="totalEarnings">—</div>
                        <div class="stat-label">Total Earnings</div>
                        <div class="stat-change" id="earningsChange"></div>
                    </div>
                </div>
                <div class="stat-card pending-payments">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="pendingPayments">—</div>
                        <div class="stat-label">Pending Payments</div>
                        <div class="stat-change" id="pendingCount"></div>
                    </div>
                </div>
                <div class="stat-card completed-sessions">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="completedSessions">—</div>
                        <div class="stat-label">Completed Sessions</div>
                        <div class="stat-change" id="completedChange"></div>
                    </div>
                </div>
                <div class="stat-card avg-rate">
                    <div class="stat-icon"><i class="fas fa-tag"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="avgRate">—</div>
                        <div class="stat-label">Hourly Rate</div>
                        <div class="stat-change">Your base rate</div>
                    </div>
                </div>
            </div>

            <!--  Filters  -->
            <div class="filters-card">
                <div class="filters-row">
                    <div class="fgroup">
                        <label>Date Range</label>
                        <select id="dateRange">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="lastMonth">Last Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="fgroup" id="customStart" style="display:none">
                        <label>From</label>
                        <input type="date" id="startDate">
                    </div>
                    <div class="fgroup" id="customEnd" style="display:none">
                        <label>To</label>
                        <input type="date" id="endDate">
                    </div>
                    <div class="fgroup">
                        <label>Session Type</label>
                        <select id="sessionType">
                            <option value="">All Types</option>
                            <option value="individual">Individual</option>
                            <option value="group">Group</option>
                            <option value="assessment">Assessment</option>
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Payment Status</label>
                        <select id="paymentStatus">
                            <option value="">All Status</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="fgroup fgroup-btn">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary" id="applyFilters">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </div>
                </div>
            </div>

            <!--  Earnings Table  -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3><i class="fas fa-list"></i> Session Earnings</h3>
                    <div class="table-controls">
                        <span class="results-label" id="resultsLabel"></span>
                        <select id="sortBy">
                            <option value="date_desc">Latest First</option>
                            <option value="date_asc">Oldest First</option>
                            <option value="amount_desc">Highest Amount</option>
                            <option value="amount_asc">Lowest Amount</option>
                        </select>
                    </div>
                </div>

                <div id="tableLoading" class="table-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading earnings…
                </div>

                <div id="earningsTableContainer" style="display:none">
                    <table class="earnings-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Session Type</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="earningsTableBody"></tbody>
                    </table>
                </div>

                <div id="noEarnings" class="empty-state" style="display:none">
                    <i class="fas fa-receipt"></i>
                    <h3>No earnings found</h3>
                    <p>Try adjusting your filters or date range.</p>
                </div>

                <div id="pagination" class="pagination" style="display:none">
                    <button class="pg-btn" id="prevPage"><i class="fas fa-chevron-left"></i></button>
                    <span id="pageInfo" class="pg-info">Page 1 of 1</span>
                    <button class="pg-btn" id="nextPage"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div><!-- .earnings-content -->
    </main>
</div>

<!--  Session Detail Modal  -->
<div id="detailModal" class="e-modal">
    <div class="e-modal-box">
        <div class="e-modal-head">
            <h3><i class="fas fa-receipt"></i> Session Details</h3>
            <button id="closeDetail"><i class="fas fa-times"></i></button>
        </div>
        <div class="e-modal-body" id="detailContent"></div>
    </div>
</div>
<div id="detailBackdrop" class="e-backdrop"></div>

<!-- 
     JavaScript
 -->
<script>
(function () {
'use strict';

/*  state  */
let page       = 1;
let totalPages = 1;

/*  currency helper  */
function lkr(n) {
    const val = parseFloat(n || 0);
    return 'Rs. ' + val.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function changeHtml(val) {
    if (val === null || val === undefined) return '';
    const up   = val >= 0;
    const icon = up ? 'fa-arrow-up' : 'fa-arrow-down';
    const cls  = up ? 'chg-up' : 'chg-down';
    return `<span class="${cls}"><i class="fas ${icon}"></i> ${Math.abs(val)}% from last period</span>`;
}

/*  filters  */
function getFilters() {
    return {
        dateRange:     document.getElementById('dateRange').value,
        sessionType:   document.getElementById('sessionType').value,
        paymentStatus: document.getElementById('paymentStatus').value,
        sortBy:        document.getElementById('sortBy').value,
        startDate:     document.getElementById('startDate').value || '',
        endDate:       document.getElementById('endDate').value   || '',
    };
}

function filtersToQuery(f, extra = {}) {
    return new URLSearchParams({ ...f, ...extra }).toString();
}

/*  load earnings list  */
async function loadEarnings(pg = 1) {
    page = pg;
    document.getElementById('tableLoading').style.display       = 'block';
    document.getElementById('earningsTableContainer').style.display = 'none';
    document.getElementById('noEarnings').style.display         = 'none';
    document.getElementById('pagination').style.display         = 'none';

    try {
        const f   = getFilters();
        const qs  = filtersToQuery(f, { page: pg, limit: 10 });
        const res = await fetch((window.BASE_URL||'')+'/api/coach/earnings?' + qs);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed');

        renderStats(data.stats || {});
        renderTable(data.earnings || []);
        totalPages = data.totalPages || 1;
        updatePagination(pg, totalPages, data.total || 0);

    } catch (e) {
        document.getElementById('tableLoading').innerHTML =
            `<i class="fas fa-exclamation-circle"></i> ${e.message}`;
    }
}

/*  render stats  */
function renderStats(s) {
    document.getElementById('totalEarnings').textContent    = lkr(s.total_earnings);
    document.getElementById('earningsChange').innerHTML     = changeHtml(s.earnings_change);
    document.getElementById('pendingPayments').textContent  = lkr(s.pending_payments);
    document.getElementById('pendingCount').textContent     = (s.pending_count || 0) + ' sessions pending';
    document.getElementById('completedSessions').textContent = s.completed_sessions || 0;
    document.getElementById('completedChange').innerHTML    = changeHtml(s.completed_change);
    document.getElementById('avgRate').textContent          = lkr(s.avg_rate) + '/hr';
}

/*  render table  */
function renderTable(rows) {
    document.getElementById('tableLoading').style.display = 'none';
    if (!rows.length) {
        document.getElementById('noEarnings').style.display = 'block';
        return;
    }
    document.getElementById('earningsTableContainer').style.display = 'block';

    const typeColors = { individual: '#3b82f6', group: '#10b981', assessment: '#8b5cf6' };
    const payColors  = { paid: '#10b981', pending: '#f59e0b', refunded: '#ef4444' };

    document.getElementById('earningsTableBody').innerHTML = rows.map(r => `
        <tr>
            <td>
                <strong>${fmtDate(r.booking_date)}</strong>
                <div class="cell-sub">${r.start_time} – ${r.end_time}</div>
            </td>
            <td>
                <strong>${r.client_name || '—'}</strong>
                <div class="cell-sub">${r.client_email || ''}</div>
            </td>
            <td>
                <span class="type-pill" style="background:${typeColors[r.session_type] || '#94a3b8'}">
                    ${(r.session_type || 'session').charAt(0).toUpperCase() + (r.session_type || '').slice(1)}
                </span>
            </td>
            <td>${r.duration_hours}h</td>
            <td class="amount-cell">${lkr(r.total_amount)}</td>
            <td>
                <span class="pay-pill" style="background:${payColors[r.payment_status]||'#94a3b8'}20;color:${payColors[r.payment_status]||'#94a3b8'}">
                    ${(r.payment_status || '').charAt(0).toUpperCase() + (r.payment_status || '').slice(1)}
                </span>
            </td>
            <td>
                <button class="tbl-eye" onclick="viewDetail(${r.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`).join('');
}

/*  pagination  */
function updatePagination(pg, total, count) {
    const pag  = document.getElementById('pagination');
    const info = document.getElementById('pageInfo');
    const prev = document.getElementById('prevPage');
    const next = document.getElementById('nextPage');
    const lbl  = document.getElementById('resultsLabel');

    lbl.textContent = count + ' record' + (count !== 1 ? 's' : '');
    if (total <= 1 && pg === 1 && count <= 10) { pag.style.display = 'none'; return; }
    pag.style.display  = 'flex';
    info.textContent   = `Page ${pg} of ${total}`;
    prev.disabled      = pg <= 1;
    next.disabled      = pg >= total;
}

document.getElementById('prevPage').addEventListener('click', () => page > 1 && loadEarnings(page - 1));
document.getElementById('nextPage').addEventListener('click', () => page < totalPages && loadEarnings(page + 1));

/*  session detail modal  */
window.viewDetail = async function (id) {
    document.getElementById('detailContent').innerHTML =
        '<div class="d-loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
    openDetail();

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/coach/earnings/' + id);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Not found');

        const s = data.session;
        const payColor = { paid: '#10b981', pending: '#f59e0b', refunded: '#ef4444' }[s.payment_status] || '#94a3b8';

        document.getElementById('detailContent').innerHTML = `
            <div class="detail-grid">
                <div class="drow"><span>Session ID</span><strong>#${s.id}</strong></div>
                <div class="drow"><span>Client</span><strong>${s.client_name || '—'}</strong></div>
                <div class="drow"><span>Email</span><strong>${s.client_email || '—'}</strong></div>
                <div class="drow"><span>Phone</span><strong>${s.client_phone || '—'}</strong></div>
                <div class="drow"><span>Date</span><strong>${fmtDate(s.booking_date)}</strong></div>
                <div class="drow"><span>Time</span><strong>${s.start_time} – ${s.end_time}</strong></div>
                <div class="drow"><span>Duration</span><strong>${s.duration_hours} hr</strong></div>
                <div class="drow"><span>Session Type</span><strong>${(s.session_type||'').charAt(0).toUpperCase()+(s.session_type||'').slice(1)}</strong></div>
                <div class="drow"><span>Amount</span><strong class="d-amount">${lkr(s.total_amount)}</strong></div>
                <div class="drow"><span>Payment</span>
                    <strong style="color:${payColor}">${(s.payment_status||'').charAt(0).toUpperCase()+(s.payment_status||'').slice(1)}</strong>
                </div>
                <div class="drow"><span>Booking Status</span><strong>${(s.status||'').charAt(0).toUpperCase()+(s.status||'').slice(1)}</strong></div>
                ${s.special_requests ? `<div class="drow drow-full"><span>Special Requests</span><p>${s.special_requests}</p></div>` : ''}
                ${s.coach_notes      ? `<div class="drow drow-full"><span>Coach Notes</span><p>${s.coach_notes}</p></div>` : ''}
            </div>`;
    } catch (e) {
        document.getElementById('detailContent').innerHTML = `<p class="d-err">${e.message}</p>`;
    }
};

function openDetail()  { document.getElementById('detailModal').classList.add('open'); document.getElementById('detailBackdrop').classList.add('open'); document.body.style.overflow='hidden'; }
function closeDetail() { document.getElementById('detailModal').classList.remove('open'); document.getElementById('detailBackdrop').classList.remove('open'); document.body.style.overflow=''; }
document.getElementById('closeDetail').addEventListener('click', closeDetail);
document.getElementById('detailBackdrop').addEventListener('click', closeDetail);

/*  export  */
document.getElementById('exportBtn').addEventListener('click', () => {
    const f  = getFilters();
    const qs = filtersToQuery(f);
    window.location.href=(window.BASE_URL||'')+'/api/coach/earnings/export?' + qs;
});

/*  event wiring  */
document.getElementById('dateRange').addEventListener('change', e => {
    const isCustom = e.target.value === 'custom';
    document.getElementById('customStart').style.display = isCustom ? 'flex' : 'none';
    document.getElementById('customEnd').style.display   = isCustom ? 'flex' : 'none';
});

document.getElementById('applyFilters').addEventListener('click', () => loadEarnings(1));
document.getElementById('sortBy').addEventListener('change', () => loadEarnings(1));

/*  init  */
loadEarnings(1);

// ── Wallet & Payout ──────────────────────────────────────────────────────────
(function () {
    function fmt(n) { return 'Rs. ' + Number(n || 0).toLocaleString('en-LK', {minimumFractionDigits:2,maximumFractionDigits:2}); }

    function loadBalance() {
        fetch('/api/coach/balance')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const b = data.balance;
                document.getElementById('walletAvailable').textContent = fmt(b.available_balance);
                document.getElementById('walletPending').textContent   = fmt(b.pending_balance);
                document.getElementById('walletWithdrawn').textContent = fmt(b.total_withdrawn);
                const modalEl = document.getElementById('modalAvailable');
                if (modalEl) modalEl.textContent = fmt(b.available_balance);
            }).catch(() => {});
    }

    loadBalance();

    window.openPayoutModal = function () {
        loadBalance();
        document.getElementById('payoutModal').style.display = 'flex';
    };
    window.closePayoutModal = function () {
        document.getElementById('payoutModal').style.display = 'none';
    };

    window.submitPayout = function (e) {
        e.preventDefault();
        const btn = document.getElementById('payoutSubmitBtn');
        btn.disabled = true; btn.textContent = 'Submitting…';
        const body = {
            amount:         parseFloat(document.getElementById('payoutAmount').value),
            bank_name:      document.getElementById('payoutBank').value.trim(),
            account_number: document.getElementById('payoutAccNo').value.trim(),
            account_holder: document.getElementById('payoutHolder').value.trim(),
            branch_name:    document.getElementById('payoutBranch').value.trim(),
        };
        fetch('/api/coach/payouts', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)})
            .then(r => r.json())
            .then(data => {
                if (data.success) { closePayoutModal(); loadBalance(); loadPayoutHistory(); alert('✅ ' + data.message); document.getElementById('payoutForm').reset(); }
                else { const err = document.getElementById('payoutError'); err.textContent = data.error || data.message; err.style.display = 'block'; }
            })
            .catch(() => {})
            .finally(() => { btn.disabled = false; btn.textContent = 'Submit Request'; });
    };

    let historyVisible = false;
    window.togglePayoutHistory = function () {
        historyVisible = !historyVisible;
        document.getElementById('payoutHistory').style.display = historyVisible ? 'block' : 'none';
        if (historyVisible) loadPayoutHistory();
    };

    function statusBadge(s) {
        const m = {pending:['#d97706','#fef3c7','Pending'],processing:['#2563eb','#dbeafe','Processing'],completed:['#16a34a','#dcfce7','Completed'],rejected:['#dc2626','#fee2e2','Rejected']};
        const [c,bg,l] = m[s] || ['#6b7280','#f3f4f6',s];
        return `<span style="background:${bg};color:${c};padding:.2rem .6rem;border-radius:99px;font-size:.75rem;font-weight:600;">${l}</span>`;
    }

    function loadPayoutHistory() {
        fetch('/api/coach/payouts').then(r=>r.json()).then(data => {
            if (!data.success) return;
            const tbody = document.getElementById('payoutRows');
            if (!data.payouts.length) { tbody.innerHTML = '<tr><td colspan="5" style="padding:.75rem;color:#9ca3af;text-align:center;">No payout requests yet.</td></tr>'; return; }
            tbody.innerHTML = data.payouts.map(p => `
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:.5rem .75rem;">${new Date(p.requested_at).toLocaleDateString()}</td>
                    <td style="padding:.5rem .75rem;font-weight:600;">${fmt(p.amount)}</td>
                    <td style="padding:.5rem .75rem;">${p.bank_name||'—'} ${p.account_number?'•••'+p.account_number.slice(-4):''}</td>
                    <td style="padding:.5rem .75rem;">${statusBadge(p.status)}</td>
                    <td style="padding:.5rem .75rem;">${p.status==='pending'?`<button onclick="cancelPayout(${p.id})" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:.75rem;font-weight:600;">Cancel</button>`:''}</td>
                </tr>`).join('');
        }).catch(()=>{});
    }

    window.cancelPayout = function (id) {
        if (!confirm('Cancel this payout? Amount will return to your balance.')) return;
        fetch('/api/coach/payouts/'+id,{method:'DELETE'}).then(r=>r.json()).then(data => {
            if (data.success) { loadBalance(); loadPayoutHistory(); }
            else alert(data.error||data.message||'Failed');
        });
    };
})();

})();
</script>

<!-- Payout Request Modal -->
<div id="payoutModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:2rem;width:100%;max-width:440px;margin:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
            <h3 style="margin:0;font-size:1.125rem;font-weight:700;">Request Payout</h3>
            <button onclick="closePayoutModal()" style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:#6b7280;">&times;</button>
        </div>
        <p style="color:#6b7280;font-size:.875rem;margin-bottom:1.25rem;">Available: <strong id="modalAvailable" style="color:#16a34a;">—</strong></p>
        <form id="payoutForm" onsubmit="submitPayout(event)">
            <div style="display:grid;gap:.875rem;">
                <div><label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Amount (LKR) *</label>
                    <input type="number" id="payoutAmount" min="1" step="0.01" required placeholder="e.g. 5000" style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;"></div>
                <div><label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Bank Name *</label>
                    <input type="text" id="payoutBank" required placeholder="e.g. Sampath Bank" style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;"></div>
                <div><label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Account Number *</label>
                    <input type="text" id="payoutAccNo" required placeholder="e.g. 1234567890" style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;"></div>
                <div><label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Account Holder *</label>
                    <input type="text" id="payoutHolder" required placeholder="Name on account" style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;"></div>
                <div><label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Branch</label>
                    <input type="text" id="payoutBranch" placeholder="e.g. Colombo 03" style="width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;box-sizing:border-box;"></div>
            </div>
            <div id="payoutError" style="display:none;color:#ef4444;font-size:.8125rem;margin-top:.75rem;"></div>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
                <button type="button" onclick="closePayoutModal()" style="flex:1;padding:.6rem;border:1px solid #d1d5db;border-radius:8px;background:#fff;cursor:pointer;font-weight:600;color:#374151;">Cancel</button>
                <button type="submit" id="payoutSubmitBtn" style="flex:1;padding:.6rem;border:none;border-radius:8px;background:#3b82f6;color:#fff;cursor:pointer;font-weight:600;">Submit Request</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
