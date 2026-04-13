<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Availability – Coach Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/coach-availability.css">
</head>
<body>

<div class="coach-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">

        <!--  Page Header  -->
        <div class="page-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title"><i class="fas fa-clock"></i> Availability</h1>
                    <p class="page-subtitle">Manage your weekly schedule and session slots</p>
                </div>
            </div>
            <div>
                <button class="btn btn-primary" id="saveBtn" onclick="saveSchedule()">
                    <i class="fas fa-save"></i> Save Schedule
                </button>
            </div>
        </div>

        <div class="availability-content">

            <!--  Stats Grid  -->
            <div class="stats-grid">
                <div class="stat-card stat-avail-hours">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statHours">—</div>
                        <div class="stat-label">Available Hours / Week</div>
                        <div class="stat-sub" id="statHoursSub">Set your schedule to see</div>
                    </div>
                </div>
                <div class="stat-card stat-avail-days">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statDays">—</div>
                        <div class="stat-label">Available Days / Week</div>
                        <div class="stat-sub">Days marked as available</div>
                    </div>
                </div>
                <div class="stat-card stat-upcoming">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statUpcoming">—</div>
                        <div class="stat-label">Upcoming Sessions</div>
                        <div class="stat-sub">Confirmed &amp; pending</div>
                    </div>
                </div>
                <div class="stat-card stat-next-in">
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statNextIn">—</div>
                        <div class="stat-label">Next Session</div>
                        <div class="stat-sub" id="statNextInSub">Time until next booking</div>
                    </div>
                </div>
            </div>

            <!--  Main Layout  -->
            <div class="avail-layout">

                <!-- Left column: schedule + calendar -->
                <div class="avail-main">

                    <!-- Weekly Schedule Card -->
                    <div class="schedule-card">
                        <div class="card-hd">
                            <h3><i class="fas fa-calendar-week"></i> Weekly Schedule</h3>
                            <span class="card-hd-sub">Toggle days and set working hours</span>
                        </div>
                        <div class="schedule-body" id="scheduleBody">
                            <div class="skel-loading">
                                <?php for ($i = 0; $i < 7; $i++): ?>
                                <div class="skel-row"></div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Card -->
                    <div class="calendar-card">
                        <div class="card-hd">
                            <h3><i class="fas fa-calendar-alt"></i> <span id="calMonthLabel">Loading…</span></h3>
                            <div class="cal-nav-btns">
                                <button class="cal-nav-btn" id="calPrev" title="Previous month">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="cal-nav-btn" id="calNext" title="Next month">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cal-grid-header">
                            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                            <div class="cal-day-hd"><?= $d ?></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                        <div class="cal-legend">
                            <div class="leg-item"><div class="leg-dot leg-dot-avail"></div> Available day</div>
                            <div class="leg-item"><div class="leg-dot leg-dot-booked"></div> Has bookings</div>
                            <div class="leg-item"><div class="leg-dot leg-dot-today"></div> Today</div>
                        </div>
                    </div>

                </div><!-- /avail-main -->

                <!-- Right column: upcoming sessions -->
                <div class="avail-side">
                    <div class="upcoming-card">
                        <div class="card-hd">
                            <h3><i class="fas fa-list-ul"></i> Upcoming Sessions</h3>
                            <span class="card-hd-sub" id="upcomingCount">Loading…</span>
                        </div>
                        <div class="upcoming-list" id="upcomingList">
                            <div class="skel-loading">
                                <div class="skel-row" style="height:72px"></div>
                                <div class="skel-row" style="height:72px"></div>
                                <div class="skel-row" style="height:72px"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /avail-layout -->

        </div><!-- /availability-content -->
    </div><!-- /main-content -->
</div><!-- /coach-dashboard -->

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
// 
//  State
// 
let schedule   = [];
let upcoming   = [];
let calYear    = new Date().getFullYear();
let calMonth   = new Date().getMonth() + 1;   // 1-based
let calBookings = {};   // "YYYY-MM-DD" => { count, confirmed, pending }
let calDayAvail = {};   // "Monday" => bool

