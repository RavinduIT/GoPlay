/* 
   Ground Owner – Booking Management JS
 */
(function () {
'use strict';

/*  helpers  */
function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function cap(s) {
    if (!s) return '—';
    return s.charAt(0).toUpperCase() + s.slice(1);
}

/*  toast  */
function toast(msg, type = 'info') {
    let wrap = document.getElementById('bkToastWrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'bkToastWrap';
        wrap.className = 'bk-toast-wrap';
        document.body.appendChild(wrap);
    }
    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };
    const t = document.createElement('div');
    t.className = `bk-toast ${type}`;
    t.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${msg}`;
    wrap.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 320); }, 3500);
}

/*  modal  */
function openModal(id, backdropId) {
    document.getElementById(backdropId).classList.add('open');
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id, backdropId) {
    document.getElementById(backdropId).classList.remove('open');
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

/*  state  */
let allBookings = [];
let page        = 1;
const PER_PAGE  = 15;
let pendingCancelId  = null;
let pendingStatusId  = null;
let pendingStatusVal = null;
let pendingPaymentBookingId  = null;
let pendingPaymentFacilityId = null;

/*  load stats  */
async function loadStats() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/dashboard-stats');
        const data = await res.json();
        if (!data.success) return;
        const b = data.stats?.bookings || {};
        document.getElementById('statTotal').textContent     = b.total_bookings     ?? '—';
        document.getElementById('statToday').textContent     = b.today_bookings     ?? '—';
        document.getElementById('statUpcoming').textContent  = b.upcoming_bookings  ?? '—';
        document.getElementById('statCompleted').textContent = b.completed_bookings ?? '—';
    } catch (e) { console.error('Stats error:', e); }
}

/*  load facilities for filter  */
async function loadFacilities() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/grounds');
        const data = await res.json();
        const sel  = document.getElementById('filterFacility');
        (data.grounds || []).forEach(f => {
            const opt = document.createElement('option');
            opt.value = f.id;
            opt.textContent = f.name + (f.city ? ` (${f.city})` : '');
            sel.appendChild(opt);
        });
    } catch (e) { console.error('Facilities error:', e); }
}

/*  load bookings  */
async function loadBookings() {
    const params = new URLSearchParams();
    const status   = document.getElementById('filterStatus').value;
    const facility = document.getElementById('filterFacility').value;
    const search   = document.getElementById('filterSearch').value.trim();
    const start    = document.getElementById('filterStart').value;
    const end      = document.getElementById('filterEnd').value;

    if (status)   params.set('status', status);
    if (facility) params.set('facility_id', facility);
    if (search)   params.set('user_search', search);
    if (start)    params.set('start_date', start);
    if (end)      params.set('end_date', end);

    document.getElementById('tableLoading').style.display   = 'block';
    document.getElementById('tableContainer').style.display = 'none';
    document.getElementById('noData').style.display         = 'none';
    document.getElementById('pagination').style.display     = 'none';
    document.getElementById('resultsLabel').textContent     = '';

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/bookings?' + params.toString());
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed');
        allBookings = data.bookings || [];
        page = 1;
        renderTable();
    } catch (e) {
        console.error('Bookings error:', e);
        document.getElementById('tableLoading').innerHTML =
            '<i class="fas fa-exclamation-circle"></i> Failed to load bookings';
    }
}

/*  render table  */
function renderTable() {
    const loading   = document.getElementById('tableLoading');
    const container = document.getElementById('tableContainer');
    const noData    = document.getElementById('noData');
    const pag       = document.getElementById('pagination');
    const label     = document.getElementById('resultsLabel');

    loading.style.display = 'none';

    if (!allBookings.length) {
        container.style.display = 'none';
        noData.style.display    = 'block';
        pag.style.display       = 'none';
        label.textContent       = '0 records';
        return;
    }

    noData.style.display    = 'none';
    container.style.display = 'block';

    const total      = allBookings.length;
    const totalPages = Math.ceil(total / PER_PAGE);
    const startIdx   = (page - 1) * PER_PAGE;
    const rows       = allBookings.slice(startIdx, startIdx + PER_PAGE);

    label.textContent = total + ' booking' + (total !== 1 ? 's' : '');

    const statusIcons = {
        pending:   'fa-clock',
        confirmed: 'fa-check-circle',
        completed: 'fa-check',
        cancelled: 'fa-ban',
        no_show:   'fa-user-slash'
    };

    document.getElementById('tableBody').innerHTML = rows.map(b => {
        const name   = `${b.first_name || ''} ${b.last_name || ''}`.trim() || 'Unknown';
        const status = b.status || 'pending';
        const icon   = statusIcons[status] || 'fa-circle';

        return `
        <tr>
            <td><strong style="color:#2563eb">#${b.id}</strong></td>
            <td>
                <strong>${name}</strong>
                <div class="bk-cell-sub">${b.email || ''}</div>
            </td>
            <td>
                <strong>${b.facility_name || '—'}</strong>
                ${b.sport_name ? `<div class="bk-cell-sub"><i class="fas fa-tag"></i> ${b.sport_name}</div>` : ''}
            </td>
            <td><strong>${fmtDate(b.booking_date)}</strong></td>
            <td>${b.start_time || '—'} – ${b.end_time || '—'}</td>
            <td>${parseFloat(b.duration_hours || 0).toFixed(1)}h</td>
            <td>
                <span class="bk-status ${status}">
                    <i class="fas ${icon}"></i> ${cap(status)}
                </span>
            </td>
            <td>
                <div class="bk-actions">${buildActions(b)}</div>
            </td>
        </tr>`;
    }).join('');

    if (totalPages <= 1) {
        pag.style.display = 'none';
    } else {
        pag.style.display = 'flex';
        document.getElementById('pageInfo').textContent = `Page ${page} of ${totalPages}`;
        document.getElementById('prevPage').disabled = page <= 1;
        document.getElementById('nextPage').disabled = page >= totalPages;
    }
}

function buildActions(b) {
    const id = b.id;
    const s  = b.status;
    let html = `<button class="bk-tbl-eye" title="View details" onclick="viewBooking(${id})"><i class="fas fa-eye"></i></button>`;

    if (s === 'pending') {
        html += `<button class="bk-action-confirm" onclick="triggerStatus(${id},'confirmed')">
                    <i class="fas fa-check"></i> Confirm</button>`;
    }
    if (s === 'confirmed') {
        html += `<button class="bk-action-complete" onclick="triggerStatus(${id},'completed')">
                    <i class="fas fa-check"></i> Complete</button>`;
    }
    if (s !== 'cancelled' && s !== 'completed') {
        html += `<button class="bk-action-cancel" onclick="triggerCancel(${id})">
                    <i class="fas fa-times"></i> Cancel</button>`;
    }

    return html;
}

/*  view detail modal  */
window.viewBooking = function (id) {
    const b = allBookings.find(x => x.id == id);
    if (!b) return;

    const name   = `${b.first_name || ''} ${b.last_name || ''}`.trim() || 'Unknown';
    const status = b.status || 'pending';

    document.getElementById('detailContent').innerHTML = `
        <div class="bk-detail-grid">
            <div class="bk-d-section">Booking Info</div>
            <div class="bk-drow"><span class="bk-dlabel">Booking ID</span><span class="bk-dval" style="color:#2563eb">#${b.id}</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Status</span>
                <span class="bk-dval"><span class="bk-status ${status}">${cap(status)}</span></span></div>
            <div class="bk-drow"><span class="bk-dlabel">Date</span><span class="bk-dval">${fmtDate(b.booking_date)}</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Time</span><span class="bk-dval">${b.start_time || '—'} – ${b.end_time || '—'}</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Duration</span><span class="bk-dval">${parseFloat(b.duration_hours || 0).toFixed(1)} hours</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Ground</span><span class="bk-dval">${b.facility_name || '—'}</span></div>
            ${b.sport_name ? `<div class="bk-drow"><span class="bk-dlabel">Sport</span><span class="bk-dval">${b.sport_name}</span></div>` : ''}

            <div class="bk-d-section">Customer Info</div>
            <div class="bk-drow"><span class="bk-dlabel">Name</span><span class="bk-dval">${name}</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Email</span><span class="bk-dval">${b.email || '—'}</span></div>
            <div class="bk-drow"><span class="bk-dlabel">Phone</span><span class="bk-dval">${b.phone || '—'}</span></div>
            ${b.special_requests ? `<div class="bk-drow" style="flex-direction:column;gap:6px">
                <span class="bk-dlabel">Special Requests</span>
                <span class="bk-dval note">${b.special_requests}</span></div>` : ''}
        </div>`;

    let footerHtml = `<button class="bk-btn bk-btn-ghost" onclick="closeModal('detailModal','detailBackdrop')">Close</button>`;
    if (b.status === 'pending')
        footerHtml += `<button class="bk-btn bk-btn-confirm" onclick="closeModal('detailModal','detailBackdrop');triggerStatus(${b.id},'confirmed')"><i class="fas fa-check"></i> Confirm</button>`;
    if (b.status === 'confirmed')
        footerHtml += `<button class="bk-btn bk-btn-success" onclick="closeModal('detailModal','detailBackdrop');triggerStatus(${b.id},'completed')"><i class="fas fa-check"></i> Complete</button>`;
    if (b.status !== 'cancelled' && b.status !== 'completed')
        footerHtml += `<button class="bk-btn bk-btn-danger" onclick="closeModal('detailModal','detailBackdrop');triggerCancel(${b.id})"><i class="fas fa-ban"></i> Cancel</button>`;

    document.getElementById('detailFooter').innerHTML = footerHtml;
    openModal('detailModal', 'detailBackdrop');
};

/*  status change flow  */
window.triggerStatus = function (id, newStatus) {
    pendingStatusId  = id;
    pendingStatusVal = newStatus;

    if (newStatus === 'completed') {
        // Go straight to payment confirmation — skip the generic status modal
        const b = allBookings.find(x => x.id == id);
        pendingPaymentBookingId  = id;
        pendingPaymentFacilityId = b ? b.facility_id : null;

        document.getElementById('payConfirmBookingId').textContent = '#' + id;
        document.getElementById('payConfirmAmount').value = parseFloat(b?.total_amount || 0).toFixed(2);
        document.getElementById('payConfirmMethod').value = b && b.payment_status === 'paid' ? 'online' : 'cash';

        openModal('payConfirmModal', 'payConfirmBackdrop');
        return;
    }

    document.getElementById('statusModalTitle').innerHTML =
        '<i class="fas fa-check"></i> Confirm Booking';
    document.getElementById('statusModalDesc').innerHTML =
        'Mark booking <strong>#' + id + '</strong> as <strong>' + cap(newStatus) + '</strong>?';

    const btn = document.getElementById('confirmStatusBtn');
    btn.className = 'bk-btn bk-btn-confirm';
    btn.innerHTML = '<i class="fas fa-check"></i> ' + cap(newStatus);

    openModal('statusModal', 'statusBackdrop');
};

document.getElementById('confirmStatusBtn').addEventListener('click', async () => {
    if (!pendingStatusId || !pendingStatusVal) return;
    const id = pendingStatusId; const val = pendingStatusVal;
    closeModal('statusModal', 'statusBackdrop');

    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/bookings/${id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: val })
        });
        const data = await res.json();
        if (data.success) {
            toast('Booking #' + id + ' marked as ' + val, 'success');
            await loadStats();
            await loadBookings();
        } else {
            toast(data.message || 'Update failed', 'error');
        }
    } catch (e) {
        toast('Network error. Please try again.', 'error');
    } finally {
        pendingStatusId = null; pendingStatusVal = null;
    }
});

/*  payment confirmation flow  */
async function markBookingComplete(bookingId) {
    const res  = await fetch((window.BASE_URL||'') + '/api/ground-owner/bookings/' + bookingId + '/status', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: 'completed' })
    });
    return res.json();
}

async function cleanupPayModal() {
    pendingPaymentBookingId  = null;
    pendingPaymentFacilityId = null;
    pendingStatusId  = null;
    pendingStatusVal = null;
    await loadStats();
    await loadBookings();
}

document.getElementById('confirmPayBtn').addEventListener('click', async () => {
    const id     = pendingPaymentBookingId;
    const amount = parseFloat(document.getElementById('payConfirmAmount').value);
    const method = document.getElementById('payConfirmMethod').value;

    if (!id || isNaN(amount) || amount <= 0) {
        document.getElementById('payConfirmAmount').style.borderColor = '#dc2626';
        document.getElementById('payConfirmAmount').focus();
        return;
    }
    document.getElementById('payConfirmAmount').style.borderColor = '';

    const btn = document.getElementById('confirmPayBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recording…';

    try {
        const res  = await fetch((window.BASE_URL||'') + '/api/ground-owner/bookings/' + id + '/confirm-payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ amount: amount, payment_method: method })
        });
        const data = await res.json();

        closeModal('payConfirmModal', 'payConfirmBackdrop');

        if (data.success) {
            toast('Booking #' + id + ' completed — Rs. ' + parseFloat(amount).toLocaleString() + ' recorded to earnings.', 'success');
        } else {
            toast(data.message || 'Failed to record payment', 'error');
        }
    } catch (e) {
        closeModal('payConfirmModal', 'payConfirmBackdrop');
        toast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-money-bill-wave"></i> Complete &amp; Record Payment';
        await cleanupPayModal();
    }
});

document.getElementById('completeNoPayBtn').addEventListener('click', async () => {
    const id = pendingPaymentBookingId;
    closeModal('payConfirmModal', 'payConfirmBackdrop');

    try {
        const data = await markBookingComplete(id);
        if (data.success) {
            toast('Booking #' + id + ' marked as completed.', 'success');
        } else {
            toast(data.message || 'Failed to complete booking', 'error');
        }
    } catch (e) {
        toast('Network error. Please try again.', 'error');
    } finally {
        await cleanupPayModal();
    }
});

document.getElementById('closePayConfirmModal').addEventListener('click', () => {
    closeModal('payConfirmModal', 'payConfirmBackdrop');
    pendingPaymentBookingId  = null;
    pendingPaymentFacilityId = null;
    pendingStatusId  = null;
    pendingStatusVal = null;
});

/*  cancel flow  */
window.triggerCancel = function (id) {
    pendingCancelId = id;
    document.getElementById('cancelBookingId').textContent = `#${id}`;
    document.getElementById('cancelReason').value = '';
    document.getElementById('cancelReason').style.borderColor = '';
    openModal('cancelModal', 'cancelBackdrop');
};

document.getElementById('confirmCancelBtn').addEventListener('click', async () => {
    const reason = document.getElementById('cancelReason').value.trim();
    if (!reason) {
        document.getElementById('cancelReason').style.borderColor = '#dc2626';
        document.getElementById('cancelReason').focus();
        return;
    }
    document.getElementById('cancelReason').style.borderColor = '';

    const id = pendingCancelId;
    closeModal('cancelModal', 'cancelBackdrop');

    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/bookings/${id}/cancel`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason })
        });
        const data = await res.json();
        if (data.success) {
            toast(`Booking #${id} cancelled`, 'success');
            await loadStats();
            await loadBookings();
        } else {
            toast(data.message || 'Cancellation failed', 'error');
        }
    } catch (e) {
        toast('Network error. Please try again.', 'error');
    } finally {
        pendingCancelId = null;
    }
});

