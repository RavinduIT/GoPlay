/* 
   Ground Owner Earnings – JS
 */
(function () {
'use strict';

/*  helpers  */
function lkr(n) {
    return 'LKR ' + (parseFloat(n) || 0).toLocaleString('en-LK', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function cap(s) {
    if (!s) return '—';
    return s.charAt(0).toUpperCase() + s.slice(1);
}

/*  state  */
let allTx     = [];
let filtered  = [];
let page      = 1;
const PER_PAGE = 12;

/*  toast  */
function toast(msg, type = 'info') {
    let wrap = document.getElementById('goToastWrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'goToastWrap';
        wrap.className = 'go-toast-wrap';
        document.body.appendChild(wrap);
    }
    const t = document.createElement('div');
    const icon = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' }[type] || 'info-circle';
    t.className = `go-toast ${type}`;
    t.innerHTML = `<i class="fas fa-${icon}"></i> ${msg}`;
    wrap.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => {
        t.classList.remove('show');
        setTimeout(() => t.remove(), 300);
    }, 3500);
}

/*  date filter helper  */
function inRange(dateStr, range) {
    if (range === 'all' || !dateStr) return true;
    const d   = new Date(dateStr);
    const now = new Date();
    const sod = (dt) => { const x = new Date(dt); x.setHours(0,0,0,0); return x; };

    if (range === 'today') return d >= sod(now);
    if (range === 'week') {
        const start = sod(now);
        start.setDate(now.getDate() - now.getDay());
        return d >= start;
    }
    if (range === 'month') return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    if (range === 'last30') {
        const cutoff = new Date(now); cutoff.setDate(now.getDate() - 30);
        return d >= cutoff;
    }
    if (range === 'quarter') {
        const q = Math.floor(now.getMonth() / 3);
        return Math.floor(d.getMonth() / 3) === q && d.getFullYear() === now.getFullYear();
    }
    if (range === 'year') return d.getFullYear() === now.getFullYear();
    return true;
}

/*  load stats  */
async function loadStats() {
    try {
        const res = await fetch((window.BASE_URL||'')+'/api/ground-owner/earnings');
        if (!res.ok) return;
        const data = await res.json();
        const ov = data.overview || {};

        const gross = parseFloat(ov.totalRevenue || 0);
        const net   = gross * 0.9;

        document.getElementById('statTotal').textContent  = lkr(gross);
        document.getElementById('statTotalSub').textContent = (ov.totalBookings || 0) + ' bookings total';
        document.getElementById('statNet').textContent    = lkr(net);
        document.getElementById('statMonth').textContent  = lkr(ov.monthlyEarnings || 0);
        document.getElementById('statMonthSub').textContent = (ov.monthBookings || 0) + ' bookings this month';
        document.getElementById('statPending').textContent = lkr(ov.pendingPayments || 0);
        document.getElementById('statPendingSub').textContent = (ov.pendingCount || 0) + ' transactions';
    } catch (e) {
        console.error('Stats load error:', e);
    }
}

/*  load transactions  */
async function loadTransactions() {
    try {
        const res = await fetch((window.BASE_URL||'')+'/api/ground-owner/earnings/transactions?limit=200');
        if (!res.ok) return;
        const data = await res.json();
        allTx = data.transactions || [];
        populateGroundFilter();
        applyFilters();
    } catch (e) {
        console.error('Transactions load error:', e);
        document.getElementById('tableLoading').innerHTML =
            '<i class="fas fa-exclamation-circle"></i> Failed to load transactions';
    }
}

/*  populate ground filter dropdown  */
function populateGroundFilter() {
    const sel  = document.getElementById('filterGround');
    const seen = new Set();
    allTx.forEach(tx => {
        if (tx.ground && !seen.has(tx.ground)) {
            seen.add(tx.ground);
            const opt = document.createElement('option');
            opt.value = tx.ground;
            opt.textContent = tx.ground;
            sel.appendChild(opt);
        }
    });
}

/*  apply filters & sort  */
function applyFilters() {
    const dateRange = document.getElementById('filterDate').value;
    const ground    = document.getElementById('filterGround').value;
    const status    = document.getElementById('filterStatus').value;
    const sort      = document.getElementById('sortBy').value;

    filtered = allTx.filter(tx => {
        if (!inRange(tx.date || tx.created_at, dateRange)) return false;
        if (ground && tx.ground !== ground) return false;
        if (status && tx.status !== status) return false;
        return true;
    });

    filtered.sort((a, b) => {
        if (sort === 'date_desc') return new Date(b.date || b.created_at) - new Date(a.date || a.created_at);
        if (sort === 'date_asc')  return new Date(a.date || a.created_at) - new Date(b.date || b.created_at);
        if (sort === 'amount_desc') return (b.amount || 0) - (a.amount || 0);
        if (sort === 'amount_asc')  return (a.amount || 0) - (b.amount || 0);
        return 0;
    });

    page = 1;
    renderTable();
}

/*  render table  */
function renderTable() {
    const loading   = document.getElementById('tableLoading');
    const container = document.getElementById('tableContainer');
    const noData    = document.getElementById('noData');
    const pag       = document.getElementById('pagination');
    const label     = document.getElementById('resultsLabel');

    loading.style.display = 'none';

    if (!filtered.length) {
        container.style.display = 'none';
        noData.style.display    = 'block';
        pag.style.display       = 'none';
        label.textContent       = '';
        return;
    }

    noData.style.display    = 'none';
    container.style.display = 'block';

    const totalPages = Math.ceil(filtered.length / PER_PAGE);
    const start      = (page - 1) * PER_PAGE;
    const pageRows   = filtered.slice(start, start + PER_PAGE);

    label.textContent = filtered.length + ' record' + (filtered.length !== 1 ? 's' : '');

    document.getElementById('tableBody').innerHTML = pageRows.map(tx => {
        const statusCls = tx.status === 'completed' ? 'completed'
                        : tx.status === 'pending'   ? 'pending' : 'failed';
        const method = (tx.payment_method || '').replace('_', ' ');
        const notes  = tx.notes || tx.customer || '—';

        return `
        <tr>
            <td>
                <strong>${fmtDate(tx.date || tx.created_at)}</strong>
                <div class="go-cell-sub">#${tx.id}</div>
            </td>
            <td><strong>${tx.ground || '—'}</strong></td>
            <td>
                <span style="color:#374151">${notes.length > 35 ? notes.slice(0,35) + '…' : notes}</span>
            </td>
            <td><span class="go-method-pill">${method || 'online'}</span></td>
            <td class="go-amount-cell">${lkr(tx.amount)}</td>
            <td class="go-comm-cell">- ${lkr(tx.fee)}</td>
            <td class="go-net-cell">${lkr(tx.net)}</td>
            <td>
                <span class="go-status-pill ${statusCls}">
                    <i class="fas fa-${statusCls === 'completed' ? 'check' : statusCls === 'pending' ? 'clock' : 'times'}"></i>
                    ${cap(tx.status)}
                </span>
            </td>
            <td>
                <button class="go-tbl-eye" onclick="viewDetail(${JSON.stringify(tx).replace(/"/g,'&quot;')})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`;
    }).join('');

    // Pagination
    if (totalPages <= 1) {
        pag.style.display = 'none';
    } else {
        pag.style.display = 'flex';
        document.getElementById('pageInfo').textContent = `Page ${page} of ${totalPages}`;
        document.getElementById('prevPage').disabled = page <= 1;
        document.getElementById('nextPage').disabled = page >= totalPages;
    }
}

/*  detail modal  */
window.viewDetail = function (tx) {
    if (typeof tx === 'string') {
        try { tx = JSON.parse(tx); } catch(e) { return; }
    }

    const payColor = { completed: '#059669', pending: '#d97706', failed: '#dc2626' }[tx.status] || '#94a3b8';

    document.getElementById('detailContent').innerHTML = `
        <div class="go-detail-grid">
            <div class="go-drow"><span>Transaction #</span><strong>#${tx.id}</strong></div>
            <div class="go-drow"><span>Ground</span><strong>${tx.ground || '—'}</strong></div>
            <div class="go-drow"><span>Date</span><strong>${fmtDate(tx.date || tx.created_at)}</strong></div>
            <div class="go-drow"><span>Payment Method</span><strong style="text-transform:capitalize">${(tx.payment_method || 'online').replace('_',' ')}</strong></div>
            <div class="go-drow"><span>Gross Amount</span><strong class="go-d-amount">${lkr(tx.amount)}</strong></div>
            <div class="go-drow"><span>Commission (10%)</span><strong style="color:#dc2626">- ${lkr(tx.fee)}</strong></div>
            <div class="go-drow"><span>Net Earnings</span><strong class="go-d-net">${lkr(tx.net)}</strong></div>
            <div class="go-drow"><span>Status</span><strong style="color:${payColor}">${cap(tx.status)}</strong></div>
            ${tx.notes ? `<div class="go-drow" style="flex-direction:column;align-items:flex-start;gap:4px"><span>Notes</span><p style="font-size:13px;color:#374151;line-height:1.5;margin-top:4px">${tx.notes}</p></div>` : ''}
        </div>`;

    openModal('detailModal', 'detailBackdrop');
};

/*  walk-in modal  */
async function openWalkInModal() {
    openModal('walkInModal', 'walkInBackdrop');
    document.getElementById('wiDate').value = new Date().toISOString().split('T')[0];

    const sel = document.getElementById('wiGround');
    if (sel.options.length <= 1) {
        try {
            const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/grounds');
            const data = await res.json();
            (data.grounds || []).forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id;
                opt.textContent = g.name + (g.city ? ` (${g.city})` : '');
                sel.appendChild(opt);
            });
        } catch (e) { console.error('Ground load error:', e); }
    }
}

document.getElementById('walkInForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('wiSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    const payload = {
        facility_id:    document.getElementById('wiGround').value,
        amount:         parseFloat(document.getElementById('wiAmount').value),
        earning_date:   document.getElementById('wiDate').value,
        payment_method: document.getElementById('wiPayMethod').value,
        notes:          document.getElementById('wiNotes').value.trim() || null
    };

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/earnings/walk-in', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
            toast('Walk-in transaction saved', 'success');
            closeModal('walkInModal', 'walkInBackdrop');
            document.getElementById('walkInForm').reset();
            await loadStats();
            await loadTransactions();
        } else {
            toast(data.message || 'Failed to save transaction', 'error');
        }
    } catch (err) {
        toast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Save Transaction';
    }
});

