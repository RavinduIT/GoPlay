/**
 * Ground Owner Schedule Dashboard
 * Month / Week / Day calendar with bookings and blocked dates
 */

/* ── Constants ──────────────────────────────────────────────── */
const DAY_NAMES   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const MONTH_NAMES = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
const REASON_META = {
    maintenance : { label: '🔧 Maintenance',   cls: 'maintenance' },
    personal    : { label: '👤 Personal Use',   cls: 'personal'    },
    event       : { label: '🎉 Private Event',  cls: 'event'       },
    weather     : { label: '🌧 Weather',        cls: 'weather'     },
    other       : { label: '📌 Other',          cls: 'other'       }
};

/* ── State ──────────────────────────────────────────────────── */
let currentView        = 'month';
let currentDate        = new Date();
let allBookings        = [];
let facilities         = [];
let blockedDates       = [];
let selectedFacilityId = null;

/* ── Bootstrap ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    bindNavButtons();
    bindViewTabs();
    bindGroundSelector();
    setDefaultDateInputs();
    loadSchedule();
});

function bindNavButtons() {
    document.getElementById('prevPeriod').addEventListener('click', () => navigate(-1));
    document.getElementById('nextPeriod').addEventListener('click', () => navigate(1));
}

function bindViewTabs() {
    document.querySelectorAll('.sc-view-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sc-view-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentView = btn.dataset.view;
            render();
        });
    });
}

function bindGroundSelector() {
    document.getElementById('groundSelector').addEventListener('change', e => {
        selectedFacilityId = e.target.value || null;
        render();
        renderBlockedPanel();
    });
}

function setDefaultDateInputs() {
    const today = fmtDate(new Date());
    ['blockStart','blockEnd'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = today; el.min = today; }
    });
}

/* ── Data ───────────────────────────────────────────────────── */
async function loadSchedule() {
    showLoading(true);
    try {
        // Load a wide date range so navigation works client-side without re-fetching
        const now   = new Date();
        const start = `${now.getFullYear() - 1}-01-01`;
        const end   = `${now.getFullYear() + 2}-12-31`;
        const url   = `/api/ground-owner/schedule?start_date=${start}&end_date=${end}`;

        const res  = await fetch(url);
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Load failed');

        // API wraps everything under data.schedule
        const schedule   = data.schedule || {};
        allBookings  = (schedule.bookings  || []).map(normBooking);
        facilities   = schedule.facilities    || [];
        blockedDates = schedule.blocked_dates || [];

        populateFacilitySelectors();
        updateStats(schedule.stats || {});
        render();
        renderBlockedPanel();
    } catch (err) {
        console.error('Schedule load error:', err);
        showToast('Failed to load schedule', 'error');
    } finally {
        showLoading(false);
    }
}

function populateFacilitySelectors() {
    const mainSel  = document.getElementById('groundSelector');
    const blockSel = document.getElementById('blockGround');

    mainSel.innerHTML  = '<option value="">All Grounds</option>';
    blockSel.innerHTML = '<option value="">Select ground…</option>';

    facilities.forEach(f => {
        // getByOwnerId returns 'id', not 'facility_id'
        const fid = f.facility_id !== undefined ? f.facility_id : f.id;
        const opt = `<option value="${fid}">${escHtml(f.name)}</option>`;
        mainSel.insertAdjacentHTML('beforeend', opt);
        blockSel.insertAdjacentHTML('beforeend', opt);
    });

    if (selectedFacilityId) mainSel.value = selectedFacilityId;
}

function updateStats(stats) {
    // API returns: total_bookings, booked_slots, occupancy_rate
    const total   = stats.total_bookings !== undefined ? stats.total_bookings : 0;
    const active  = stats.booked_slots   !== undefined ? stats.booked_slots   : 0;
    const pending = allBookings.filter(b => b.status === 'pending').length;

    setEl('statTotal',   total);
    setEl('statActive',  active);
    setEl('statPending', pending);

    const today = new Date(); today.setHours(0,0,0,0);
    const futureBlocked = blockedDates.filter(b => new Date(b.end_date + 'T00:00:00') >= today).length;
    setEl('statBlocked', futureBlocked);

    setEl('statOccupancy', (stats.occupancy_rate || 0) + '%');
}

/* ── Filtered sets ──────────────────────────────────────────── */
function filteredBookings() {
    if (!selectedFacilityId) return allBookings;
    return allBookings.filter(b => String(b.facility_id) === String(selectedFacilityId));
}