/*  export CSV  */
document.getElementById('exportBtn').addEventListener('click', () => {
    if (!allBookings.length) { toast('No bookings to export', 'error'); return; }

    const rows = [
        ['#', 'Customer', 'Email', 'Phone', 'Ground', 'Date', 'Start', 'End', 'Duration (h)', 'Status'],
        ...allBookings.map(b => [
            b.id,
            `${b.first_name || ''} ${b.last_name || ''}`.trim(),
            b.email || '', b.phone || '',
            b.facility_name || '',
            b.booking_date || '',
            b.start_time || '', b.end_time || '',
            parseFloat(b.duration_hours || 0).toFixed(1),
            b.status || ''
        ])
    ];

    const csv  = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url;
    a.download = `bookings-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a); a.click();
    document.body.removeChild(a); URL.revokeObjectURL(url);
    toast('Export downloaded', 'success');
});

/*  modal close wiring  */
window.closeModal = closeModal;

document.getElementById('closeDetail').addEventListener('click',       () => closeModal('detailModal',  'detailBackdrop'));
document.getElementById('detailBackdrop').addEventListener('click',    () => closeModal('detailModal',  'detailBackdrop'));
document.getElementById('closeCancelModal').addEventListener('click',  () => closeModal('cancelModal',  'cancelBackdrop'));
document.getElementById('closeCancelBtn').addEventListener('click',    () => closeModal('cancelModal',  'cancelBackdrop'));
document.getElementById('cancelBackdrop').addEventListener('click',    () => closeModal('cancelModal',  'cancelBackdrop'));
document.getElementById('closeStatusModal').addEventListener('click',  () => closeModal('statusModal',  'statusBackdrop'));
document.getElementById('closeStatusBtn').addEventListener('click',    () => closeModal('statusModal',  'statusBackdrop'));
document.getElementById('statusBackdrop').addEventListener('click',    () => closeModal('statusModal',  'statusBackdrop'));
document.getElementById('payConfirmBackdrop').addEventListener('click', () => {
    closeModal('payConfirmModal', 'payConfirmBackdrop');
    pendingPaymentBookingId  = null;
    pendingPaymentFacilityId = null;
    pendingStatusId  = null;
    pendingStatusVal = null;
});

/*  filter / pagination events  */
document.getElementById('applyFilters').addEventListener('click', loadBookings);

document.getElementById('clearFilters').addEventListener('click', () => {
    document.getElementById('filterSearch').value   = '';
    document.getElementById('filterFacility').value = '';
    document.getElementById('filterStatus').value   = '';
    document.getElementById('filterStart').value    = '';
    document.getElementById('filterEnd').value      = '';
    loadBookings();
});

document.getElementById('filterSearch').addEventListener('keydown', e => {
    if (e.key === 'Enter') loadBookings();
});

document.getElementById('prevPage').addEventListener('click', () => {
    if (page > 1) { page--; renderTable(); }
});
document.getElementById('nextPage').addEventListener('click', () => {
    if (page < Math.ceil(allBookings.length / PER_PAGE)) { page++; renderTable(); }
});

/*  sidebar  */
window.toggleSidebar = function () {
    document.getElementById('adminSidebar')?.classList.toggle('active');
};

/*  init  */
loadStats();
loadFacilities();
loadBookings();

})();
