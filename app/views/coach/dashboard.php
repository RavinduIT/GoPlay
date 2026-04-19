<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Coach Dashboard – GoPlay';
$additionalCSS = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
    '/public/css/coach/sidebar.css',
    '/public/css/pages/coach-dashboard.css',
];
?>
<style>
/*  Coach Dashboard Layout inside main.php  */
#main-content { padding: 0 !important; }

.coach-dashboard {
    display: flex;
    min-height: calc(100vh - 140px); /* leave room for public nav + footer */
    background: #f8fafc;
}

/*  Page Header  */
.cd-header {
    background: #fff;
    padding: 18px 32px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.cd-header-left { display: flex; align-items: center; gap: 16px; }
.sidebar-toggle {
    background: none; border: 1px solid #e2e8f0; padding: 8px 10px;
    border-radius: 8px; cursor: pointer; color: #64748b; transition: all .2s;
}
.sidebar-toggle:hover { background: #f1f5f9; color: #1e293b; }
.cd-title { font-size: 1.35rem; font-weight: 700; color: #1e293b; margin: 0; }
.cd-subtitle { font-size: .82rem; color: #64748b; margin: 2px 0 0; }
.coach-header-info { display: flex; align-items: center; gap: 12px; }
.coach-header-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: .9rem;
    overflow: hidden; flex-shrink: 0;
}
.coach-header-avatar img { width:100%; height:100%; object-fit:cover; }
.coach-header-name  { font-size: .92rem; font-weight: 600; color: #1e293b; line-height: 1.2; }
.coach-header-sport { font-size: .78rem; color: #64748b; }

/*  Main content wrapper  */
.cd-main {
    flex: 1;
    margin-left: 260px; /* sidebar width */
    transition: margin-left .3s ease;
    min-width: 0;
}
.admin-sidebar.collapsed ~ .cd-main { margin-left: 80px; }

/*  Stats  */
.stats-section { padding: 24px 28px 0; }
.stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; }
.stat-card {
    background: #fff; border-radius: 14px; padding: 20px 18px;
    border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,.04);
    display: flex; align-items: center; gap: 14px;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.stat-icon {
    width: 50px; height: 50px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; color: #fff; flex-shrink: 0;
}
.stat-icon.earn     { background: linear-gradient(135deg,#f59e0b,#fbbf24); }
.stat-icon.sess     { background: linear-gradient(135deg,#10b981,#34d399); }
.stat-icon.upcoming { background: linear-gradient(135deg,#3b82f6,#6366f1); }
.stat-icon.rating   { background: linear-gradient(135deg,#8b5cf6,#a78bfa); }
.stat-body { flex: 1; }
.stat-value { font-size: 1.45rem; font-weight: 800; color: #1e293b; line-height: 1; }
.stat-lbl   { font-size: .75rem; font-weight: 600; color: #64748b; margin: 4px 0; text-transform: uppercase; letter-spacing: .4px; }
.stat-sub   { font-size: .76rem; color: #94a3b8; }

/*  Dashboard Grid  */
.dash-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px 28px 28px;
}
.dash-card {
    background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,.04); overflow: hidden;
}
.card-hd {
    padding: 18px 20px 0; display: flex;
    justify-content: space-between; align-items: center; margin-bottom: 14px;
}
.card-hd h3 { font-size: .97rem; font-weight: 700; color: #1e293b; margin: 0; display:flex;align-items:center;gap:8px; }
.card-hd h3 i { color: #3b82f6; }
.card-link { font-size: .8rem; font-weight: 600; color: #3b82f6; text-decoration: none; padding: 4px 10px; border-radius: 6px; transition: background .2s; }
.card-link:hover { background: rgba(59,130,246,.08); }
.card-body { padding: 0 20px 20px; }
.span-full  { grid-column: 1 / -1; }

/*  Today schedule  */
.sched-list { display: flex; flex-direction: column; gap: 10px; }
.sched-item {
    display: flex; align-items: center; gap: 12px; padding: 12px 14px;
    background: #f8fafc; border-radius: 10px; border-left: 4px solid #3b82f6;
}
.sched-item.confirmed { border-left-color: #3b82f6; }
.sched-item.pending   { border-left-color: #f59e0b; }
.sched-item.completed { border-left-color: #10b981; }
.sched-item.cancelled { border-left-color: #94a3b8; opacity:.7; }
.sched-time { min-width: 70px; text-align: center; }
.sched-time .t { display:block; font-weight:700; font-size:.88rem; color:#1e293b; }
.sched-time .dur { font-size:.72rem; color:#94a3b8; }
.sched-info { flex:1; }
.sched-info .client { font-size:.9rem; font-weight:600; color:#1e293b; margin:0 0 3px; }
.type-pill {
    display:inline-block; font-size:.68rem; font-weight:600;
    padding:2px 8px; border-radius:20px; text-transform:capitalize;
    background:rgba(59,130,246,.1); color:#3b82f6;
}
.sched-actions { flex-shrink:0; }
.btn-complete {
    background:#10b981; color:#fff; border:none;
    padding:6px 12px; border-radius:7px; font-size:.76rem;
    font-weight:600; cursor:pointer; transition:background .2s;
}
.btn-complete:hover { background:#059669; }
.status-pill { display:inline-block; font-size:.7rem; font-weight:600; padding:3px 10px; border-radius:20px; }
.pill-confirmed { background:#dbeafe; color:#1d4ed8; }
.pill-completed { background:#d1fae5; color:#065f46; }
.pill-pending   { background:#fef3c7; color:#92400e; }
.pill-cancelled { background:#f1f5f9; color:#64748b; }

/*  Upcoming  */
.upcoming-list { display:flex; flex-direction:column; }
.up-item { display:flex; align-items:center; gap:12px; padding:11px 0; border-bottom:1px solid #f1f5f9; }
.up-item:last-child { border-bottom:none; padding-bottom:0; }
.up-date-col {
    width:42px; height:42px; border-radius:10px;
    background:linear-gradient(135deg,#3b82f6,#6366f1);
    display:flex; flex-direction:column; align-items:center; justify-content:center; flex-shrink:0;
}
.up-date-col .m { font-size:.58rem; font-weight:700; color:rgba(255,255,255,.8); text-transform:uppercase; }
.up-date-col .d { font-size:.95rem; font-weight:800; color:#fff; line-height:1; }
.up-info { flex:1; min-width:0; }
.up-info .client { font-size:.88rem; font-weight:600; color:#1e293b; margin:0 0 2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.up-info .meta { font-size:.76rem; color:#94a3b8; }
.up-right { text-align:right; flex-shrink:0; }
.up-right .amt { font-size:.87rem; font-weight:700; color:#10b981; display:block; }

/*  Clients grid  */
.clients-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.client-card {
    display:flex; align-items:center; gap:10px; padding:12px;
    background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; transition:border-color .2s;
}
.client-card:hover { border-color:#3b82f6; }
.client-avatar {
    width:38px; height:38px; border-radius:50%;
    background:linear-gradient(135deg,#3b82f6,#6366f1);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-weight:700; font-size:.85rem; flex-shrink:0; overflow:hidden;
}
.client-avatar img { width:100%; height:100%; object-fit:cover; }
.client-details { flex:1; min-width:0; }
.client-name { font-size:.84rem; font-weight:600; color:#1e293b; margin:0 0 2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.client-meta { font-size:.73rem; color:#94a3b8; }
.dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.dot-active   { background:#10b981; }
.dot-inactive { background:#94a3b8; }

/*  Quick actions  */
.qa-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.qa-btn {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:9px; padding:18px 10px; background:#f8fafc; border:2px solid #e2e8f0;
    border-radius:12px; text-decoration:none; color:#1e293b; font-size:.82rem;
    font-weight:600; transition:all .2s; text-align:center;
}
.qa-btn:hover {
    border-color:#3b82f6; background:rgba(59,130,246,.05);
    transform:translateY(-2px); box-shadow:0 4px 12px rgba(59,130,246,.12); color:#3b82f6;
}
.qa-btn i { font-size:1.2rem; color:#3b82f6; }

/*  Reviews  */
.reviews-list { display:flex; flex-direction:column; gap:12px; }
.review-card { display:flex; gap:12px; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; }
.rev-avatar {
    width:38px; height:38px; border-radius:50%;
    background:linear-gradient(135deg,#10b981,#34d399);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-weight:700; font-size:.82rem; flex-shrink:0;
}
.rev-body { flex:1; min-width:0; }
.rev-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:5px; }
.rev-name { font-size:.88rem; font-weight:600; color:#1e293b; }
.rev-date { font-size:.73rem; color:#94a3b8; }
.rev-stars { color:#f59e0b; font-size:.8rem; margin-bottom:5px; }
.rev-text { font-size:.81rem; color:#475569; line-height:1.5; margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }

/* Empty state */
.empty-state { text-align:center; padding:24px 16px; color:#94a3b8; }
.empty-state i { font-size:1.8rem; margin-bottom:8px; display:block; }
.empty-state p  { margin:0; font-size:.88rem; }

/* Skeleton */
.skel { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.skel-row { height:16px; margin-bottom:10px; }

/* Responsive */
@media (max-width: 1100px) {
    .stats-row { grid-template-columns:repeat(2,1fr); }
    .dash-grid  { grid-template-columns:1fr; }
    .span-full  { grid-column:1; }
    .cd-main    { margin-left:0; }
}
@media (max-width: 640px) {
    .stats-section, .dash-grid { padding:14px; }
    .stats-row  { grid-template-columns:1fr; gap:10px; }
    .cd-header  { padding:14px; flex-wrap:wrap; gap:10px; }
    .qa-grid, .clients-grid { grid-template-columns:1fr; }
}
</style>

<div class="coach-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="cd-main">

        <!--  Page Header  -->
        <div class="cd-header">
            <div class="cd-header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="cd-title">Dashboard</h1>
                    <p class="cd-subtitle" id="headerDate">Loading…</p>
                </div>
            </div>
            <div class="coach-header-info">
                <div class="coach-header-avatar" id="headerAvatar">—</div>
                <div>
                    <div class="coach-header-name"  id="headerName">Loading…</div>
                    <div class="coach-header-sport" id="headerSport"></div>
                </div>
            </div>
        </div>

        <!--  Stats  -->
        <div class="stats-section">
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon earn"><i class="fas fa-wallet"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statMonthEarnings">—</div>
                        <div class="stat-lbl">Monthly Earnings</div>
                        <div class="stat-sub"  id="statTotalEarnings">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon sess"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statTotalSessions">—</div>
                        <div class="stat-lbl">Total Sessions</div>
                        <div class="stat-sub"  id="statCompletedSessions">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon upcoming"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statUpcoming">—</div>
                        <div class="stat-lbl">Upcoming</div>
                        <div class="stat-sub"  id="statToday">—</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating"><i class="fas fa-star"></i></div>
                    <div class="stat-body">
                        <div class="stat-value" id="statRating">—</div>
                        <div class="stat-lbl">Average Rating</div>
                        <div class="stat-sub"  id="statReviews">—</div>
                    </div>
                </div>
            </div>
        </div>

        <!--  Dashboard Grid  -->
        <div class="dash-grid">

            <!-- Today's Schedule -->
            <div class="dash-card">
                <div class="card-hd">
                    <h3><i class="fas fa-clock"></i> Today's Schedule</h3>
                    <a href="<?= $_base ?>/coach/sessions" class="card-link">View All</a>
                </div>
                <div class="card-body">
                    <div id="todayList">
                        <div class="skel skel-row" style="width:100%"></div>
                        <div class="skel skel-row" style="width:80%"></div>
                        <div class="skel skel-row" style="width:90%"></div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="dash-card">
                <div class="card-hd">
                    <h3><i class="fas fa-calendar-week"></i> Upcoming Sessions</h3>
                    <a href="<?= $_base ?>/coach/sessions" class="card-link">View All</a>
                </div>
                <div class="card-body">
                    <div id="upcomingList" class="upcoming-list">
                        <div class="skel skel-row" style="width:100%"></div>
                        <div class="skel skel-row" style="width:80%"></div>
                        <div class="skel skel-row" style="width:90%"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="dash-card">
                <div class="card-hd">
                    <h3><i class="fas fa-users"></i> Recent Clients</h3>
                    <a href="<?= $_base ?>/coach/clients" class="card-link">View All</a>
                </div>
                <div class="card-body">
                    <div id="clientsList" class="clients-grid">
                        <div class="skel skel-row" style="width:100%;height:56px;border-radius:10px"></div>
                        <div class="skel skel-row" style="width:100%;height:56px;border-radius:10px"></div>
                        <div class="skel skel-row" style="width:100%;height:56px;border-radius:10px"></div>
                        <div class="skel skel-row" style="width:100%;height:56px;border-radius:10px"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dash-card">
                <div class="card-hd">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="qa-grid">
                        <a href="<?= $_base ?>/coach/availability" class="qa-btn"><i class="fas fa-clock"></i>Availability</a>
                        <a href="<?= $_base ?>/coach/sessions"     class="qa-btn"><i class="fas fa-dumbbell"></i>Sessions</a>
                        <a href="<?= $_base ?>/coach/clients"      class="qa-btn"><i class="fas fa-users"></i>My Clients</a>
                        <a href="<?= $_base ?>/coach/reviews"      class="qa-btn"><i class="fas fa-star"></i>Reviews</a>
                    </div>
                </div>
            </div>

            <!-- Recent Reviews (full width) -->
            <div class="dash-card span-full">
                <div class="card-hd">
                    <h3><i class="fas fa-star"></i> Recent Reviews</h3>
                    <a href="<?= $_base ?>/coach/reviews" class="card-link">View All</a>
                </div>
                <div class="card-body">
                    <div id="reviewsList" class="reviews-list">
                        <div class="skel skel-row" style="width:100%;height:56px;border-radius:10px;margin-bottom:0"></div>
                    </div>
                </div>
            </div>

        </div><!-- /dash-grid -->
    </div><!-- /cd-main -->
</div><!-- /coach-dashboard -->

<script>
// 
//  Helpers
// 
function fmtTime(t) {
    if (!t) return '';
    const [h, m] = t.split(':').map(Number);
    return `${h % 12 || 12}:${String(m).padStart(2,'0')} ${h >= 12 ? 'PM' : 'AM'}`;
}
function timeSince(d) {
    const secs = Math.floor((Date.now() - new Date(d)) / 1000);
    if (secs < 60)    return 'just now';
    if (secs < 3600)  return Math.floor(secs / 60) + 'm ago';
    if (secs < 86400) return Math.floor(secs / 3600) + 'h ago';
    return Math.floor(secs / 86400) + 'd ago';
}
function initials(first, last) {
    const a = first ? String(first)[0] : '';
    const b = last  ? String(last)[0]  : '';
    return (a + b).toUpperCase() || '?';
}
function statusPill(status) {
    const map = { confirmed:'pill-confirmed', pending:'pill-pending', completed:'pill-completed', cancelled:'pill-cancelled' };
    const s   = (status || '').toLowerCase();
    return `<span class="status-pill ${map[s]||''}">${s.charAt(0).toUpperCase()+s.slice(1)}</span>`;
}
function stars(n) {
    return Array.from({length:5}, (_,i) =>
        `<i class="fa${i < n ? 's' : 'r'} fa-star"></i>`
    ).join('');
}

// 
//  Init
// 
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('headerDate').textContent =
        new Date().toLocaleDateString('en-US', {weekday:'long', day:'numeric', month:'long', year:'numeric'});

    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    }

    loadAll();
});

// 
//  Load all data in parallel
// 
async function loadAll() {
    try {
        const [schedRes, profileRes, clientsRes, reviewsRes] = await Promise.all([
            fetch((window.BASE_URL||'')+'/api/coach/schedule'),
            fetch((window.BASE_URL||'')+'/api/coach/profile'),
            fetch((window.BASE_URL||'')+'/api/coach/clients'),
            fetch((window.BASE_URL||'')+'/api/coach/reviews?limit=5&sort=newest'),
        ]);

        const sched      = await schedRes.json();
        const profile    = await profileRes.json();
        const clientsData = await clientsRes.json();
        const revData    = await reviewsRes.json();

        if (sched.success)        renderStats(sched.stats, sched.today, sched.upcoming);
        if (!profile.error)       renderProfile(profile);
        if (clientsData.success)  renderClients(clientsData.clients || []);
        if (revData.success)      renderReviews(revData.data || []);

    } catch (err) {
        console.error('Dashboard load error:', err);
    }
}

// 
//  Profile header
// 
function renderProfile(p) {
    const fullName = p.fullName || ((p.first_name || '') + ' ' + (p.last_name || '')).trim();
    document.getElementById('headerName').textContent  = fullName || 'Coach';
    document.getElementById('headerSport').textContent = p.sport  || '';

    const avatarEl = document.getElementById('headerAvatar');
    if (p.avatar) {
        avatarEl.innerHTML = `<img src="${p.avatar}" alt="avatar">`;
    } else {
        avatarEl.textContent = initials(p.first_name, p.last_name);
    }

    const rating = parseFloat(p.stats && p.stats.rating ? p.stats.rating : 0);
    document.getElementById('statRating').textContent  = rating > 0 ? rating.toFixed(1) + ' ' : '—';
    document.getElementById('statReviews').textContent = ((p.stats && p.stats.reviews) || 0) + ' total reviews';
}

// 
//  Stats
// 
function renderStats(stats, today, upcoming) {
    const s         = stats || {};
    const monthEarn = parseFloat(s.monthly_earnings || 0);
    const totalEarn = parseFloat(s.total_earnings   || 0);

    document.getElementById('statMonthEarnings').textContent    = 'Rs. ' + monthEarn.toLocaleString('en-US', {maximumFractionDigits:0});
    document.getElementById('statTotalEarnings').textContent    = 'Total: Rs. ' + totalEarn.toLocaleString('en-US', {maximumFractionDigits:0});
    document.getElementById('statTotalSessions').textContent    = s.total_bookings || 0;
    document.getElementById('statCompletedSessions').textContent = (s.completed_sessions || 0) + ' completed';
    document.getElementById('statUpcoming').textContent         = s.upcoming_sessions || 0;
    document.getElementById('statToday').textContent            = (s.today_sessions || 0) + ' today';

    renderToday(today    || []);
    renderUpcoming(upcoming || []);
}

// 
//  Today's schedule
// 
function renderToday(sessions) {
    const el = document.getElementById('todayList');
    if (!sessions.length) {
        el.innerHTML = `<div class="empty-state"><i class="fas fa-sun"></i><p>No sessions scheduled for today.</p></div>`;
        return;
    }
    el.innerHTML = `<div class="sched-list">${sessions.map(s => {
        const type   = s.session_type || 'training';
        const dur    = s.duration     || '';
        const action = (s.status === 'confirmed')
            ? `<button class="btn-complete" onclick="markComplete(${s.id})">Complete</button>`
            : statusPill(s.status);
        return `
        <div class="sched-item ${s.status}">
            <div class="sched-time">
                <span class="t">${fmtTime(s.start_time)}</span>
                ${dur ? `<span class="dur">${dur} min</span>` : ''}
            </div>
            <div class="sched-info">
                <div class="client">${s.client_first_name || ''} ${s.client_last_name || ''}</div>
                <span class="type-pill">${type}</span>
            </div>
            <div class="sched-actions">${action}</div>
        </div>`;
    }).join('')}</div>`;
}

// 
//  Upcoming sessions (top 5)
// 
function renderUpcoming(sessions) {
    const el   = document.getElementById('upcomingList');
    const top5 = sessions.slice(0, 5);
    if (!top5.length) {
        el.innerHTML = `<div class="empty-state"><i class="fas fa-calendar"></i><p>No upcoming sessions.</p></div>`;
        return;
    }
    el.innerHTML = top5.map(s => {
        const dt  = new Date(s.booking_date + 'T00:00:00');
        const mon = dt.toLocaleDateString('en-US', {month:'short'}).toUpperCase();
        const day = dt.getDate();
        const amt = parseFloat(s.total_amount || 0);
        return `
        <div class="up-item">
            <div class="up-date-col">
                <span class="m">${mon}</span>
                <span class="d">${day}</span>
            </div>
            <div class="up-info">
                <div class="client">${s.client_first_name || ''} ${s.client_last_name || ''}</div>
                <div class="meta"><i class="fas fa-clock"></i> ${fmtTime(s.start_time)}</div>
            </div>
            <div class="up-right">
                <span class="amt">Rs. ${amt.toLocaleString('en-US',{maximumFractionDigits:0})}</span>
                ${statusPill(s.status)}
            </div>
        </div>`;
    }).join('');
}

// 
//  Recent clients (top 4)
// 
function renderClients(clients) {
    const el   = document.getElementById('clientsList');
    const top4 = clients.slice(0, 4);
    if (!top4.length) {
        el.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><i class="fas fa-user-friends"></i><p>No clients yet.</p></div>`;
        return;
    }
    el.innerHTML = top4.map(c => {
        const ini      = initials(c.first_name, c.last_name);
        const total    = c.total_sessions || 0;
        const isActive = (c.status === 'active');
        return `
        <div class="client-card">
            <div class="client-avatar">${ini}</div>
            <div class="client-details">
                <div class="client-name">${c.first_name || ''} ${c.last_name || ''}</div>
                <div class="client-meta">${total} session${total !== 1 ? 's' : ''}</div>
            </div>
            <div class="dot ${isActive ? 'dot-active' : 'dot-inactive'}" title="${isActive ? 'Active' : 'Inactive'}"></div>
        </div>`;
    }).join('');
}

// 
//  Recent reviews (top 3)
// 
function renderReviews(reviews) {
    const el = document.getElementById('reviewsList');
    if (!reviews.length) {
        el.innerHTML = `<div class="empty-state"><i class="fas fa-star"></i><p>No reviews yet.</p></div>`;
        return;
    }
    el.innerHTML = reviews.slice(0, 3).map(r => {
        const ini = initials(r.first_name, r.last_name);
        return `
        <div class="review-card">
            <div class="rev-avatar">${ini}</div>
            <div class="rev-body">
                <div class="rev-top">
                    <span class="rev-name">${r.first_name || ''} ${r.last_name || ''}</span>
                    <span class="rev-date">${timeSince(r.created_at)}</span>
                </div>
                <div class="rev-stars">${stars(parseInt(r.rating) || 0)}</div>
                <p class="rev-text">${r.review_text || ''}</p>
            </div>
        </div>`;
    }).join('');
}

// 
//  Mark session complete
// 
window.markComplete = async function(bookingId) {
    if (!confirm('Mark this session as completed?')) return;
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/bookings/${bookingId}/complete`, { method: 'PUT' });
        const data = await res.json();
        if (data.success) loadAll();
        else alert(data.error || 'Failed to update session.');
    } catch {
        alert('Network error. Please try again.');
    }
};
</script>