// Normalise a booking so JS always uses consistent field names
function normBooking(b) {
    var name = b.user_name || (((b.first_name || '') + ' ' + (b.last_name || '')).trim()) || '—';
    return Object.assign({}, b, {
        booking_id: b.booking_id !== undefined ? b.booking_id : b.id,
        user_name:  name,
        user_email: b.user_email || b.email  || null,
        user_phone: b.user_phone || b.phone  || null,
    });
}

function filteredBlocked() {
    if (!selectedFacilityId) return blockedDates;
    return blockedDates.filter(b => String(b.facility_id) === String(selectedFacilityId));
}

/* ── Navigation ─────────────────────────────────────────────── */
function navigate(dir) {
    if (currentView === 'month') {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + dir, 1);
    } else if (currentView === 'week') {
        currentDate = new Date(currentDate.getTime() + dir * 7 * 86400000);
    } else {
        currentDate = new Date(currentDate.getTime() + dir * 86400000);
    }
    render();
}

function goToToday() {
    currentDate = new Date();
    render();
}

/* ── Render dispatcher ──────────────────────────────────────── */
function render() {
    document.getElementById('viewMonth').style.display = currentView === 'month' ? '' : 'none';
    document.getElementById('viewWeek').style.display  = currentView === 'week'  ? '' : 'none';
    document.getElementById('viewDay').style.display   = currentView === 'day'   ? '' : 'none';

    if      (currentView === 'month') renderMonth();
    else if (currentView === 'week')  renderWeek();
    else                              renderDay();
}

/* ════════════════════════════════════════════════════════════
   MONTH VIEW
═══════════════════════════════════════════════════════════════ */
function renderMonth() {
    const y = currentDate.getFullYear();
    const m = currentDate.getMonth();
    setLabel(`${MONTH_NAMES[m]} ${y}`);

    const firstWeekday = new Date(y, m, 1).getDay();
    const daysInMonth  = new Date(y, m + 1, 0).getDate();
    const todayStr     = fmtDate(new Date());
    const bookings     = filteredBookings();
    const blocked      = filteredBlocked();

    let html = '<div class="sc-month-grid">';

    /* Day name headers */
    DAY_NAMES.forEach(d => {
        html += `<div class="sc-month-day-header">${d}</div>`;
    });

    /* Leading blank cells (prev month) */
    for (let i = 0; i < firstWeekday; i++) {
        const prevD = new Date(y, m, i - firstWeekday + 1);
        html += `<div class="sc-month-cell other-month">
            <div class="sc-cell-top"><span class="sc-cell-num">${prevD.getDate()}</span></div>
        </div>`;
    }

    /* Current month cells */
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr   = `${y}-${pad(m+1)}-${pad(d)}`;
        const isToday   = dateStr === todayStr;
        const isBlocked = isDayBlocked(dateStr, blocked);
        const dayBook   = bookings.filter(b => b.booking_date === dateStr);

        let cls = 'sc-month-cell';
        if (isToday)   cls += ' is-today';
        if (isBlocked) cls += ' is-blocked';

        html += `<div class="${cls}" onclick="navigateToDay('${dateStr}')">
            <div class="sc-cell-top">
                <span class="sc-cell-num">${d}</span>
                ${isBlocked ? '<span class="sc-blocked-badge">Blocked</span>' : ''}
            </div>`;

        const MAX = 3;
        dayBook.slice(0, MAX).forEach(b => {
            html += `<div class="sc-booking-pill ${b.status}"
                onclick="event.stopPropagation();showBookingModal(${b.booking_id})"
                title="${escHtml(b.facility_name)} · ${formatTime12(b.start_time)}–${formatTime12(b.end_time)}">
                ${formatTime12(b.start_time)} ${escHtml(truncate(b.facility_name, 10))}
            </div>`;
        });
        if (dayBook.length > MAX) {
            html += `<div class="sc-more-pill">+${dayBook.length - MAX} more</div>`;
        }

        html += '</div>';
    }

    /* Trailing blank cells (next month) */
    const totalCells = firstWeekday + daysInMonth;
    const trailing   = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= trailing; i++) {
        html += `<div class="sc-month-cell other-month">
            <div class="sc-cell-top"><span class="sc-cell-num">${i}</span></div>
        </div>`;
    }

    html += '</div>';
    document.getElementById('viewMonth').innerHTML = html;
}