/*  export CSV  */
document.getElementById('exportBtn').addEventListener('click', () => {
    if (!filtered.length) { toast('No transactions to export', 'error'); return; }

    const rows = [
        ['#', 'Date', 'Ground', 'Notes', 'Method', 'Amount (LKR)', 'Commission (LKR)', 'Net (LKR)', 'Status'],
        ...filtered.map(tx => [
            tx.id,
            fmtDate(tx.date || tx.created_at),
            tx.ground || '',
            tx.notes || tx.customer || '',
            (tx.payment_method || 'online').replace('_', ' '),
            (parseFloat(tx.amount) || 0).toFixed(2),
            (parseFloat(tx.fee) || 0).toFixed(2),
            (parseFloat(tx.net) || 0).toFixed(2),
            tx.status || ''
        ])
    ];

    const csv  = rows.map(r => r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url;
    a.download = `earnings-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    toast('Export downloaded', 'success');
});

/*  modal helpers  */
function openModal(modalId, backdropId) {
    document.getElementById(backdropId).classList.add('open');
    document.getElementById(modalId).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId, backdropId) {
    document.getElementById(backdropId).classList.remove('open');
    document.getElementById(modalId).classList.remove('open');
    document.body.style.overflow = '';
}

window.openWalkInModal = openWalkInModal;

document.getElementById('closeDetail').addEventListener('click',  () => closeModal('detailModal',  'detailBackdrop'));
document.getElementById('detailBackdrop').addEventListener('click', () => closeModal('detailModal', 'detailBackdrop'));
document.getElementById('closeWalkIn').addEventListener('click',   () => closeModal('walkInModal',  'walkInBackdrop'));
document.getElementById('cancelWalkIn').addEventListener('click',  () => closeModal('walkInModal',  'walkInBackdrop'));
document.getElementById('walkInBackdrop').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeModal('walkInModal', 'walkInBackdrop'); });

/*  sidebar toggle  */
window.toggleSidebar = function () {
    document.getElementById('adminSidebar')?.classList.toggle('active');
};

/*  filter events  */
document.getElementById('applyFilters').addEventListener('click', applyFilters);
document.getElementById('sortBy').addEventListener('change', applyFilters);
document.getElementById('prevPage').addEventListener('click', () => { if (page > 1) { page--; renderTable(); } });
document.getElementById('nextPage').addEventListener('click', () => {
    const total = Math.ceil(filtered.length / PER_PAGE);
    if (page < total) { page++; renderTable(); }
});

/*  init  */
loadStats();
loadTransactions();

})();
