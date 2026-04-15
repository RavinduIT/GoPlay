<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title         = 'My Coach Sessions - GoPlay';
$additionalCSS = [];
$additionalJS  = [];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
    --primary:#2563eb;--primary-dk:#1d4ed8;--success:#10b981;--warn:#f59e0b;
    --danger:#ef4444;--bg:#f1f5f9;--card:#fff;--border:#e2e8f0;
    --txt:#1e293b;--txt-2:#64748b;--radius:14px;--shadow:0 4px 20px rgba(0,0,0,.08);
    --transition:all .25s ease;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:var(--txt);background:var(--bg)}

/* Page container */
.sessions-page{max-width:1100px;margin:2rem auto;padding:0 1.5rem}

/* Header */
.page-hdr{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.page-hdr h1{font-size:1.8rem;font-weight:800;display:flex;align-items:center;gap:.6rem}
.page-hdr p{color:var(--txt-2);margin-top:.3rem;font-size:.95rem}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.7rem 1.4rem;border-radius:10px;font-weight:600;font-size:.9rem;cursor:pointer;transition:var(--transition);border:none;text-decoration:none}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-dk)}
.btn-outline{background:#fff;color:var(--primary);border:2px solid var(--primary)}
.btn-outline:hover{background:var(--primary);color:#fff}
.btn-sm{padding:.4rem .9rem;font-size:.82rem}
.btn-danger-outline{background:#fff;color:var(--danger);border:2px solid var(--danger)}
.btn-danger-outline:hover{background:var(--danger);color:#fff}

/* Stats */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:2rem}
@media(max-width:700px){.stats-row{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:var(--card);border-radius:var(--radius);padding:1.2rem;box-shadow:var(--shadow);display:flex;align-items:center;gap:1rem}
.stat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
.stat-val{font-size:1.5rem;font-weight:700}
.stat-lbl{font-size:.78rem;color:var(--txt-2);margin-top:.1rem}

/* Filters */
.filters{background:var(--card);border-radius:var(--radius);padding:1rem 1.5rem;box-shadow:var(--shadow);margin-bottom:1.5rem;display:flex;gap:.8rem;flex-wrap:wrap;align-items:center}
.filter-btn{padding:.45rem 1rem;border:2px solid var(--border);border-radius:2rem;font-size:.83rem;cursor:pointer;background:#fff;color:var(--txt-2);transition:var(--transition);font-weight:500}
.filter-btn.active{border-color:var(--primary);background:var(--primary);color:#fff}

/* Booking Cards */
.bookings-list{display:flex;flex-direction:column;gap:1rem}
.booking-card{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;transition:var(--transition)}
.booking-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.12)}
.booking-card-inner{display:grid;grid-template-columns:auto 1fr auto;gap:1.5rem;padding:1.5rem;align-items:start}
@media(max-width:600px){.booking-card-inner{grid-template-columns:1fr;gap:1rem}}

/* Status stripe */
.status-stripe{width:5px;border-radius:3px;align-self:stretch;flex-shrink:0}
.stripe-confirmed{background:var(--success)}
.stripe-pending{background:var(--warn)}
.stripe-completed{background:var(--primary)}
.stripe-cancelled{background:#94a3b8}
.stripe-no_show{background:var(--danger)}

.bc-left{display:flex;gap:1rem;align-items:flex-start}
.coach-avatar{width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0}
.coach-initials{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#1e3a8a,var(--primary));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0}
.bc-info h3{font-size:1rem;font-weight:700;margin-bottom:.2rem}
.bc-info .sport-chip{display:inline-block;background:#dbeafe;color:#1d4ed8;padding:.15rem .6rem;border-radius:2rem;font-size:.75rem;font-weight:500;margin-bottom:.5rem}
.bc-meta{display:flex;flex-wrap:wrap;gap:.5rem 1.2rem;font-size:.83rem;color:var(--txt-2)}
.bc-meta i{color:var(--primary);width:14px}

.status-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .8rem;border-radius:2rem;font-size:.78rem;font-weight:600}
.badge-confirmed{background:#d1fae5;color:#065f46}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-completed{background:#dbeafe;color:#1d4ed8}
.badge-cancelled{background:#f1f5f9;color:#64748b}
.badge-no_show{background:#fee2e2;color:#991b1b}

.bc-right{display:flex;flex-direction:column;align-items:flex-end;gap:.7rem;min-width:130px}
.bc-amount{font-size:1.1rem;font-weight:700;color:var(--primary)}
.bc-actions{display:flex;gap:.5rem}

/* Empty / Loading */
.empty-state,.loading-state{text-align:center;padding:4rem 2rem;color:var(--txt-2)}
.empty-state i,.loading-state i{font-size:2.5rem;margin-bottom:1rem;display:block}
.spinner{display:inline-block;width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/*  Edit Modal  */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9000;display:none;align-items:center;justify-content:center;padding:1rem}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:var(--radius);padding:2rem;max-width:500px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto}
.modal-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem}
.modal-hdr h2{font-size:1.2rem;font-weight:700}
.modal-close{background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--txt-2);padding:.3rem}
.modal-close:hover{color:var(--danger)}
.form-group{margin-bottom:1.2rem}
.form-label{display:block;font-weight:600;font-size:.85rem;margin-bottom:.4rem}
.form-control{width:100%;padding:.75rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:.9rem}
.form-control:focus{outline:none;border-color:var(--primary)}
.slots-grid-modal{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-top:.5rem}
.slot-btn{padding:.5rem .2rem;border:2px solid var(--border);border-radius:8px;font-size:.78rem;cursor:pointer;transition:var(--transition);text-align:center;background:#fff}
.slot-btn:hover:not(.booked){border-color:var(--primary);background:#eff6ff;color:var(--primary)}
.slot-btn.selected{border-color:var(--primary);background:var(--primary);color:#fff}
.slot-btn.booked{background:#f1f5f9;color:#94a3b8;cursor:not-allowed;text-decoration:line-through}
.modal-footer{display:flex;gap:.8rem;justify-content:flex-end;margin-top:1.5rem}

/* Cancel confirm */
.confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9100;display:none;align-items:center;justify-content:center}
.confirm-overlay.open{display:flex}
.confirm-box{background:#fff;border-radius:var(--radius);padding:2rem;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.confirm-icon{font-size:2.5rem;color:var(--danger);margin-bottom:1rem}
.confirm-box h3{font-size:1.1rem;font-weight:700;margin-bottom:.5rem}
.confirm-box p{color:var(--txt-2);font-size:.9rem;margin-bottom:1.5rem}
.confirm-actions{display:flex;gap:.8rem;justify-content:center}

/* Toast */
.toast{position:fixed;bottom:2rem;right:2rem;background:#1e293b;color:#fff;padding:1rem 1.5rem;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.2);z-index:9999;transform:translateY(120px);opacity:0;transition:var(--transition);max-width:320px}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{background:#10b981}
.toast.error{background:#ef4444}
</style>

<div class="sessions-page">
    <!-- Header -->
    <div class="page-hdr">
        <div>
            <h1><i class="fas fa-dumbbell" style="color:var(--primary)"></i> My Coach Sessions</h1>
            <p>Track and manage your personal coaching sessions</p>
        </div>
        <a href="<?= $_base ?>/book-coach" class="btn btn-primary">
            <i class="fas fa-plus"></i> Book New Session
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:#2563eb"><i class="fas fa-calendar-check"></i></div>
            <div><div class="stat-val" id="stat-total">0</div><div class="stat-lbl">Total Bookings</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;color:#10b981"><i class="fas fa-clock"></i></div>
            <div><div class="stat-val" id="stat-upcoming">0</div><div class="stat-lbl">Upcoming</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0e7ff;color:#6366f1"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-val" id="stat-completed">0</div><div class="stat-lbl">Completed</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;color:#f59e0b"><i class="fas fa-rupee-sign"></i></div>
            <div><div class="stat-val" id="stat-spent">LKR 0</div><div class="stat-lbl">Total Spent</div></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="confirmed">Upcoming</button>
        <button class="filter-btn" data-filter="completed">Completed</button>
        <button class="filter-btn" data-filter="cancelled">Cancelled</button>
    </div>

    <!-- Bookings List -->
    <div class="bookings-list" id="bookings-list">
        <div class="loading-state">
            <div class="spinner"></div>
            <p style="margin-top:1rem">Loading your sessions…</p>
        </div>
    </div>
</div>

<!--  Edit Modal  -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal">
        <div class="modal-hdr">
            <h2><i class="fas fa-edit" style="color:var(--primary)"></i> Reschedule Session</h2>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <input type="hidden" id="edit-booking-id">
        <input type="hidden" id="edit-coach-id">
        <div class="form-group">
            <label class="form-label">New Date</label>
            <input type="date" class="form-control" id="edit-date" min="">
        </div>
        <div class="form-group">
            <label class="form-label">Available Time Slots</label>
            <div id="edit-slots-container"><div style="color:var(--txt-2);font-size:.9rem">Select a date first</div></div>
            <input type="hidden" id="edit-start-time">
            <input type="hidden" id="edit-end-time">
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
            <button class="btn btn-primary" id="btn-save-edit" onclick="saveReschedule()" disabled>
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!--  Cancel Confirm  -->
<div class="confirm-overlay" id="cancel-confirm">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h3>Cancel This Session?</h3>
        <p>This action cannot be undone. The booking will be marked as cancelled.</p>
        <div class="confirm-actions">
            <button class="btn btn-outline" onclick="closeCancelConfirm()">Keep Session</button>
            <button class="btn btn-danger-outline" id="btn-confirm-cancel" onclick="confirmCancel()">Yes, Cancel</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
(function () {
    let allBookings    = [];
    let currentFilter  = 'all';
    let cancelBookingId = null;
    let editSelectedStart = '', editSelectedEnd = '';

    //  Toast 
    function showToast(msg, type = 'info') {
        const t   = document.getElementById('toast');
        t.textContent = msg;
        t.className   = 'toast show ' + type;
        setTimeout(() => (t.className = 'toast'), 3500);
    }

    //  Load bookings 
    async function loadBookings() {
        try {
            const res  = await fetch((window.BASE_URL||'')+'/api/user/coach-bookings');
            const data = await res.json();

            if (!data.success) throw new Error(data.error || 'Failed');
            allBookings = data.bookings || [];
            renderStats(allBookings);
            renderBookings(allBookings, currentFilter);
        } catch (e) {
            document.getElementById('bookings-list').innerHTML =
                '<div class="empty-state"><i class="fas fa-exclamation-circle" style="color:var(--danger)"></i><p>' + e.message + '</p></div>';
        }
    }

    function renderStats(bookings) {
        const upcoming  = bookings.filter(b => ['confirmed','pending'].includes(b.status) && b.booking_date >= today()).length;
        const completed = bookings.filter(b => b.status === 'completed').length;
        const spent     = bookings.filter(b => b.status === 'completed').reduce((s, b) => s + parseFloat(b.total_amount || 0), 0);

        document.getElementById('stat-total').textContent     = bookings.length;
        document.getElementById('stat-upcoming').textContent  = upcoming;
        document.getElementById('stat-completed').textContent = completed;
        document.getElementById('stat-spent').textContent     = 'LKR ' + spent.toLocaleString();
    }

    function today() { return new Date().toISOString().split('T')[0]; }

    function renderBookings(bookings, filter) {
        let filtered = bookings;
        if (filter === 'confirmed') {
            filtered = bookings.filter(b => ['confirmed','pending'].includes(b.status));
        } else if (filter !== 'all') {
            filtered = bookings.filter(b => b.status === filter);
        }

        const el = document.getElementById('bookings-list');

        if (!filtered.length) {
            el.innerHTML = `<div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No ${filter === 'all' ? '' : filter} sessions found.</p>
                <a href="<?= $_base ?>/book-coach" class="btn btn-primary" style="margin-top:1rem;display:inline-flex">
                    <i class="fas fa-plus"></i> Book a Session
                </a>
            </div>`;
            return;
        }

        el.innerHTML = filtered.map(b => buildCard(b)).join('');
    }

    function buildCard(b) {
        const coachName    = (b.coach_first_name || '') + ' ' + (b.coach_last_name || '');
        const initial      = coachName.trim().charAt(0).toUpperCase();
        const avatarHtml   = b.coach_avatar
            ? `<img src="${b.coach_avatar}" class="coach-avatar" alt="${coachName}">`
            : `<div class="coach-initials">${initial}</div>`;

        const dateStr  = new Date(b.booking_date).toLocaleDateString('en-GB',{weekday:'short',day:'numeric',month:'short',year:'numeric'});
        const timeStr  = formatTime(b.start_time) + ' – ' + formatTime(b.end_time);
        const canEdit  = ['confirmed','pending'].includes(b.status) && b.booking_date >= today();
        const canCancel= canEdit;

        const statusBadge = {
            confirmed: '<span class="status-badge badge-confirmed"><i class="fas fa-check-circle"></i> Confirmed</span>',
            pending:   '<span class="status-badge badge-pending"><i class="fas fa-hourglass-half"></i> Pending</span>',
            completed: '<span class="status-badge badge-completed"><i class="fas fa-flag-checkered"></i> Completed</span>',
            cancelled: '<span class="status-badge badge-cancelled"><i class="fas fa-ban"></i> Cancelled</span>',
            no_show:   '<span class="status-badge badge-no_show"><i class="fas fa-user-times"></i> No Show</span>',
        }[b.status] || '';

        const stripeClass = 'stripe-' + b.status;

        return `
        <div class="booking-card" id="card-${b.id}">
            <div class="booking-card-inner">
                <div style="display:flex;gap:1rem;align-items:flex-start;flex:1">
                    <div class="${stripeClass}" style="width:5px;border-radius:3px;align-self:stretch"></div>
                    ${avatarHtml}
                    <div class="bc-info">
                        <h3>${coachName}</h3>
                        <span class="sport-chip">${b.sport_name || 'General'}</span>
                        <div class="bc-meta">
                            <span><i class="fas fa-calendar"></i> ${dateStr}</span>
                            <span><i class="fas fa-clock"></i> ${timeStr}</span>
                            <span><i class="fas fa-user-tag"></i> ${capitalize(b.session_type || 'individual')}</span>
                            <span><i class="fas fa-map-marker-alt"></i> ${b.coach_location || 'N/A'}</span>
                        </div>
                        ${b.special_requests ? `<div style="margin-top:.5rem;font-size:.82rem;color:var(--txt-2);background:var(--bg);padding:.4rem .7rem;border-radius:6px"><i class="fas fa-comment-dots"></i> ${b.special_requests}</div>` : ''}
                    </div>
                </div>
                <div class="bc-right">
                    ${statusBadge}
                    <div class="bc-amount">LKR ${parseFloat(b.total_amount).toLocaleString()}</div>
                    ${canEdit || canCancel ? `
                    <div class="bc-actions">
                        ${canEdit   ? `<button class="btn btn-outline btn-sm" onclick="openEditModal(${b.id},${b.coach_id},'${b.booking_date}')"><i class="fas fa-edit"></i> Edit</button>` : ''}
                        ${canCancel ? `<button class="btn btn-danger-outline btn-sm" onclick="openCancelConfirm(${b.id})"><i class="fas fa-times"></i></button>` : ''}
                    </div>` : ''}
                </div>
            </div>
        </div>`;
    }

    function formatTime(t) {
        if (!t) return '';
        const [h, m] = t.split(':').map(Number);
        const ampm = h >= 12 ? 'PM' : 'AM';
        return ((h % 12) || 12) + ':' + String(m).padStart(2,'0') + ' ' + ampm;
    }
    function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    //  Filter buttons 
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderBookings(allBookings, currentFilter);
        });
    });

    //  Edit Modal 
    window.openEditModal = function (bookingId, coachId, currentDate) {
        document.getElementById('edit-booking-id').value = bookingId;
        document.getElementById('edit-coach-id').value   = coachId;
        document.getElementById('edit-date').value       = '';
        document.getElementById('edit-date').min         = new Date().toISOString().split('T')[0];
        document.getElementById('edit-slots-container').innerHTML = '<div style="color:var(--txt-2);font-size:.9rem">Select a date first</div>';
        document.getElementById('edit-start-time').value = '';
        document.getElementById('edit-end-time').value   = '';
        document.getElementById('btn-save-edit').disabled = true;
        editSelectedStart = ''; editSelectedEnd = '';
        document.getElementById('edit-modal').classList.add('open');
    };

    window.closeEditModal = function () {
        document.getElementById('edit-modal').classList.remove('open');
    };

    document.getElementById('edit-date').addEventListener('change', async e => {
        const date    = e.target.value;
        const coachId = document.getElementById('edit-coach-id').value;
        if (!date || !coachId) return;

        const cont = document.getElementById('edit-slots-container');
        cont.innerHTML = '<div style="color:var(--txt-2);font-size:.9rem"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
        editSelectedStart = ''; editSelectedEnd = '';
        document.getElementById('btn-save-edit').disabled = true;

        try {
            const res  = await fetch(`${window.BASE_URL||""}/api/coaches/${coachId}/availability?date=${date}`);
            const data = await res.json();
            if (!data.success || !data.slots.length) {
                cont.innerHTML = '<div style="color:var(--txt-2);font-size:.9rem">No slots available for this date.</div>';
                return;
            }
            const grid = document.createElement('div');
            grid.className = 'slots-grid-modal';
            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type      = 'button';
                btn.className = 'slot-btn' + (slot.available ? '' : ' booked');
                btn.textContent = slot.label.split('–')[0].trim();
                btn.title       = slot.label;
                if (slot.available) {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.slots-grid-modal .slot-btn').forEach(b => b.classList.remove('selected'));
                        btn.classList.add('selected');
                        editSelectedStart = slot.start_time;
                        editSelectedEnd   = slot.end_time;
                        document.getElementById('edit-start-time').value = slot.start_time;
                        document.getElementById('edit-end-time').value   = slot.end_time;
                        document.getElementById('btn-save-edit').disabled = false;
                    });
                } else {
                    btn.disabled = true;
                }
                grid.appendChild(btn);
            });
            cont.innerHTML = '';
            cont.appendChild(grid);
        } catch {
            cont.innerHTML = '<div style="color:var(--danger);font-size:.9rem">Error loading slots.</div>';
        }
    });

    window.saveReschedule = async function () {
        const bookingId = document.getElementById('edit-booking-id').value;
        const date      = document.getElementById('edit-date').value;
        const start     = document.getElementById('edit-start-time').value;
        const end       = document.getElementById('edit-end-time').value;

        if (!date || !start || !end) { showToast('Select a date and time slot.','error'); return; }

        const btn = document.getElementById('btn-save-edit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const res  = await fetch(`${window.BASE_URL||""}/api/user/coach-bookings/${bookingId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ booking_date: date, start_time: start, end_time: end }),
            });
            const data = await res.json();
            if (data.success) {
                showToast('Session rescheduled!', 'success');
                closeEditModal();
                loadBookings();
            } else {
                showToast(data.error || 'Reschedule failed.', 'error');
            }
        } catch { showToast('Network error.','error'); }

        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    };

    //  Cancel 
    window.openCancelConfirm = function (id) {
        cancelBookingId = id;
        document.getElementById('cancel-confirm').classList.add('open');
    };
    window.closeCancelConfirm = function () {
        document.getElementById('cancel-confirm').classList.remove('open');
        cancelBookingId = null;
    };
    window.confirmCancel = async function () {
        if (!cancelBookingId) return;
        const btn = document.getElementById('btn-confirm-cancel');
        btn.disabled  = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        try {
            const res  = await fetch(`${window.BASE_URL||""}/api/user/coach-bookings/${cancelBookingId}/cancel`, { method: 'PUT' });
            const data = await res.json();
            if (data.success) {
                showToast('Booking cancelled.', 'success');
                closeCancelConfirm();
                loadBookings();
            } else {
                showToast(data.error || 'Cancellation failed.', 'error');
            }
        } catch { showToast('Network error.','error'); }
        btn.disabled  = false;
        btn.innerHTML = 'Yes, Cancel';
    };

    //  Init 
    loadBookings();
})();
</script>