function navigateToDay(dateStr) {
    currentDate = new Date(dateStr + 'T00:00:00');
    currentView = 'day';
    document.querySelectorAll('.sc-view-tab').forEach(b => {
        b.classList.toggle('active', b.dataset.view === 'day');
    });
    render();
}

/* ════════════════════════════════════════════════════════════
   WEEK VIEW
═══════════════════════════════════════════════════════════════ */
function renderWeek() {
    const weekStart = getWeekStart(currentDate);
    const weekEnd   = new Date(weekStart.getTime() + 6 * 86400000);
    const todayStr  = fmtDate(new Date());
    const bookings  = filteredBookings();
    const blocked   = filteredBlocked();

    const s = weekStart, e = weekEnd;
    setLabel(`${MONTH_NAMES[s.getMonth()].slice(0,3)} ${s.getDate()} – ${MONTH_NAMES[e.getMonth()].slice(0,3)} ${e.getDate()}, ${e.getFullYear()}`);

    /* Build 7-day array */
    const days = Array.from({ length: 7 }, (_, i) => {
        const d = new Date(weekStart.getTime() + i * 86400000);
        return { date: d, dateStr: fmtDate(d) };
    });

    /* Hours shown: 6 AM – 11 PM */
    const hours = Array.from({ length: 18 }, (_, i) => i + 6);

    let html = '<div class="sc-week-grid">';

    /* Header row */
    html += '<div class="sc-time-header"></div>';
    days.forEach(({ date, dateStr }) => {
        const isToday   = dateStr === todayStr;
        const isBlocked = isDayBlocked(dateStr, blocked);
        let cls = 'sc-week-day-header';
        if (isToday)   cls += ' is-today';
        if (isBlocked) cls += ' is-blocked';
        html += `<div class="${cls}">
            <div class="sc-wdh-name">${DAY_NAMES[date.getDay()]}</div>
            <div class="sc-wdh-num">${date.getDate()}</div>
            ${isBlocked ? '<div class="sc-wdh-blocked">Blocked</div>' : ''}
        </div>`;
    });

    /* Time rows */
    hours.forEach(h => {
        const label = h === 12 ? '12 PM' : h > 12 ? `${h-12} PM` : `${h} AM`;
        html += `<div class="sc-time-col"><div class="sc-time-label">${label}</div></div>`;

        days.forEach(({ dateStr }) => {
            const isBlocked = isDayBlocked(dateStr, blocked);
            let colCls = 'sc-week-col';
            if (isBlocked) colCls += ' is-blocked';

            const slotBook = bookings.filter(b => {
                if (b.booking_date !== dateStr) return false;
                return parseInt(b.start_time.split(':')[0]) === h;
            });

            html += `<div class="${colCls}">
                <div class="sc-week-slot${slotBook.length ? ' has-booking' : ''}">`;

            if (isBlocked && slotBook.length === 0) {
                html += '<div class="sc-week-blocked-x">✕</div>';
            }
            slotBook.forEach(b => {
                html += `<div class="sc-week-booking ${b.status}"
                    onclick="showBookingModal(${b.booking_id})"
                    title="${escHtml(b.facility_name)}: ${formatTime12(b.start_time)}–${formatTime12(b.end_time)}">
                    ${formatTime12(b.start_time)} · ${escHtml(truncate(b.facility_name, 12))}
                </div>`;
            });

            html += '</div></div>';
        });
    });

    html += '</div>';
    document.getElementById('viewWeek').innerHTML = html;
}

function getWeekStart(date) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() - d.getDay());
    return d;
}