const MONTHS = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
];
// Map JS getDay() (0=Sun) to day_of_week string
const DOW_MAP = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

// 
//  Init
// 
document.addEventListener('DOMContentLoaded', () => {
    loadSchedule();

    document.getElementById('calPrev').addEventListener('click', () => {
        stepMonth(-1);
        loadCalendar();
    });
    document.getElementById('calNext').addEventListener('click', () => {
        stepMonth(1);
        loadCalendar();
    });

    // Sidebar toggle (match other dashboard pages)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar       = document.querySelector('.dashboard-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    }
});

// 
//  Load schedule from API
// 
async function loadSchedule() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/coach/availability');
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed to load');

        schedule = data.schedule;
        upcoming = data.upcoming;
        renderStats(data.stats);
        renderSchedule();
        renderUpcoming();
        loadCalendar();
    } catch (err) {
        console.error(err);
        toast('Failed to load availability data.', 'error');
    }
}

// 
//  Render stats
// 
function renderStats(stats) {
    const hrs = stats.available_hours;
    document.getElementById('statHours').textContent    = hrs + ' hrs';
    document.getElementById('statHoursSub').textContent = hrs > 0 ? 'Hours open for booking' : 'No available hours set';
    document.getElementById('statDays').textContent     = stats.available_days + ' days';
    document.getElementById('statUpcoming').textContent = stats.upcoming_sessions;

    if (upcoming.length > 0) {
        const next = upcoming[0];
        const diff = daysBetween(new Date(), new Date(next.booking_date + 'T00:00:00'));
        if (diff === 0) {
            document.getElementById('statNextIn').textContent  = 'Today';
        } else if (diff === 1) {
            document.getElementById('statNextIn').textContent  = 'Tomorrow';
        } else {
            document.getElementById('statNextIn').textContent  = diff + ' days';
        }
        document.getElementById('statNextInSub').textContent =
            next.first_name + ' ' + next.last_name + ' · ' + fmtTime(next.start_time);
    } else {
        document.getElementById('statNextIn').textContent    = '—';
        document.getElementById('statNextInSub').textContent = 'No upcoming sessions';
    }
}

// 
//  Render weekly schedule
// 
function renderSchedule() {
    const body = document.getElementById('scheduleBody');
    body.innerHTML = schedule.map((s, i) => buildScheduleRow(s, i)).join('');

    schedule.forEach((s, i) => {
        const toggle  = document.getElementById('toggle-' + i);
        const startEl = document.getElementById('start-' + i);
        const endEl   = document.getElementById('end-'   + i);

        toggle.addEventListener('change', () => onToggle(i));
        if (startEl) startEl.addEventListener('change', () => updateHours(i));
        if (endEl)   endEl.addEventListener('change',   () => updateHours(i));

        updateHours(i);
    });
}

function buildScheduleRow(s, i) {
    const isAvail = parseInt(s.is_available) === 1;
    const start   = (s.start_time || '09:00:00').slice(0, 5);
    const end     = (s.end_time   || '18:00:00').slice(0, 5);
    const dur     = parseInt(s.slot_duration) || 60;

    const durOpts = [
        [30,  '30 min slots'],
        [45,  '45 min slots'],
        [60,  '1 hr slots'],
        [90,  '90 min slots'],
        [120, '2 hr slots'],
    ].map(([v, lbl]) =>
        `<option value="${v}" ${dur === v ? 'selected' : ''}>${lbl}</option>`
    ).join('');

    return `
    <div class="schedule-row ${isAvail ? '' : 'day-off'}" id="row-${i}">
        <div class="day-indicator">
            <span class="day-dot"></span>
            <div class="day-name">${s.day_of_week}</div>
        </div>

        <div class="toggle-wrap">
            <label class="toggle-switch" title="${isAvail ? 'Available – click to set off' : 'Day off – click to enable'}">
                <input type="checkbox" id="toggle-${i}" ${isAvail ? 'checked' : ''}>
                <span class="toggle-track"></span>
            </label>
        </div>

        <div class="time-controls" id="timeCtrl-${i}" style="display:${isAvail ? 'flex' : 'none'}">
            <input type="time" class="time-input" id="start-${i}" value="${start}">
            <span class="time-sep">–</span>
            <input type="time" class="time-input" id="end-${i}" value="${end}">
            <select class="dur-select" id="dur-${i}">${durOpts}</select>
        </div>

        <div class="day-off-label" id="offLabel-${i}" style="display:${isAvail ? 'none' : 'flex'}">
            Day Off
        </div>

        <div class="day-hours-badge" id="hrs-${i}"></div>
    </div>`;
}

