<?php
$_base = defined('BASE_URL') ? BASE_URL : ''; $currentPage = 'sessions'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Sessions - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/coach-sessions.css">
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <h1>Training Sessions</h1>
                    <p class="header-subtitle">Manage your coaching sessions and track progress</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" id="exportSessionsBtn">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Session Stats -->
            <div class="session-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalSessions">&mdash;</div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="upcomingSessions">&mdash;</div>
                        <div class="stat-label">Upcoming</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="completedSessions">&mdash;</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="cancelledSessions">&mdash;</div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="sessions-filters">
                <div class="filter-group">
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="in-progress">In Progress</option>
                    </select>
                    
                    <select id="typeFilter">
                        <option value="">All Types</option>
                        <option value="individual">Individual</option>
                        <option value="group">Group</option>
                        <option value="assessment">Assessment</option>
                    </select>
                    
                    <input type="date" id="dateFilter" placeholder="Filter by date">
                </div>
                
                <div class="search-group">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="sessionSearch" placeholder="Search sessions...">
                    </div>
                    <button class="btn btn-outline" id="clearFilters">Clear Filters</button>
                </div>
            </div>

            <!-- Sessions List -->
            <div class="sessions-container">
                <div class="sessions-header">
                    <div class="view-controls">
                        <button class="view-btn active" data-view="list">
                            <i class="fas fa-list"></i>
                            List
                        </button>
                        <button class="view-btn" data-view="calendar">
                            <i class="fas fa-calendar"></i>
                            Calendar
                        </button>
                        <button class="view-btn" data-view="timeline">
                            <i class="fas fa-stream"></i>
                            Timeline
                        </button>
                    </div>
                </div>

                <!-- List View -->
                <div id="listView" class="sessions-view active">
                    <div id="sessionsList" class="sessions-list">
                        <!-- Dynamic content -->
                    </div>
                </div>

                <!-- Calendar View -->
                <div id="calendarView" class="sessions-view">
                    <div class="calendar-header">
                        <button class="nav-btn" id="prevMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h3 id="currentMonth">December 2024</h3>
                        <button class="nav-btn" id="nextMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div id="sessionsCalendar" class="sessions-calendar">
                        <!-- Dynamic calendar content -->
                    </div>
                </div>

                <!-- Timeline View -->
                <div id="timelineView" class="sessions-view">
                    <div id="sessionsTimeline" class="sessions-timeline">
                        <!-- Dynamic timeline content -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Session Details Modal -->
    <div id="sessionDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Session Details</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="sessionDetailsContent">
                    <!-- Dynamic session details -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('sessionDetailsModal').classList.remove('active')">Close</button>
            </div>
        </div>
    </div>