/* ════════════════════════════════════════════════════════════
   DAY VIEW
═══════════════════════════════════════════════════════════════ */
function renderDay() {
    const dateStr   = fmtDate(currentDate);
    const blocked   = filteredBlocked();
    const bookings  = filteredBookings().filter(b => b.booking_date === dateStr);
    const isBlocked = isDayBlocked(dateStr, blocked);

    const dayName  = DAY_NAMES[currentDate.getDay()];
    const monthStr = MONTH_NAMES[currentDate.getMonth()];
    const year     = currentDate.getFullYear();
    const dayNum   = currentDate.getDate();

    setLabel(`${dayName}, ${monthStr} ${dayNum}, ${year}`);

    const confirmed = bookings.filter(b => b.status === 'confirmed').length;
    const pending   = bookings.filter(b => b.status === 'pending').length;
    const cancelled = bookings.filter(b => b.status === 'cancelled' || b.status === 'rejected').length;

    let html = `<div class="sc-day-view">
        <div class="sc-day-banner">
            <div>
                <p class="sc-day-banner-title">${dayName}, ${monthStr} ${dayNum}</p>
                <p class="sc-day-banner-sub">${year} · ${bookings.length} booking${bookings.length !== 1 ? 's' : ''}</p>
            </div>
            <div class="sc-day-chips">
                <div class="sc-day-chip">
                    <div class="sc-day-chip-num">${confirmed}</div>
                    <div class="sc-day-chip-lab">Confirmed</div>
                </div>
                <div class="sc-day-chip">
                    <div class="sc-day-chip-num">${pending}</div>
                    <div class="sc-day-chip-lab">Pending</div>
                </div>
                <div class="sc-day-chip">
                    <div class="sc-day-chip-num">${cancelled}</div>
                    <div class="sc-day-chip-lab">Cancelled</div>
                </div>
            </div>
        </div>`;

    if (isBlocked) {
        const bEntry = blocked.find(b => dateStr >= b.start_date && dateStr <= b.end_date);
        const reason = bEntry ? (REASON_META[bEntry.reason]?.label || bEntry.reason) : 'Blocked';
        html += `<div class="sc-blocked-banner">
            <i class="fas fa-ban"></i> ${reason} – This date is blocked for new bookings
        </div>`;
    }

    if (bookings.length === 0) {
        html += `<div class="sc-day-empty">
            <i class="fas fa-calendar-day"></i>
            <h4>No bookings</h4>
            <p>${isBlocked ? 'This day is blocked for new bookings.' : 'No bookings scheduled for this day.'}</p>
        </div>`;
    } else {
        const sorted = [...bookings].sort((a, b) => a.start_time.localeCompare(b.start_time));
        html += '<div class="sc-day-timeline">';
        sorted.forEach(b => {
            html += `<div class="sc-day-booking-card ${b.status}" onclick="showBookingModal(${b.booking_id})">
                <div class="sc-dbc-time">
                    <div class="sc-dbc-start">${formatTime12(b.start_time)}</div>
                    <div class="sc-dbc-to">to</div>
                    <div class="sc-dbc-end">${formatTime12(b.end_time)}</div>
                </div>
                <div class="sc-dbc-body">
                    <div class="sc-dbc-title">
                        ${escHtml(b.facility_name)}
                        <span class="sc-status-badge">${b.status}</span>
                    </div>
                    <div class="sc-dbc-meta">
                        <div class="sc-dbc-meta-item"><i class="fas fa-user"></i>${escHtml(b.user_name || '—')}</div>
                        ${b.user_phone ? `<div class="sc-dbc-meta-item"><i class="fas fa-phone"></i><a href="tel:${escHtml(b.user_phone)}">${escHtml(b.user_phone)}</a></div>` : ''}
                        <div class="sc-dbc-meta-item"><i class="fas fa-tag"></i>Rs. ${Number(b.total_amount).toLocaleString()}</div>
                    </div>
                    ${b.special_requests ? `<div class="sc-dbc-requests"><i class="fas fa-comment-dots"></i> ${escHtml(b.special_requests)}</div>` : ''}
                </div>
            </div>`;
        });
        html += '</div>';
    }

    html += '</div>';
    document.getElementById('viewDay').innerHTML = html;
}