function onToggle(i) {
    const toggle = document.getElementById('toggle-' + i);
    const row    = document.getElementById('row-'    + i);
    const ctrl   = document.getElementById('timeCtrl-' + i);
    const offLbl = document.getElementById('offLabel-' + i);
    const isOn   = toggle.checked;

    schedule[i].is_available = isOn ? 1 : 0;
    row.classList.toggle('day-off', !isOn);
    ctrl.style.display   = isOn ? 'flex' : 'none';
    offLbl.style.display = isOn ? 'none' : 'flex';
    updateHours(i);
}

function updateHours(i) {
    const hrsEl  = document.getElementById('hrs-' + i);
    const toggle = document.getElementById('toggle-' + i);
    if (!hrsEl) return;
    if (!toggle || !toggle.checked) { hrsEl.textContent = ''; return; }

    const startEl = document.getElementById('start-' + i);
    const endEl   = document.getElementById('end-'   + i);
    if (!startEl || !endEl || !startEl.value || !endEl.value) return;

    const [sh, sm] = startEl.value.split(':').map(Number);
    const [eh, em] = endEl.value.split(':').map(Number);
    const mins     = (eh * 60 + em) - (sh * 60 + sm);

    if (mins > 0) {
        const hrs = mins / 60;
        hrsEl.textContent = (hrs % 1 === 0 ? hrs : hrs.toFixed(1)) + ' hrs';
    } else {
        hrsEl.textContent = '—';
    }
}

// 
//  Save schedule
// 
async function saveSchedule() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    // Collect current DOM state
    const payload = schedule.map((s, i) => {
        const toggle  = document.getElementById('toggle-' + i);
        const startEl = document.getElementById('start-'  + i);
        const endEl   = document.getElementById('end-'    + i);
        const durEl   = document.getElementById('dur-'    + i);
        return {
            day_of_week:   s.day_of_week,
            is_available:  toggle && toggle.checked ? 1 : 0,
            start_time:    startEl ? startEl.value : (s.start_time || '09:00'),
            end_time:      endEl   ? endEl.value   : (s.end_time   || '18:00'),
            slot_duration: durEl   ? parseInt(durEl.value) : (parseInt(s.slot_duration) || 60),
        };
    });

    try {
        const res  = await fetch('/api/coach/availability', {
            method:  'PUT',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ schedule: payload }),
        });
        const data = await res.json();

        if (data.success) {
            toast('Schedule saved successfully!', 'success');
            await loadSchedule();  // refresh stats + calendar
        } else {
            toast(data.message || 'Failed to save schedule.', 'error');
        }
    } catch (err) {
        toast('Network error – please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Schedule';
    }
}

// 
//  Calendar
// 
function stepMonth(delta) {
    calMonth += delta;
    if (calMonth < 1)  { calMonth = 12; calYear--; }
    if (calMonth > 12) { calMonth = 1;  calYear++; }
}

