<?php $currentPage = 'clients'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Clients - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-clients.css">
</head>
<body>
<div class="coach-dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- ── Page Header ──────────────────────────────────── -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-users"></i> My Clients</h1>
                <p class="header-subtitle">Manage your client base and track their progress</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" id="exportBtn">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- ── Stat Cards ────────────────────────────────────── -->
        <div class="client-stats" id="statsRow">
            <div class="stat-card" id="sc-total">
                <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8)">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="totalClients">—</div>
                    <div class="stat-label">Total Clients</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="activeClients">—</div>
                    <div class="stat-label">Active (Last 30 days)</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="newClients">—</div>
                    <div class="stat-label">New This Month</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="avgSessions">—</div>
                    <div class="stat-label">Avg Sessions / Client</div>
                </div>
            </div>
        </div>

        <!-- ── Filters ───────────────────────────────────────── -->
        <div class="clients-filters">
            <div class="filter-group">
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select id="typeFilter">
                    <option value="">All Session Types</option>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                    <option value="assessment">Assessment</option>
                </select>
                <select id="sortFilter">
                    <option value="last_session">Recently Active</option>
                    <option value="most_sessions">Most Sessions</option>
                    <option value="name">Name A–Z</option>
                    <option value="total_paid">Highest Spend</option>
                </select>
            </div>
            <div class="search-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="clientSearch" placeholder="Search by name or email…">
                </div>
            </div>
        </div>

        <!-- ── View Toggle + Grid/List ────────────────────────── -->
        <div class="clients-container">
            <div class="view-controls">
                <button class="view-btn active" data-view="grid">
                    <i class="fas fa-th-large"></i> Grid
                </button>
                <button class="view-btn" data-view="list">
                    <i class="fas fa-list"></i> List
                </button>
                <span class="results-count" id="resultsCount"></span>
            </div>

            <!-- Loading skeleton -->
            <div id="loadingState" class="loading-state">
                <div class="skeleton-grid">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="skeleton-card">
                        <div class="skeleton sk-avatar"></div>
                        <div class="sk-lines">
                            <div class="skeleton sk-line long"></div>
                            <div class="skeleton sk-line short"></div>
                            <div class="skeleton sk-line medium"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="clients-view active" style="display:none">
                <div id="clientsGrid" class="clients-grid"></div>
            </div>

            <!-- List View -->
            <div id="listView" class="clients-view" style="display:none">
                <div class="clients-table-container">
                    <table id="clientsTable" class="clients-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Sessions</th>
                                <th>Preferred Type</th>
                                <th>Last Session</th>
                                <th>Total Spent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Empty state -->
            <div id="emptyState" class="no-data" style="display:none">
                <i class="fas fa-users"></i>
                <h3>No clients yet</h3>
                <p>Clients appear here once they book a session with you.</p>
            </div>
        </div>

    </main>
</div>

<!-- ══════════════════════════════════════════════════════════
     Client Detail Modal
═══════════════════════════════════════════════════════════ -->
<div id="clientModal" class="modal">
    <div class="modal-content large">
        <div class="modal-head">
            <div id="modalAvatarWrap" class="modal-avatar-wrap">
                <div id="modalAvatar" class="modal-avatar-ring"></div>
            </div>
            <div class="modal-head-info">
                <h2 id="modalName"></h2>
                <p id="modalMeta"></p>
                <span id="modalStatusBadge" class="status-badge"></span>
            </div>
            <button class="modal-close" id="closeModal"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body-cols">
            <!-- Left: contact + stats -->
            <div class="modal-left">
                <div class="detail-section">
                    <h4><i class="fas fa-address-card"></i> Contact</h4>
                    <div class="detail-rows" id="modalContact"></div>
                </div>
                <div class="detail-section">
                    <h4><i class="fas fa-chart-bar"></i> Stats</h4>
                    <div class="mini-stats" id="modalStats"></div>
                </div>
            </div>

            <!-- Right: booking history -->
            <div class="modal-right">
                <div class="detail-section">
                    <h4><i class="fas fa-history"></i> Session History</h4>
                    <div id="modalHistory" class="history-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalBackdrop" class="modal-backdrop"></div>

<!-- ══════════════════════════════════════════════════════════
     Inline Script