/* ════════════════════════════════════════════════════════════
   BOOKING DETAIL MODAL
═══════════════════════════════════════════════════════════════ */
function showBookingModal(bookingId) {
    const b = allBookings.find(bk => bk.booking_id == bookingId);
    if (!b) return;

    document.getElementById('bookingModalTitle').textContent = `Booking #${b.booking_id}`;
    document.getElementById('bookingModalBody').innerHTML = `
        <div class="sc-modal-body">
            <div class="sc-booking-detail">
                <div class="sc-bd-banner ${b.status}">
                    <div class="sc-bd-time">
                        ${formatTime12(b.start_time)} – ${formatTime12(b.end_time)}
                        <span>· ${fmtDateLong(b.booking_date)}</span>
                    </div>
                    <span class="sc-status-badge">${b.status}</span>
                </div>
                <div class="sc-bd-rows">
                    <div class="sc-bd-row"><i class="fas fa-map-marker-alt"></i><div><strong>${escHtml(b.facility_name)}</strong></div></div>
                    <div class="sc-bd-row"><i class="fas fa-user"></i><div><strong>${escHtml(b.user_name || '—')}</strong></div></div>
                    ${b.user_email ? `<div class="sc-bd-row"><i class="fas fa-envelope"></i><div>${escHtml(b.user_email)}</div></div>` : ''}
                    ${b.user_phone ? `<div class="sc-bd-row"><i class="fas fa-phone"></i><div>${escHtml(b.user_phone)}</div></div>` : ''}
                    <div class="sc-bd-row"><i class="fas fa-tag"></i><div><strong>Rs. ${Number(b.total_amount).toLocaleString()}</strong></div></div>
                    <div class="sc-bd-row"><i class="fas fa-clock"></i><div>${b.booking_date} · ${formatTime12(b.start_time)} – ${formatTime12(b.end_time)}</div></div>
                    ${b.special_requests ? `<div class="sc-bd-row"><i class="fas fa-comment-dots"></i><div>${escHtml(b.special_requests)}</div></div>` : ''}
                    <div class="sc-bd-row"><i class="fas fa-calendar-plus"></i><div>Booked: ${fmtDateLong((b.created_at || '').split(' ')[0])}</div></div>
                </div>
            </div>
        </div>`;

    document.getElementById('bookingModal').style.display = 'flex';
}

function closeBookingModal(e) {
    if (!e || e.target === document.getElementById('bookingModal')) {
        document.getElementById('bookingModal').style.display = 'none';
    }
}

