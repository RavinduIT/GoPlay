<?php $currentPage = 'earnings'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings - Coach Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-earnings.css">

</head>
<body>
<div class="coach-dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- ── Page Header ─────────────────────────────────── -->
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

            <!-- ── Stat Cards ──────────────────────────────── -->
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

            <!-- ── Filters ──────────────────────────────────── -->
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

            <!-- ── Earnings Table ────────────────────────────── -->
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

<!-- ── Session Detail Modal ─────────────────────────────── -->
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

<!-- ══════════════════════════════════════════════════════
     JavaScript
══════════════════════════════════════════════════════ -->
<script>
(function () {
'use strict';

/* ── state ─────────────────────────────────────────────── */
let page       = 1;
let totalPages = 1;

/* ── currency helper ─────────────────────────────────── */
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

/* ── filters ──────────────────────────────────────────── */
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

/* ── load earnings list ───────────────────────────────── */
async function loadEarnings(pg = 1) {
    page = pg;
    document.getElementById('tableLoading').style.display       = 'block';
    document.getElementById('earningsTableContainer').style.display = 'none';
    document.getElementById('noEarnings').style.display         = 'none';
    document.getElementById('pagination').style.display         = 'none';

    try {
        const f   = getFilters();
        const qs  = filtersToQuery(f, { page: pg, limit: 10 });
        const res = await fetch('/api/coach/earnings?' + qs);
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

/* ── render stats ─────────────────────────────────────── */
function renderStats(s) {
    document.getElementById('totalEarnings').textContent    = lkr(s.total_earnings);
    document.getElementById('earningsChange').innerHTML     = changeHtml(s.earnings_change);
    document.getElementById('pendingPayments').textContent  = lkr(s.pending_payments);
    document.getElementById('pendingCount').textContent     = (s.pending_count || 0) + ' sessions pending';
    document.getElementById('completedSessions').textContent = s.completed_sessions || 0;
    document.getElementById('completedChange').innerHTML    = changeHtml(s.completed_change);
    document.getElementById('avgRate').textContent          = lkr(s.avg_rate) + '/hr';
}

/* ── render table ─────────────────────────────────────── */
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

/* ── pagination ───────────────────────────────────────── */
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

/* ── session detail modal ─────────────────────────────── */
window.viewDetail = async function (id) {
    document.getElementById('detailContent').innerHTML =
        '<div class="d-loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
    openDetail();

    try {
        const res  = await fetch('/api/coach/earnings/' + id);
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

/* ── export ───────────────────────────────────────────── */
document.getElementById('exportBtn').addEventListener('click', () => {
    const f  = getFilters();
    const qs = filtersToQuery(f);
    window.location.href = '/api/coach/earnings/export?' + qs;
});

/* ── event wiring ─────────────────────────────────────── */
document.getElementById('dateRange').addEventListener('change', e => {
    const isCustom = e.target.value === 'custom';
    document.getElementById('customStart').style.display = isCustom ? 'flex' : 'none';
    document.getElementById('customEnd').style.display   = isCustom ? 'flex' : 'none';
});

document.getElementById('applyFilters').addEventListener('click', () => loadEarnings(1));
document.getElementById('sortBy').addEventListener('change', () => loadEarnings(1));

/* ── init ─────────────────────────────────────────────── */
loadEarnings(1);

})();
</script>
</body>
</html>