async function loadCalendar() {
    document.getElementById('calMonthLabel').textContent = MONTHS[calMonth - 1] + ' ' + calYear;
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/availability/calendar?year=${calYear}&month=${calMonth}`);
        const data = await res.json();
        if (data.success) {
            calBookings = data.bookings;
            calDayAvail = data.day_avail;
        }
    } catch (_) { /* render with existing data */ }
    renderCalendar();
}

function renderCalendar() {
    const grid     = document.getElementById('calGrid');
    const todayStr = fmtDateStr(new Date());

    // Determine padding (Mon-based grid)
    const firstDay   = new Date(calYear, calMonth - 1, 1);
    const daysInMonth = new Date(calYear, calMonth, 0).getDate();
    const prevLast    = new Date(calYear, calMonth - 1, 0).getDate();

    // Mon = 0 … Sun = 6
    let startOffset = firstDay.getDay() - 1;
    if (startOffset < 0) startOffset = 6;

    const cells = [];

    // Trailing days from previous month
    for (let i = startOffset - 1; i >= 0; i--) {
        cells.push({ day: prevLast - i, cur: false });
    }
    // Current month
    for (let d = 1; d <= daysInMonth; d++) {
        cells.push({ day: d, cur: true });
    }
    // Leading days from next month
    while (cells.length < 42) {
        cells.push({ day: cells.length - startOffset - daysInMonth + 1, cur: false });
    }

    grid.innerHTML = cells.map(({ day, cur }) => {
        if (!cur) {
            return `<div class="cal-day other-month"><div class="cal-day-num">${day}</div></div>`;
        }

        const dateStr = `${calYear}-${String(calMonth).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        const dow     = DOW_MAP[new Date(calYear, calMonth - 1, day).getDay()];
        const avail   = calDayAvail[dow] || false;
        const booking = calBookings[dateStr] || null;
        const isToday = dateStr === todayStr;

        const cls = ['cal-day'];
        if (isToday) cls.push('today');
        if (avail)   cls.push('is-available');
        if (booking) cls.push('has-bookings');

        const badge = booking
            ? `<div class="cal-booking-badge" title="${booking.count} session(s)">${booking.count}</div>`
            : '';
        const dot = avail && !booking ? '<div class="cal-avail-dot"></div>' : '';

        return `<div class="${cls.join(' ')}">
            <div class="cal-day-num">${day}</div>
            ${badge}${dot}
        </div>`;
    }).join('');
}

// 
//  Upcoming sessions
// 
function renderUpcoming() {
    const list    = document.getElementById('upcomingList');
    const countEl = document.getElementById('upcomingCount');
    const n       = upcoming.length;
    countEl.textContent = n + ' session' + (n !== 1 ? 's' : '');

    if (!n) {
        list.innerHTML = `
        <div class="empty-upcoming">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming sessions scheduled.</p>
        </div>`;
        return;
    }

    list.innerHTML = upcoming.map(s => {
        const d      = new Date(s.booking_date + 'T00:00:00');
        const month  = d.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        const day    = d.getDate();
        const status = s.status.charAt(0).toUpperCase() + s.status.slice(1);
        const stCls  = s.status === 'confirmed' ? 'sess-status-confirmed' : 'sess-status-pending';
        const type   = s.session_type ? s.session_type.charAt(0).toUpperCase() + s.session_type.slice(1) : 'Training';

        return `
        <div class="session-item">
            <div class="sess-date-col">
                <div class="sess-month">${month}</div>
                <div class="sess-day">${day}</div>
            </div>
            <div class="sess-info">
                <div class="sess-client">${s.first_name} ${s.last_name}</div>
                <div class="sess-meta">
                    <i class="fas fa-clock sess-time-icon"></i>
                    ${fmtTime(s.start_time)} – ${fmtTime(s.end_time)}
                    <span class="sess-type-pill">${type}</span>
                    <span class="sess-status-pill ${stCls}">${status}</span>
                </div>
            </div>
        </div>`;
    }).join('');
}

// 
//  Helpers
// 
function fmtTime(t) {
    if (!t) return '';
    const [h, m] = t.split(':').map(Number);
    const ampm   = h >= 12 ? 'PM' : 'AM';
    return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${ampm}`;
}

function fmtDateStr(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

function daysBetween(a, b) {
    const ms = b.setHours(0,0,0,0) - a.setHours(0,0,0,0);
    return Math.max(0, Math.round(ms / 86400000));
}

// 
//  Toast
// 
function toast(msg, type = 'success') {
    const wrap = document.getElementById('toastWrap');
    const el   = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>

</body>
</html>