/* ════════════════════════════════════════════════════════════
   BLOCKED DATES PANEL
═══════════════════════════════════════════════════════════════ */
function renderBlockedPanel() {
    const blocked = filteredBlocked();
    const container = document.getElementById('blockedDatesList');
    if (!container) return;

    if (blocked.length === 0) {
        container.innerHTML = `<div class="sc-empty-small">
            ${selectedFacilityId ? 'No blocked dates for this ground.' : 'Select a ground to see blocked dates, or no dates are blocked yet.'}
        </div>`;
        return;
    }

    const today    = new Date(); today.setHours(0, 0, 0, 0);
    const upcoming = blocked.filter(b => new Date(b.end_date + 'T00:00:00') >= today);
    const past     = blocked.filter(b => new Date(b.end_date + 'T00:00:00') <  today);

    let html = `<table class="sc-blocked-table">
        <thead><tr>
            <th>Dates</th>
            <th>Time</th>
            <th>Ground</th>
            <th>Reason</th>
            <th>Notes</th>
            <th></th>
        </tr></thead>
        <tbody>`;

    [...upcoming, ...past].forEach(b => {
        const isPast    = new Date(b.end_date + 'T00:00:00') < today;
        const meta      = REASON_META[b.reason] || { label: b.reason || '—', cls: 'other' };
        const timeStr   = (b.start_time && b.end_time)
            ? `${formatTime12(b.start_time)} – ${formatTime12(b.end_time)}`
            : 'All day';
        var facObj  = facilities.find(function(f) { return String(f.facility_id !== undefined ? f.facility_id : f.id) === String(b.facility_id); });
        const facName   = facObj ? facObj.name : '—';
        const dateRange = b.start_date === b.end_date
            ? fmtDateShort(b.start_date)
            : `${fmtDateShort(b.start_date)} – ${fmtDateShort(b.end_date)}`;

        html += `<tr class="${isPast ? 'is-past' : ''}">
            <td>${dateRange}</td>
            <td>${timeStr}</td>
            <td>${escHtml(facName)}</td>
            <td><span class="sc-reason-chip ${meta.cls}">${meta.label}</span></td>
            <td>${b.notes ? escHtml(b.notes) : '—'}</td>
            <td>${isPast ? '' : `<button class="sc-remove-btn" onclick="removeBlockedDate(${b.id})" title="Remove block"><i class="fas fa-trash-alt"></i></button>`}</td>
        </tr>`;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

/* ════════════════════════════════════════════════════════════
   BLOCK DATES MODAL
═══════════════════════════════════════════════════════════════ */
function openBlockModal() {
    const bSel = document.getElementById('blockGround');
    if (selectedFacilityId) bSel.value = selectedFacilityId;
    document.getElementById('blockModal').style.display = 'flex';
}

function closeBlockModal(e) {
    if (!e || e.target === document.getElementById('blockModal')) {
        document.getElementById('blockModal').style.display = 'none';
    }
}

async function submitBlock() {
    const facilityId = document.getElementById('blockGround').value;
    const reason     = document.getElementById('blockReason').value;
    const startDate  = document.getElementById('blockStart').value;
    const endDate    = document.getElementById('blockEnd').value;
    const startTime  = document.getElementById('blockStartTime').value;
    const endTime    = document.getElementById('blockEndTime').value;
    const notes      = document.getElementById('blockNotes').value;

    if (!facilityId || !reason || !startDate || !endDate) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    if (endDate < startDate) {
        showToast('End date must be on or after start date', 'error');
        return;
    }

    const btn = document.getElementById('submitBlockBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Blocking…';

    try {
        const res = await fetch('/api/ground-owner/blocked-dates', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                facility_id: facilityId,
                start_date:  startDate,
                end_date:    endDate,
                start_time:  startTime || null,
                end_time:    endTime   || null,
                reason,
                notes
            })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed to block dates');

        showToast('Dates blocked successfully', 'success');
        document.getElementById('blockModal').style.display = 'none';
        resetBlockForm();
        await loadSchedule();
    } catch (err) {
        showToast(err.message || 'Failed to block dates', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-ban"></i> Block Dates';
    }
}

function resetBlockForm() {
    ['blockGround','blockReason','blockStartTime','blockEndTime','blockNotes'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const today = fmtDate(new Date());
    document.getElementById('blockStart').value = today;
    document.getElementById('blockEnd').value   = today;
}

async function removeBlockedDate(id) {
    if (!confirm('Remove this blocked date? Bookings on this date will no longer be restricted.')) return;
    try {
        const res  = await fetch(`/api/ground-owner/blocked-dates/${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed');
        showToast('Blocked date removed', 'success');
        await loadSchedule();
    } catch (err) {
        showToast(err.message || 'Failed to remove blocked date', 'error');
    }
}

/* ════════════════════════════════════════════════════════════
   UTILITY HELPERS
═══════════════════════════════════════════════════════════════ */
function isDayBlocked(dateStr, blocked) {
    return blocked.some(b => dateStr >= b.start_date && dateStr <= b.end_date);
}

function showLoading(visible) {
    const el = document.getElementById('calLoading');
    if (el) el.style.display = visible ? 'flex' : 'none';
}

function setLabel(text) {
    setEl('periodLabel', text);
}

function setEl(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function pad(n) {
    return String(n).padStart(2, '0');
}

function fmtDate(d) {
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function formatTime12(t) {
    if (!t) return '';
    const [hh, mm] = t.split(':');
    const h = parseInt(hh, 10);
    return `${h % 12 || 12}:${mm} ${h >= 12 ? 'PM' : 'AM'}`;
}

function fmtDateShort(s) {
    if (!s) return '—';
    const d = new Date(s + 'T00:00:00');
    return `${MONTH_NAMES[d.getMonth()].slice(0,3)} ${d.getDate()}, ${d.getFullYear()}`;
}

function fmtDateLong(s) {
    if (!s) return '—';
    const d = new Date(s + 'T00:00:00');
    return `${DAY_NAMES[d.getDay()]}, ${MONTH_NAMES[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
}

function truncate(s, n) {
    if (!s) return '';
    return s.length > n ? s.slice(0, n) + '…' : s;
}

function escHtml(s) {
    return String(s !== undefined && s !== null ? s : '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showToast(msg, type = 'success') {
    let t = document.getElementById('sc-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'sc-toast';
        t.style.cssText = [
            'position:fixed', 'bottom:24px', 'right:24px', 'z-index:99999',
            'padding:12px 20px', 'border-radius:10px', 'font-size:14px',
            'font-weight:600', 'color:#fff', 'max-width:320px',
            'box-shadow:0 4px 20px rgba(0,0,0,.2)', 'transition:opacity .3s'
        ].join(';');
        document.body.appendChild(t);
    }
    const colors = { success: '#10b981', error: '#ef4444', info: '#3b82f6' };
    t.style.background = colors[type] || colors.info;
    t.textContent = msg;
    t.style.opacity = '1';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.opacity = '0'; }, 3500);
}