═══════════════════════════════════════════════════════════ -->
<script>
(function () {
'use strict';

/* ── state ─────────────────────────────────────────────────── */
let allClients = [];
let currentView = 'grid';

/* ── DOM refs ──────────────────────────────────────────────── */
const gridEl       = document.getElementById('clientsGrid');
const tableBody    = document.getElementById('clientsTableBody');
const loadingEl    = document.getElementById('loadingState');
const gridView     = document.getElementById('gridView');
const listView     = document.getElementById('listView');
const emptyEl      = document.getElementById('emptyState');
const resultsCount = document.getElementById('resultsCount');
const searchInput  = document.getElementById('clientSearch');
const statusFilter = document.getElementById('statusFilter');
const typeFilter   = document.getElementById('typeFilter');
const sortFilter   = document.getElementById('sortFilter');
const modal        = document.getElementById('clientModal');
const backdrop     = document.getElementById('modalBackdrop');

/* ── helpers ────────────────────────────────────────────────── */
function initials(first, last) {
    return ((first || '')[0] + (last || '')[0]).toUpperCase();
}

function avatar(c, size = 52) {
    const colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4'];
    const bg = colors[(c.first_name.charCodeAt(0) || 0) % colors.length];
    if (c.profile_picture) {
        return `<img src="${c.profile_picture}" alt="${c.first_name}" style="width:${size}px;height:${size}px;border-radius:50%;object-fit:cover;">`;
    }
    return `<div style="width:${size}px;height:${size}px;border-radius:50%;background:${bg};color:#fff;display:flex;align-items:center;justify-content:center;font-size:${Math.round(size*0.35)}px;font-weight:700;flex-shrink:0;">${initials(c.first_name, c.last_name)}</div>`;
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
}

function fmtMoney(n) {
    return 'Rs. ' + parseFloat(n || 0).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function statusBadge(s) {
    const cls = s === 'active' ? 'status-active' : 'status-inactive';
    const lbl = s === 'active' ? 'Active' : 'Inactive';
    return `<span class="status-badge ${cls}">${lbl}</span>`;
}

function typeIcon(t) {
    const map = { individual:'<i class="fas fa-user"></i>', group:'<i class="fas fa-users"></i>', assessment:'<i class="fas fa-clipboard-check"></i>' };
    return (map[t] || '<i class="fas fa-dumbbell"></i>') + ' ' + (t ? t.charAt(0).toUpperCase() + t.slice(1) : '—');
}

/* ── fetch clients ──────────────────────────────────────────── */
async function loadClients() {
    try {
        const res  = await fetch('/api/coach/clients');
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed');

        allClients = data.clients || [];

        // stats row
        const s = data.stats || {};
        document.getElementById('totalClients').textContent  = s.total_clients  || 0;
        document.getElementById('activeClients').textContent = s.active_clients  || 0;
        document.getElementById('newClients').textContent    = s.this_month_clients || 0;
        const totalSessions = allClients.reduce((a, c) => a + Number(c.total_sessions || 0), 0);
        const avg = allClients.length ? (totalSessions / allClients.length).toFixed(1) : '0';
        document.getElementById('avgSessions').textContent = avg;

        loadingEl.style.display = 'none';
        render();
    } catch (e) {
        loadingEl.innerHTML = `<div class="no-data"><i class="fas fa-exclamation-circle"></i><h3>Failed to load clients</h3><p>${e.message}</p></div>`;
    }
}

/* ── filter + sort ──────────────────────────────────────────── */
function filteredClients() {
    let list = [...allClients];
    const q  = searchInput.value.toLowerCase().trim();
    const st = statusFilter.value;
    const ty = typeFilter.value;
    const so = sortFilter.value;

    if (q)  list = list.filter(c => `${c.first_name} ${c.last_name} ${c.email}`.toLowerCase().includes(q));
    if (st) list = list.filter(c => c.status === st);
    if (ty) list = list.filter(c => (c.preferred_type || '') === ty);

    list.sort((a, b) => {
        switch (so) {
            case 'most_sessions': return (b.total_sessions||0) - (a.total_sessions||0);
            case 'name':          return `${a.first_name}${a.last_name}`.localeCompare(`${b.first_name}${b.last_name}`);
            case 'total_paid':    return (b.total_paid||0) - (a.total_paid||0);
            default:              return (b.last_session_date||'') > (a.last_session_date||'') ? 1 : -1;
        }
    });
    return list;
}

/* ── render ─────────────────────────────────────────────────── */
function render() {
    const list = filteredClients();
    resultsCount.textContent = list.length + ' client' + (list.length !== 1 ? 's' : '');

    if (list.length === 0) {
        gridView.style.display = 'none';
        listView.style.display = 'none';
        emptyEl.style.display  = 'block';
        return;
    }
    emptyEl.style.display = 'none';

    if (currentView === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        renderGrid(list);
    } else {
        listView.style.display = 'block';
        gridView.style.display = 'none';
        renderList(list);
    }
}

function renderGrid(list) {
    gridEl.innerHTML = list.map(c => {
        const pct = c.total_sessions > 0
            ? Math.min(100, Math.round((c.completed_sessions / c.total_sessions) * 100))
            : 0;
        return `
        <div class="client-card" data-id="${c.user_id}">
            <div class="client-card-top">
                <div class="cc-avatar">${avatar(c, 56)}</div>
                <div class="cc-info">
                    <h3 class="cc-name">${c.first_name} ${c.last_name}</h3>
                    <div class="cc-meta">${c.email}</div>
                    ${statusBadge(c.status)}
                </div>
            </div>
            <div class="cc-divider"></div>
            <div class="cc-stats">
                <div class="cc-stat">
                    <span class="cc-stat-num">${c.total_sessions}</span>
                    <span class="cc-stat-lbl">Sessions</span>
                </div>
                <div class="cc-stat">
                    <span class="cc-stat-num">${c.completed_sessions}</span>
                    <span class="cc-stat-lbl">Completed</span>
                </div>
                <div class="cc-stat">
                    <span class="cc-stat-num">${c.upcoming_sessions > 0 ? c.upcoming_sessions : '—'}</span>
                    <span class="cc-stat-lbl">Upcoming</span>
                </div>
            </div>
            <div class="cc-progress">
                <div class="progress-label">
                    <span>Completion Rate</span><span>${pct}%</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width:${pct}%"></div></div>
            </div>
            <div class="cc-footer">
                <div class="cc-footer-info">
                    <i class="fas fa-calendar-alt"></i>
                    Last: ${fmtDate(c.last_session_date)}
                </div>
                <button class="btn-view" onclick="showClientDetail(${c.user_id})">
                    <i class="fas fa-eye"></i> View
                </button>
            </div>
        </div>`;
    }).join('');
}

function renderList(list) {
    tableBody.innerHTML = list.map(c => `
        <tr>
            <td>
                <div class="table-client">
                    ${avatar(c, 40)}
                    <div class="table-client-info">
                        <h4>${c.first_name} ${c.last_name}</h4>
                        <p>${c.email}</p>
                    </div>
                </div>
            </td>
            <td>
                <span class="pill-num">${c.total_sessions}</span>
                <span style="color:var(--text-muted);font-size:12px">${c.completed_sessions} done</span>
            </td>
            <td>${typeIcon(c.preferred_type)}</td>
            <td>${fmtDate(c.last_session_date)}</td>
            <td class="money">${fmtMoney(c.total_paid)}</td>
            <td>${statusBadge(c.status)}</td>
            <td>
                <button class="tbl-btn" onclick="showClientDetail(${c.user_id})">
                    <i class="fas fa-eye"></i> Details
                </button>
            </td>
        </tr>`).join('');
}

/* ── client detail modal ────────────────────────────────────── */
window.showClientDetail = async function (userId) {
    const c = allClients.find(x => x.user_id == userId);
    if (!c) return;

    // Header
    document.getElementById('modalAvatar').innerHTML = avatar(c, 64);
    document.getElementById('modalName').textContent = `${c.first_name} ${c.last_name}`;
    document.getElementById('modalMeta').textContent = c.email + (c.phone ? ' · ' + c.phone : '');
    const sb = document.getElementById('modalStatusBadge');
    sb.textContent = c.status === 'active' ? 'Active' : 'Inactive';
    sb.className   = 'status-badge ' + (c.status === 'active' ? 'status-active' : 'status-inactive');

    // Contact
    document.getElementById('modalContact').innerHTML = `
        <div class="dr"><i class="fas fa-envelope"></i><span>${c.email || '—'}</span></div>
        <div class="dr"><i class="fas fa-phone"></i><span>${c.phone || '—'}</span></div>
        <div class="dr"><i class="fas fa-calendar-plus"></i><span>First session: ${fmtDate(c.first_session_date)}</span></div>
        <div class="dr"><i class="fas fa-clock"></i><span>Last session: ${fmtDate(c.last_session_date)}</span></div>`;

    // Mini stats
    const pct = c.total_sessions > 0
        ? Math.round((c.completed_sessions / c.total_sessions) * 100) : 0;
    document.getElementById('modalStats').innerHTML = `
        <div class="ms"><span class="ms-n">${c.total_sessions}</span><span class="ms-l">Total</span></div>
        <div class="ms"><span class="ms-n">${c.completed_sessions}</span><span class="ms-l">Done</span></div>
        <div class="ms"><span class="ms-n">${c.upcoming_sessions}</span><span class="ms-l">Upcoming</span></div>
        <div class="ms"><span class="ms-n">${pct}%</span><span class="ms-l">Completion</span></div>
        <div class="ms full"><span class="ms-n">${fmtMoney(c.total_paid)}</span><span class="ms-l">Total Revenue</span></div>`;

    // History
    document.getElementById('modalHistory').innerHTML = '<div class="spinner-sm"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
    openModal();

    try {
        const res  = await fetch('/api/coach/clients/' + userId);
        const data = await res.json();
        const hist = data.history || [];

        if (hist.length === 0) {
            document.getElementById('modalHistory').innerHTML = '<p class="no-history">No sessions found.</p>';
            return;
        }
        document.getElementById('modalHistory').innerHTML = hist.map(h => {
            const stMap = {
                completed: { cls:'hs-done',    icon:'fa-check-circle',   lbl:'Completed' },
                confirmed: { cls:'hs-upcoming',icon:'fa-calendar-check', lbl:'Confirmed' },
                pending:   { cls:'hs-pending', icon:'fa-clock',          lbl:'Pending'   },
                cancelled: { cls:'hs-cancel',  icon:'fa-times-circle',   lbl:'Cancelled' },
                no_show:   { cls:'hs-noshow',  icon:'fa-user-slash',     lbl:'No Show'   },
            };
            const st = stMap[h.status] || { cls:'', icon:'fa-circle', lbl: h.status };
            return `
            <div class="hist-row ${st.cls}">
                <div class="hist-icon"><i class="fas ${st.icon}"></i></div>
                <div class="hist-info">
                    <div class="hist-date">${fmtDate(h.booking_date)}
                        <span class="hist-time">${h.start_time} – ${h.end_time}</span>
                    </div>
                    <div class="hist-meta">
                        ${typeIcon(h.session_type)}
                        <span class="hist-amount">${fmtMoney(h.total_amount)}</span>
                    </div>
                    ${h.coach_notes ? `<div class="hist-notes"><i class="fas fa-sticky-note"></i> ${h.coach_notes}</div>` : ''}
                </div>
                <div class="hist-status"><span class="status-badge ${st.cls === 'hs-done' ? 'status-active' : ''}">${st.lbl}</span></div>
            </div>`;
        }).join('');
    } catch (e) {
        document.getElementById('modalHistory').innerHTML = '<p class="no-history">Failed to load history.</p>';
    }
};

function openModal()  { modal.classList.add('open'); backdrop.classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal() { modal.classList.remove('open'); backdrop.classList.remove('open'); document.body.style.overflow=''; }

document.getElementById('closeModal').addEventListener('click', closeModal);
backdrop.addEventListener('click', closeModal);

/* ── view toggle ────────────────────────────────────────────── */
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentView = btn.dataset.view;
        render();
    });
});

/* ── filters / search ───────────────────────────────────────── */
let searchTimer;
searchInput.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(render, 250); });
statusFilter.addEventListener('change', render);
typeFilter.addEventListener('change', render);
sortFilter.addEventListener('change', render);

/* ── export ─────────────────────────────────────────────────── */
document.getElementById('exportBtn').addEventListener('click', () => {
    const list = filteredClients();
    if (!list.length) { alert('No clients to export.'); return; }
    const rows = [['Name','Email','Phone','Total Sessions','Completed','Last Session','Total Paid','Status']];
    list.forEach(c => rows.push([
        `${c.first_name} ${c.last_name}`, c.email, c.phone || '',
        c.total_sessions, c.completed_sessions,
        c.last_session_date || '', c.total_paid || 0, c.status
    ]));
    const csv  = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type:'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'clients.csv'; a.click();
    URL.revokeObjectURL(url);
});

/* ── init ───────────────────────────────────────────────────── */
loadClients();

})();
</script>
</body>
</html>
