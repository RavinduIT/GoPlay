/* Ground Owner – Dashboard (IIFE) */
(function () {
    'use strict';

    const $ = id => document.getElementById(id);
    let chartInstance = null;

    /* ================================================================
       BOOT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        boot();
        bindSidebar();
    });

    async function boot() {
        await Promise.allSettled([
            loadProfile(),
            loadStats(),
            loadNotifCount(),
            loadReviews(),
            loadPendingApprovals()
        ]);
        loadChart(30);
        bindChartPeriod();
    }

    /* ================================================================
       PROFILE – header name / avatar
    ================================================================ */
    async function loadProfile() {
        try {
            const r = await fetch((window.BASE_URL||'')+'/api/ground-owner/profile');
            const d = await r.json();
            if (!d.success) return;

            const u  = d.profile.user          || {};
            const name = [u.first_name, u.last_name].filter(Boolean).join(' ') || u.username || 'Ground Owner';

            $('dbOwnerName').textContent = name;

            const av = $('dbAvatar');
            if (u.profile_picture) {
                av.innerHTML = `<img src="${esc(u.profile_picture)}" alt="${esc(name)}">`;
            } else {
                $('dbInitials').textContent = initials(name);
            }
        } catch (e) { /* silent */ }
    }

    /* ================================================================
       DASHBOARD STATS
    ================================================================ */
    async function loadStats() {
        try {
            const r = await fetch((window.BASE_URL||'')+'/api/ground-owner/dashboard-stats');
            if (r.status === 401) { window.location.href=(window.BASE_URL||'')+'/login'; return; }
            const d = await r.json();
            if (!d.success) return;

            const { stats, today_bookings, ground_performance } = d;

            /* --- Earnings --- */
            const monthEarnings = Number(stats.earnings?.this_month_earnings ?? 0);
            const totalEarnings  = Number(stats.earnings?.total_earnings    ?? 0);
            $('dbEarnings').textContent = fmt(monthEarnings);
            setChange('dbEarningsChange', `${fmt(totalEarnings)} total`, 'neutral');

            /* --- Bookings --- */
            const totalBook  = stats.bookings?.total_bookings      ?? 0;
            const monthBook  = stats.bookings?.this_month_bookings ?? 0;
            $('dbBookings').textContent = totalBook;
            setChange('dbBookingsChange', `${monthBook} this month`, 'positive');

            /* --- Grounds --- */
            const activeG = stats.grounds?.active_grounds ?? 0;
            const totalG  = stats.grounds?.total_grounds  ?? 0;
            $('dbGrounds').textContent = activeG;
            setChange('dbGroundsChange', `${totalG} total grounds`, 'neutral');

            /* --- Rating --- */
            const avg     = Number(stats.rating?.average       ?? 0);
            const revCount = stats.rating?.total_reviews ?? 0;
            $('dbRating').textContent = avg > 0 ? avg.toFixed(1) : '—';
            setChange('dbRatingChange', `${revCount} review${revCount !== 1 ? 's' : ''}`, 'positive');

            /* --- Today's Bookings --- */
            renderTodayBookings(today_bookings || []);

            /* --- Ground Performance --- */
            renderPerformance(ground_performance || []);

        } catch (e) {
            console.error('Dashboard stats error:', e);
        }
    }

    function setChange(id, text, type) {
        const el = $(id);
        if (!el) return;
        el.className = 'stat-change' + (type === 'positive' ? ' positive' : type === 'negative' ? ' negative' : '');
        el.innerHTML = `<span>${esc(text)}</span>`;
    }

    /* ================================================================
       TODAY'S BOOKINGS
    ================================================================ */
    function renderTodayBookings(bookings) {
        const el = $('dbTodayBookings');
        if (!el) return;

        if (!bookings.length) {
            el.innerHTML = `<div class="db-empty">
                <i class="fas fa-calendar-day"></i>
                <p>No bookings today</p>
            </div>`;
            return;
        }

        el.innerHTML = bookings.map(b => {
            const customerName = [(b.first_name || ''), (b.last_name || '')].join(' ').trim() || 'Customer';
            return `
            <div class="booking-item">
                <div class="booking-info">
                    <h4>${esc(b.facility_name || 'Facility')}</h4>
                    <p>${esc(customerName)}</p>
                    <span class="booking-details">${esc(b.start_time || '')} – ${esc(b.end_time || '')}</span>
                    <span class="booking-amount">${fmt(b.total_amount || 0)}</span>
                </div>
                <span class="status-badge ${esc(b.status || '')}">${cap(b.status)}</span>
            </div>`;
        }).join('');
    }

    /* ================================================================
       GROUND PERFORMANCE
    ================================================================ */
    function renderPerformance(grounds) {
        const el = $('dbPerformance');
        if (!el) return;

        if (!grounds.length) {
            el.innerHTML = `<div class="db-empty">
                <i class="fas fa-chart-bar"></i>
                <p>No performance data yet</p>
            </div>`;
            return;
        }

        const maxRev = Math.max(...grounds.map(g => Number(g.total_revenue || 0)), 1);

        el.innerHTML = grounds.slice(0, 5).map(g => {
            const rev = Number(g.total_revenue || 0);
            const pct = Math.round((rev / maxRev) * 100);
            const name = g.name || g.facility_name || 'Ground';
            return `
            <div class="performance-item">
                <div class="ground-info">
                    <h4>${esc(name)}</h4>
                    <div class="performance-stats">
                        <span class="bookings">${g.total_bookings ?? 0} bookings</span>
                        <span class="earnings">${fmt(rev)}</span>
                    </div>
                </div>
                <div class="performance-chart">
                    <div class="chart-bar" style="width:${pct}%"></div>
                    <span class="performance-rate">${pct}%</span>
                </div>
            </div>`;
        }).join('');
    }

    /* ================================================================
       EARNINGS CHART
    ================================================================ */
    async function loadChart(days) {
        const canvas = $('dbEarningsChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        try {
            const r = await fetch(`${window.BASE_URL||""}/api/ground-owner/earnings/trends?days=${days}`);
            const d = await r.json();

            let labels = [];
            let values = [];

            if (d.success !== false && d.trends) {
                labels = d.trends.labels  || [];
                values = d.trends.revenue || [];
            }

            if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Earnings (LKR)',
                        data: values,
                        borderColor: '#3b82f6',
                        backgroundColor: (c) => {
                            const g = c.chart.ctx.createLinearGradient(0, 0, 0, 380);
                            g.addColorStop(0, 'rgba(59,130,246,.22)');
                            g.addColorStop(1, 'rgba(59,130,246,.02)');
                            return g;
                        },
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17,24,39,.95)',
                            titleColor: '#fff',
                            bodyColor: '#e5e7eb',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: c => 'LKR ' + Number(c.parsed.y).toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 11 },
                                maxRotation: 0,
                                autoSkipPadding: 20
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(156,163,175,.1)', drawBorder: false },
                            border: { display: false },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 11 },
                                padding: 8,
                                callback: v => 'LKR ' + Number(v).toLocaleString()
                            }
                        }
                    }
                }
            });

        } catch (e) {
            canvas.parentElement.innerHTML = `<div class="db-empty" style="height:100%">
                <i class="fas fa-chart-line"></i>
                <p>Could not load earnings data</p>
            </div>`;
        }
    }

    function bindChartPeriod() {
        const sel = $('dbChartPeriod');
        if (!sel) return;
        sel.addEventListener('change', () => loadChart(parseInt(sel.value)));
    }

    /* ================================================================
       NOTIFICATION COUNT
    ================================================================ */
    async function loadNotifCount() {
        try {
            const r = await fetch((window.BASE_URL||'')+'/api/ground-owner/notifications/stats');
            const d = await r.json();
            const count = d.stats?.unread ?? 0;
            const el = $('dbNotifCount');
            if (!el) return;
            if (count > 0) {
                el.textContent = count > 99 ? '99+' : count;
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        } catch (e) { /* silent */ }
    }

    /* ================================================================
       RECENT REVIEWS
    ================================================================ */
    async function loadReviews() {
        const el = $('dbReviews');
        if (!el) return;
        try {
            const r = await fetch((window.BASE_URL||'')+'/api/ground-owner/reviews?limit=3');
            const d = await r.json();
            const reviews = d.reviews || [];

            if (!reviews.length) {
                el.innerHTML = `<div class="db-empty">
                    <i class="fas fa-star"></i>
                    <p>No reviews yet</p>
                </div>`;
                return;
            }

            el.innerHTML = reviews.slice(0, 3).map(rv => {
                const name  = [rv.first_name, rv.last_name].filter(Boolean).join(' ') || 'Customer';
                const stars = Array.from({ length: 5 }, (_, i) =>
                    `<i class="${i < (rv.rating || 0) ? 'fas' : 'far'} fa-star"></i>`
                ).join('');
                const ago  = timeAgo(rv.created_at);
                const text = rv.comment || rv.review_text || '';
                return `
                <div class="review-item">
                    <div class="reviewer-info">
                        <div class="db-avatar-sm">${initials(name)}</div>
                        <div class="reviewer-details">
                            <h5>${esc(name)}</h5>
                            <div class="review-rating">${stars}</div>
                        </div>
                        <span class="review-time">${ago}</span>
                    </div>
                    ${text ? `<p class="review-text">"${esc(text)}"</p>` : ''}
                    ${rv.facility_name ? `<span class="db-facility-tag"><i class="fas fa-map-marker-alt"></i> ${esc(rv.facility_name)}</span>` : ''}
                </div>`;
            }).join('');

        } catch (e) {
            el.innerHTML = `<div class="db-empty">
                <i class="fas fa-star"></i>
                <p>Could not load reviews</p>
            </div>`;
        }
    }

    /* ================================================================
       PENDING APPROVALS
    ================================================================ */
    async function loadPendingApprovals() {
        const list  = document.getElementById('pendingApprovalsList');
        const badge = document.getElementById('pendingBadge');
        if (!list) return;
        try {
            const r = await fetch((window.BASE_URL || '') + '/api/ground-owner/bookings?status=pending&limit=5');
            const d = await r.json();
            const bookings = d.bookings || [];

            if (badge) {
                if (bookings.length > 0) {
                    badge.textContent = bookings.length;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }

            if (bookings.length === 0) {
                list.innerHTML = '<p style="color:#64748b;padding:1rem 0;font-size:0.9rem;">No pending bookings — you are all caught up.</p>';
                return;
            }

            list.innerHTML = bookings.map(b => {
                const name  = esc(((b.first_name || '') + ' ' + (b.last_name || '')).trim() || 'Unknown');
                const ground = esc(b.facility_name || 'Ground');
                const date  = fmtDate(b.booking_date);
                const time  = (b.start_time || '').slice(0, 5) + ' - ' + (b.end_time || '').slice(0, 5);
                return `
                <div class="pending-item" id="pending-row-${b.id}">
                    <div class="pending-info">
                        <div class="pending-name">${name}</div>
                        <div class="pending-detail">${ground} &middot; ${date} &middot; ${time}</div>
                        <div class="pending-ago">${timeAgo(b.created_at)}</div>
                    </div>
                    <div class="pending-actions">
                        <button class="pa-btn pa-approve" onclick="approveBooking(${b.id})">Approve</button>
                        <button class="pa-btn pa-reject"  onclick="rejectBooking(${b.id})">Decline</button>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            if (list) list.innerHTML = '<p style="color:#ef4444;padding:1rem 0;font-size:0.9rem;">Could not load pending bookings.</p>';
        }
    }

    window.approveBooking = async function (id) {
        if (!confirm('Approve this booking?')) return;
        await updatePendingStatus(id, 'confirmed');
    };

    window.rejectBooking = async function (id) {
        if (!confirm('Decline this booking?')) return;
        await updatePendingStatus(id, 'cancelled');
    };

    async function updatePendingStatus(id, status) {
        try {
            const r = await fetch(`${window.BASE_URL || ''}/api/ground-owner/bookings/${id}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            });
            const d = await r.json();
            if (d.success) {
                const row = document.getElementById(`pending-row-${id}`);
                if (row) row.remove();
                // Update badge count
                const badge = document.getElementById('pendingBadge');
                if (badge) {
                    const current = parseInt(badge.textContent, 10) - 1;
                    if (current <= 0) {
                        badge.style.display = 'none';
                        const list = document.getElementById('pendingApprovalsList');
                        if (list && !list.querySelector('.pending-item')) {
                            list.innerHTML = '<p style="color:#64748b;padding:1rem 0;font-size:0.9rem;">No pending bookings — you are all caught up.</p>';
                        }
                    } else {
                        badge.textContent = current;
                    }
                }
                // Reload stats since counts changed
                loadStats();
            } else {
                alert(d.message || 'Failed to update booking.');
            }
        } catch (e) {
            alert('Error updating booking status.');
        }
    }

    /* ================================================================
       SIDEBAR
    ================================================================ */
    function bindSidebar() {
        window.addEventListener('resize', () => {
            const sb = document.getElementById('dashboardSidebar');
            if (sb && window.innerWidth > 768) sb.classList.remove('open');
        });
        document.addEventListener('click', e => {
            const sb = document.getElementById('dashboardSidebar');
            if (!sb || window.innerWidth > 768) return;
            if (sb.classList.contains('open') && !sb.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                sb.classList.remove('open');
            }
        });
    }

    /* ================================================================
       HELPERS
    ================================================================ */
    function fmt(val) {
        return 'LKR ' + Number(val || 0).toLocaleString('en-US', { maximumFractionDigits: 0 });
    }

    function initials(name) {
        return (name || '').trim().split(/\s+/).map(w => w[0]).join('').toUpperCase().slice(0, 2) || 'GO';
    }

    function cap(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
    }

    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const parts = String(dateStr).split(' ');
        const [y, mo, da] = parts[0].split('-').map(Number);
        const [h, mi, s]  = (parts[1] || '00:00:00').split(':').map(Number);
        const then = new Date(y, mo - 1, da, h, mi, s);
        const diff = Math.floor((Date.now() - then.getTime()) / 1000);
        if (diff < 60)     return 'just now';
        if (diff < 3600)   return Math.floor(diff / 60) + ' min ago';
        if (diff < 86400)  return Math.floor(diff / 3600) + ' hr ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
        return then.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

})();