<script>
/*  Real-data loader for coach sessions page  */
(function () {
    let allSessions = [];

    function formatTime(t) {
        if (!t) return '';
        const [h, m] = t.split(':').map(Number);
        const ampm = h >= 12 ? 'PM' : 'AM';
        return ((h % 12) || 12) + ':' + String(m).padStart(2, '0') + ' ' + ampm;
    }

    function statusBadgeClass(s) {
        return { confirmed:'upcoming', pending:'upcoming', completed:'completed', cancelled:'cancelled', no_show:'cancelled' }[s] || '';
    }

    function buildSessionCard(s) {
        const client   = (s.client_first_name || '') + ' ' + (s.client_last_name || '');
        const dateStr  = new Date(s.booking_date).toLocaleDateString('en-GB', {weekday:'short',day:'numeric',month:'short',year:'numeric'});
        const bClass   = statusBadgeClass(s.status);

        return `
        <div class="session-item ${bClass}" data-id="${s.id}" data-status="${s.status}">
            <div class="session-datetime">
                <span class="session-date">${dateStr}</span>
                <span class="session-time">${formatTime(s.start_time)} – ${formatTime(s.end_time)}</span>
                <span class="session-duration">${s.duration} min</span>
            </div>
            <div class="session-details">
                <h4 class="session-title">${s.session_title || 'Training Session'}</h4>
                <div class="session-client">
                    <span class="client-avatar">${client.charAt(0).toUpperCase()}</span>
                    <span>${client}</span>
                </div>
                <span class="session-type-badge ${s.session_type}">${s.session_type}</span>
            </div>
            <div class="session-status">
                <span class="status-indicator ${s.status}">${s.status}</span>
                <span class="session-rate">LKR ${parseFloat(s.total_amount || 0).toLocaleString()}</span>
            </div>
            <div class="session-actions">
                ${s.status === 'confirmed'
                    ? `<button class="btn btn-sm btn-success" onclick="completeSession(${s.id})">
                           <i class="fas fa-check"></i> Complete
                       </button>
                       <button class="btn btn-sm btn-danger" onclick="cancelSession(${s.id})">
                           <i class="fas fa-times"></i> Cancel
                       </button>`
                    : `<button class="btn btn-sm btn-outline" onclick="viewSessionDetails(${s.id})">
                           <i class="fas fa-eye"></i> View
                       </button>`}
            </div>
        </div>`;
    }

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const type   = document.getElementById('typeFilter').value;
        const date   = document.getElementById('dateFilter').value;
        const search = (document.getElementById('sessionSearch').value || '').toLowerCase();

        let filtered = allSessions;
        if (status === 'upcoming')  filtered = filtered.filter(s => ['confirmed','pending'].includes(s.status));
        else if (status)            filtered = filtered.filter(s => s.status === status);
        if (type)                   filtered = filtered.filter(s => s.session_type === type);
        if (date)                   filtered = filtered.filter(s => s.booking_date === date);
        if (search)                 filtered = filtered.filter(s => {
            const client = ((s.client_first_name || '') + ' ' + (s.client_last_name || '')).toLowerCase();
            return client.includes(search) || (s.session_title || '').toLowerCase().includes(search);
        });

        const list = document.getElementById('sessionsList');
        if (!filtered.length) {
            list.innerHTML = '<div style="text-align:center;padding:3rem;color:#64748b">No sessions found.</div>';
        } else {
            list.innerHTML = filtered.map(buildSessionCard).join('');
        }
    }

    async function loadSessions() {
        try {
            const res  = await fetch((window.BASE_URL||'')+'/api/coach/bookings');
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Failed');
            allSessions = data.bookings || [];
            const stats = data.stats || {};

            // Stats from real DB via getCoachBookingStats()
            document.getElementById('totalSessions').textContent     = stats.total_bookings ?? allSessions.length;
            document.getElementById('upcomingSessions').textContent  = stats.upcoming_sessions ?? allSessions.filter(s => ['confirmed','pending'].includes(s.status)).length;
            document.getElementById('completedSessions').textContent = stats.completed_sessions ?? allSessions.filter(s => s.status === 'completed').length;
            document.getElementById('cancelledSessions').textContent = stats.cancelled_sessions ?? allSessions.filter(s => s.status === 'cancelled').length;

            applyFilters();
        } catch (e) {
            document.getElementById('sessionsList').innerHTML =
                '<div style="text-align:center;padding:3rem;color:#ef4444">Failed to load sessions: ' + e.message + '</div>';
        }
    }

    window.completeSession = async function (id) {
        if (!confirm('Mark this session as completed?')) return;
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/bookings/${id}/complete`, { method: 'PUT' });
        const data = await res.json();
        if (data.success) loadSessions();
        else alert(data.error || 'Failed');
    };

    window.cancelSession = async function (id) {
        if (!confirm('Cancel this session?')) return;
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/bookings/${id}/cancel`, { method: 'PUT' });
        const data = await res.json();
        if (data.success) loadSessions();
        else alert(data.error || 'Failed');
    };

    window.viewSessionDetails = function (id) {
        const s = allSessions.find(b => b.id === id);
        if (!s) return;
        alert(`Session: ${s.session_title || 'Training Session'}
Client: ${s.client_first_name} ${s.client_last_name}
Date: ${s.booking_date}
Time: ${formatTime(s.start_time)} – ${formatTime(s.end_time)}
Status: ${s.status}
Amount: LKR ${parseFloat(s.total_amount || 0).toLocaleString()}`);
    };

    //  Export functionality 
    const exportBtn = document.getElementById('exportSessionsBtn');
    if (exportBtn) exportBtn.addEventListener('click', exportSessionsCSV);

    function exportSessionsCSV() {
        if (!allSessions.length) {
            alert('No sessions to export.');
            return;
        }

        // Apply current filters to export only filtered sessions
        const status = document.getElementById('statusFilter').value;
        const type   = document.getElementById('typeFilter').value;
        const date   = document.getElementById('dateFilter').value;
        const search = (document.getElementById('sessionSearch').value || '').toLowerCase();

        let filtered = allSessions;
        if (status === 'upcoming')  filtered = filtered.filter(s => ['confirmed','pending'].includes(s.status));
        else if (status)            filtered = filtered.filter(s => s.status === status);
        if (type)                   filtered = filtered.filter(s => s.session_type === type);
        if (date)                   filtered = filtered.filter(s => s.booking_date === date);
        if (search)                 filtered = filtered.filter(s => {
            const client = ((s.client_first_name || '') + ' ' + (s.client_last_name || '')).toLowerCase();
            return client.includes(search) || (s.session_title || '').toLowerCase().includes(search);
        });

        // Build CSV
        const headers = ['#','Date','Start Time','End Time','Duration (min)','Session Title','Client Name','Client Email','Session Type','Status','Payment Status','Amount (LKR)','Special Requests','Coach Notes'];
        const rows = filtered.map((s, i) => [
            i + 1,
            s.booking_date || '',
            s.start_time || '',
            s.end_time || '',
            s.duration || '',
            (s.session_title || 'Training Session').replace(/"/g, '""'),
            ((s.client_first_name || '') + ' ' + (s.client_last_name || '')).trim().replace(/"/g, '""'),
            (s.client_email || '').replace(/"/g, '""'),
            s.session_type || '',
            s.status || '',
            s.payment_status || '',
            parseFloat(s.total_amount || 0).toFixed(2),
            (s.special_requests || '').replace(/"/g, '""'),
            (s.coach_notes || '').replace(/"/g, '""')
        ]);

        let csv = headers.map(h => `"${h}"`).join(',') + '\n';
        rows.forEach(row => {
            csv += row.map(cell => `"${cell}"`).join(',') + '\n';
        });

        // Download
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const dateStr = new Date().toISOString().split('T')[0];
        const filterLabel = status || type || 'all';
        a.download = `training_sessions_${filterLabel}_${dateStr}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    //  Filter listeners 
    ['statusFilter','typeFilter','dateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', applyFilters);
    });
    const searchEl = document.getElementById('sessionSearch');
    if (searchEl) searchEl.addEventListener('input', applyFilters);
    const clearBtn = document.getElementById('clearFilters');
    if (clearBtn) clearBtn.addEventListener('click', () => {
        document.getElementById('statusFilter').value  = '';
        document.getElementById('typeFilter').value    = '';
        document.getElementById('dateFilter').value    = '';
        document.getElementById('sessionSearch').value = '';
        applyFilters();
    });

    //  View switcher 
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const view = this.dataset.view;
            document.querySelectorAll('.sessions-view').forEach(v => v.classList.remove('active'));
            document.getElementById(view + 'View').classList.add('active');
            if (view === 'calendar') renderCalendar();
            if (view === 'timeline') renderTimeline();
        });
    });

    //  Calendar nav 
    let calendarDate = new Date();
    let selectedDayEl = null;

    document.getElementById('prevMonth').addEventListener('click', () => {
        calendarDate.setMonth(calendarDate.getMonth() - 1);
        renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
        calendarDate.setMonth(calendarDate.getMonth() + 1);
        renderCalendar();
    });

    //  Calendar renderer 
    function renderCalendar() {
        const year  = calendarDate.getFullYear();
        const month = calendarDate.getMonth();

        document.getElementById('currentMonth').textContent =
            new Date(year, month, 1).toLocaleDateString('en-GB', { month: 'long', year: 'numeric' });

        const firstDow  = new Date(year, month, 1).getDay();   // 0=Sun
        const daysInMon = new Date(year, month + 1, 0).getDate();
        const today     = new Date();
        const todayStr  = today.toISOString().split('T')[0];

        // Group sessions by day number for this month
        const byDay = {};
        allSessions.forEach(s => {
            const d = new Date(s.booking_date);
            if (d.getFullYear() === year && d.getMonth() === month) {
                const dn = d.getDate();
                (byDay[dn] = byDay[dn] || []).push(s);
            }
        });

        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        let html = '<div class="calendar-grid">';
        dayNames.forEach(n => html += `<div class="calendar-day-header">${n}</div>`);

        // Blank cells
        for (let i = 0; i < firstDow; i++) html += '<div class="calendar-day other-month"></div>';

        for (let d = 1; d <= daysInMon; d++) {
            const dateStr   = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isToday   = dateStr === todayStr;
            const sessions  = byDay[d] || [];
            const hasSess   = sessions.length > 0;

            let dots = '';
            if (hasSess) {
                dots += '<div class="day-sessions">';
                sessions.slice(0, 3).forEach(s => {
                    const cls = s.status === 'completed' ? 'completed'
                              : s.status === 'cancelled' ? 'cancelled' : 'upcoming';
                    dots += `<span class="session-dot ${cls}" title="${(s.session_title||'Session')} · ${formatTime(s.start_time)}"></span>`;
                });
                if (sessions.length > 3) dots += `<span class="session-dot-more">+${sessions.length-3}</span>`;
                dots += '</div>';
            }

            html += `<div class="calendar-day ${isToday?'today':''} ${hasSess?'has-sessions':''}"
                          data-date="${dateStr}"
                          onclick="showDaySessions(this,'${dateStr}')">
                        <span class="day-number">${d}</span>
                        ${dots}
                     </div>`;
        }

        // Fill remaining cells to complete last row
        const filled = firstDow + daysInMon;
        const remainder = (7 - (filled % 7)) % 7;
        for (let i = 0; i < remainder; i++) html += '<div class="calendar-day other-month"></div>';

        html += '</div>';

        // Legend
        html += `<div class="calendar-legend">
            <span class="legend-item"><span class="session-dot upcoming"></span> Upcoming</span>
            <span class="legend-item"><span class="session-dot completed"></span> Completed</span>
            <span class="legend-item"><span class="session-dot cancelled"></span> Cancelled</span>
        </div>`;

        // Day detail panel (appended after grid)
        html += '<div id="calendarDayPanel" class="calendar-day-panel" style="display:none"></div>';

        document.getElementById('sessionsCalendar').innerHTML = html;
    }

    window.showDaySessions = function (cell, dateStr) {
        // Toggle off if same cell clicked
        if (selectedDayEl === cell && document.getElementById('calendarDayPanel').style.display !== 'none') {
            document.getElementById('calendarDayPanel').style.display = 'none';
            cell.classList.remove('selected');
            selectedDayEl = null;
            return;
        }
        if (selectedDayEl) selectedDayEl.classList.remove('selected');
        selectedDayEl = cell;
        cell.classList.add('selected');

        const daySessions = allSessions.filter(s => s.booking_date === dateStr);
        const panel = document.getElementById('calendarDayPanel');

        if (!daySessions.length) { panel.style.display = 'none'; return; }

        const d = new Date(dateStr + 'T00:00:00');
        const label = d.toLocaleDateString('en-GB', { weekday:'long', day:'numeric', month:'long', year:'numeric' });

        panel.innerHTML = `
            <div class="day-panel-header">
                <h4><i class="fas fa-calendar-day" style="color:var(--primary-color);margin-right:8px"></i>${label}
                    <span style="color:var(--text-muted);font-size:13px;font-weight:500;margin-left:8px">${daySessions.length} session${daySessions.length>1?'s':''}</span>
                </h4>
                <button class="panel-close" onclick="document.getElementById('calendarDayPanel').style.display='none';
                    if(selectedDayEl){selectedDayEl.classList.remove('selected');selectedDayEl=null;}">×</button>
            </div>
            <div class="day-panel-sessions">
                ${daySessions.map(s => `
                    <div class="day-panel-session ${s.status}">
                        <div class="panel-session-time">
                            <span>${formatTime(s.start_time)}</span>
                            <span>${formatTime(s.end_time)}</span>
                            <span class="panel-duration">${s.duration}m</span>
                        </div>
                        <div class="panel-session-info">
                            <strong>${s.session_title || 'Training Session'}</strong>
                            <span><i class="fas fa-user" style="font-size:11px;margin-right:4px;opacity:.6"></i>${s.client_first_name} ${s.client_last_name}</span>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
                            <span class="session-type-badge ${s.session_type}">${s.session_type}</span>
                            <span class="status-indicator ${s.status}">${s.status}</span>
                        </div>
                    </div>`).join('')}
            </div>`;
        panel.style.display = 'block';
    };

    //  Timeline renderer 
    function renderTimeline() {
        const tl = document.getElementById('sessionsTimeline');

        if (!allSessions.length) {
            tl.innerHTML = '<div style="text-align:center;padding:3rem;color:#64748b">No sessions to display.</div>';
            return;
        }

        // Sort newest first
        const sorted = [...allSessions].sort((a, b) => {
            const da = new Date(a.booking_date + 'T' + (a.start_time || '00:00'));
            const db = new Date(b.booking_date + 'T' + (b.start_time || '00:00'));
            return db - da;
        });

        // Group by date
        const groups = {};
        sorted.forEach(s => {
            (groups[s.booking_date] = groups[s.booking_date] || []).push(s);
        });

        const todayStr = new Date().toISOString().split('T')[0];

        tl.innerHTML = Object.entries(groups).map(([date, sessions]) => {
            const d        = new Date(date + 'T00:00:00');
            const dayNum   = d.toLocaleDateString('en-GB', { day: 'numeric' });
            const mon      = d.toLocaleDateString('en-GB', { month: 'short' });
            const fullDate = d.toLocaleDateString('en-GB', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
            const isPast   = date < todayStr;
            const isToday  = date === todayStr;

            const cards = sessions.map(s => {
                const actions = s.status === 'confirmed' ? `
                    <div class="tl-session-actions">
                        <button class="btn btn-sm btn-success" onclick="completeSession(${s.id})">
                            <i class="fas fa-check"></i> Complete
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="cancelSession(${s.id})">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>` : '';

                return `
                <div class="timeline-session-card ${s.status}">
                    <div class="tl-session-time">
                        <span><i class="fas fa-clock" style="font-size:11px;opacity:.6;margin-right:4px"></i>${formatTime(s.start_time)} – ${formatTime(s.end_time)}</span>
                        <span class="tl-duration">${s.duration} min</span>
                    </div>
                    <div class="tl-session-main">
                        <h4>${s.session_title || 'Training Session'}</h4>
                        <div class="tl-session-client">
                            <span class="client-avatar">${(s.client_first_name||'?').charAt(0).toUpperCase()}</span>
                            ${s.client_first_name} ${s.client_last_name}
                        </div>
                    </div>
                    <div class="tl-session-meta">
                        <span class="session-type-badge ${s.session_type}">${s.session_type}</span>
                        <span class="status-indicator ${s.status}">${s.status}</span>
                        <span class="tl-amount">LKR ${parseFloat(s.total_amount||0).toLocaleString()}</span>
                    </div>
                    ${actions}
                </div>`;
            }).join('');

            return `
            <div class="timeline-group ${isPast?'past':''} ${isToday?'today':''}">
                <div class="timeline-marker-wrap">
                    <div class="timeline-marker ${isToday?'today':''}">
                        <span class="marker-day">${dayNum}</span>
                        <span class="marker-month">${mon}</span>
                    </div>
                    <div class="timeline-line"></div>
                </div>
                <div class="timeline-content">
                    <div class="timeline-date-label">
                        ${isToday ? '<i class="fas fa-circle" style="color:var(--warning-color);font-size:8px;margin-right:6px"></i>' : ''}
                        ${fullDate} · <strong>${sessions.length}</strong> session${sessions.length>1?'s':''}
                    </div>
                    <div class="timeline-sessions">${cards}</div>
                </div>
            </div>`;
        }).join('');
    }

    loadSessions();
})();
</script>
</body>
</html>