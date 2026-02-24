<?php $currentPage = 'sessions'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Sessions - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-sessions.css">
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
                    <a href="/coach/book-session" class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i>
                        Book New Session
                    </a>
                </div>
            </div>

            <!-- Session Stats -->
            <div class="session-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalSessions">87</div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="upcomingSessions">8</div>
                        <div class="stat-label">Upcoming</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="completedSessions">76</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="cancelledSessions">3</div>
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

    <!-- New Session Modal -->
    <div id="newSessionModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Schedule New Session</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="newSessionForm">
                    <div class="form-section">
                        <h4>Session Details</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Session Title</label>
                                <input type="text" name="title" required placeholder="e.g., Cricket Fundamentals">
                            </div>
                            <div class="form-group">
                                <label>Session Type</label>
                                <select name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="individual">Individual Training</option>
                                    <option value="group">Group Session</option>
                                    <option value="assessment">Assessment</option>
                                    <option value="consultation">Consultation</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3" placeholder="Session objectives and focus areas"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Client Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Client</label>
                                <select name="clientId" required>
                                    <option value="">Select Client</option>
                                    <!-- Dynamic options -->
                                </select>
                            </div>
                            <div class="form-group" id="groupSizeGroup" style="display: none;">
                                <label>Group Size</label>
                                <input type="number" name="groupSize" min="2" max="20">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Schedule & Pricing</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="sessionDate" required>
                            </div>
                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" name="startTime" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Duration (minutes)</label>
                                <select name="duration" required>
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60" selected>1 hour</option>
                                    <option value="90">1.5 hours</option>
                                    <option value="120">2 hours</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Rate (₹)</label>
                                <input type="number" name="rate" required placeholder="Session rate">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Location & Equipment</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Location</label>
                                <select name="location" required>
                                    <option value="">Select Location</option>
                                    <option value="ground-a">Ground A - Cricket Field</option>
                                    <option value="ground-b">Ground B - Practice Nets</option>
                                    <option value="indoor">Indoor Training Hall</option>
                                    <option value="client-location">Client's Location</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Equipment Needed</label>
                                <input type="text" name="equipment" placeholder="e.g., Cricket bats, balls, cones">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Special Requirements</label>
                        <textarea name="requirements" rows="2" placeholder="Any special requirements or notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachSessions.closeModal('newSessionModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachSessions.saveSession()">Schedule Session</button>
            </div>
        </div>
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
                <button type="button" class="btn btn-secondary" onclick="coachSessions.closeModal('sessionDetailsModal')">Close</button>
                <button type="button" class="btn btn-warning" id="editSessionBtn">Edit Session</button>
                <button type="button" class="btn btn-danger" id="cancelSessionBtn">Cancel Session</button>
            </div>
        </div>
    </div>

    <script src="/public/js/pages/coach-sessions.js"></script>
<script>
/* ── Real-data loader for coach sessions page ── */
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
            const res  = await fetch('/api/coach/bookings');
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Failed');
            allSessions = data.bookings || [];

            // Stats
            document.getElementById('totalSessions').textContent     = allSessions.length;
            document.getElementById('upcomingSessions').textContent  = allSessions.filter(s => ['confirmed','pending'].includes(s.status)).length;
            document.getElementById('completedSessions').textContent = allSessions.filter(s => s.status === 'completed').length;
            document.getElementById('cancelledSessions').textContent = allSessions.filter(s => s.status === 'cancelled').length;

            applyFilters();
        } catch (e) {
            document.getElementById('sessionsList').innerHTML =
                '<div style="text-align:center;padding:3rem;color:#ef4444">Failed to load sessions: ' + e.message + '</div>';
        }
    }

    window.completeSession = async function (id) {
        if (!confirm('Mark this session as completed?')) return;
        const res  = await fetch(`/api/coach/bookings/${id}/complete`, { method: 'PUT' });
        const data = await res.json();
        if (data.success) loadSessions();
        else alert(data.error || 'Failed');
    };

    window.cancelSession = async function (id) {
        if (!confirm('Cancel this session?')) return;
        const res  = await fetch(`/api/coach/bookings/${id}/cancel`, { method: 'PUT' });
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

    // Attach filter listeners
    ['statusFilter','typeFilter','dateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', applyFilters);
    });
    const searchEl = document.getElementById('sessionSearch');
    if (searchEl) searchEl.addEventListener('input', applyFilters);
    const clearBtn = document.getElementById('clearFilters');
    if (clearBtn) clearBtn.addEventListener('click', () => {
        document.getElementById('statusFilter').value = '';
        document.getElementById('typeFilter').value   = '';
        document.getElementById('dateFilter').value   = '';
        document.getElementById('sessionSearch').value = '';
        applyFilters();
    });

    loadSessions();
})();
</script>
</body>
</html>