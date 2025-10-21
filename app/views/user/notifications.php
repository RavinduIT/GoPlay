<?php
$title = 'Notifications - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #dbeafe;
        --secondary-color: #0891b2;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --background-light: #f9fafb;
        --border-color: #e5e7eb;
        --muted-bg: #f3f4f6;
    }

    .notifications-page { background: var(--background-light); min-height: 100vh; padding: 2rem 0; }
    .page-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

    .page-header-section { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding: 2.5rem 2rem; border-radius: 16px; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2); display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 1rem; }
    .page-header-section h1 { font-size: 2rem; margin: 0; font-weight: 800; }
    .header-actions { display: flex; gap: .75rem; flex-wrap: wrap; }
    .btn { padding: 0.65rem 1rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: all .2s ease; text-decoration: none; }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: white; color: var(--primary-color); }
    .btn-outline { background: transparent; color: white; border: 2px solid rgba(255,255,255,.7); }
    .btn-danger { background: var(--danger-color); color: white; }

    .toolbar { background: white; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.05); padding: .75rem; display: grid; grid-template-columns: 1fr auto; align-items: center; gap: .75rem; margin-bottom: 1rem; }
    .search-input { display: flex; align-items: center; gap: .5rem; background: var(--muted-bg); padding: .5rem .75rem; border-radius: 10px; }
    .search-input input { border: none; outline: none; background: transparent; width: 100%; font-size: .95rem; color: var(--text-primary); }

    .filters { display: flex; gap: .5rem; flex-wrap: wrap; }
    .chip { border: 1px solid var(--border-color); background: white; color: var(--text-primary); padding: .4rem .7rem; border-radius: 999px; font-weight: 700; cursor: pointer; transition: all .15s ease; }
    .chip:hover { background: var(--primary-light); border-color: var(--primary-color); }
    .chip.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }

    .panel { background: white; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,.08); overflow: hidden; width: 100%; }

    .panel-header { display: flex; justify-content: space-between; align-items: center; padding: .9rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--background-light); }
    .panel-title { font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: .5rem; }

    .bulk-actions { display: flex; align-items: center; gap: .5rem; }
    .bulk-actions .btn { padding: .45rem .75rem; font-weight: 700; }

    .notif-item { display: grid; grid-template-columns: auto 1fr auto; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--border-color); transition: background .2s ease; }
    .notif-item:hover { background: #fbfdff; }
    .notif-item.unread { background: #f8fbff; }

    .notif-icon { width: 42px; height: 42px; border-radius: 10px; background: var(--primary-light); display: inline-flex; align-items: center; justify-content: center; color: var(--primary-color); }

    .notif-content { display: grid; gap: .3rem; }
    .notif-title { font-weight: 800; color: var(--text-primary); }
    .notif-meta { font-size: .85rem; color: var(--text-secondary); display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
    .notif-body { color: var(--text-primary); }

    .notif-actions { display: flex; align-items: center; gap: .4rem; }
    .icon-btn { background: transparent; border: none; width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-secondary); }
    .icon-btn:hover { background: var(--muted-bg); color: var(--text-primary); }

    .empty-state, .loading-state { text-align: center; padding: 3rem 1rem; color: var(--text-secondary); }
    .empty-state i { font-size: 3rem; color: var(--border-color); margin-bottom: .75rem; }

    .panel-footer { display: flex; justify-content: center; gap: .5rem; padding: .8rem; background: var(--background-light); }
    .pager { border: 1px solid var(--border-color); background: white; padding: .4rem .75rem; border-radius: 8px; cursor: pointer; color: var(--text-primary); font-weight: 700; }
    .pager[disabled] { opacity: .5; cursor: not-allowed; }

    @media (max-width: 768px) { .page-container { padding: 0 1rem; } .notif-item { grid-template-columns: auto 1fr; } .notif-actions { grid-column: 1 / -1; justify-content: flex-end; } }
</style>

<div class="notifications-page">
    <div class="page-container">
        <div class="page-header-section">
            <div>
                <h1><i class="fas fa-bell"></i> Notifications</h1>
                <div style="opacity:.95">Stay on top of orders, bookings, and system updates</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="notifications.markAllRead()"><i class="fas fa-check-double"></i> Mark all read</button>
                <button class="btn btn-outline" onclick="notifications.toggleMuted()"><i class="fas fa-bell-slash"></i> Toggle mute</button>
                <button class="btn btn-danger" onclick="notifications.clearAll()"><i class="fas fa-trash"></i> Clear all</button>
            </div>
        </div>

        <div class="toolbar">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input id="searchBox" type="text" placeholder="Search notifications (e.g., order #, coach, venue, status)" oninput="notifications.applyFilters()"/>
            </div>
            <div class="filters">
                <span class="chip active" data-filter="all" onclick="notifications.switchFilter(this)"><i class="fas fa-layer-group"></i> All</span>
                <span class="chip" data-filter="unread" onclick="notifications.switchFilter(this)"><i class="fas fa-envelope-open"></i> Unread</span>
                <span class="chip" data-filter="orders" onclick="notifications.switchFilter(this)"><i class="fas fa-box"></i> Orders</span>
                <span class="chip" data-filter="bookings" onclick="notifications.switchFilter(this)"><i class="fas fa-calendar-check"></i> Bookings</span>
                <span class="chip" data-filter="coaches" onclick="notifications.switchFilter(this)"><i class="fas fa-user-tie"></i> Coaches</span>
                <span class="chip" data-filter="system" onclick="notifications.switchFilter(this)"><i class="fas fa-gear"></i> System</span>
                <span class="chip" data-filter="promotions" onclick="notifications.switchFilter(this)"><i class="fas fa-tags"></i> Promotions</span>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fas fa-inbox"></i> Inbox <span id="inboxCount" style="font-weight:600; opacity:.7; margin-left:.4rem;">(0)</span></div>
                <div class="bulk-actions">
                    <label style="display:flex; align-items:center; gap:.5rem;">
                        <input id="selectAll" type="checkbox" onchange="notifications.toggleSelectAll(this)"/>
                        <span style="font-weight:700; color:var(--text-secondary)">Select All</span>
                    </label>
                    <button class="btn" onclick="notifications.bulkMarkRead()"><i class="fas fa-envelope-open"></i> Read</button>
                    <button class="btn" onclick="notifications.bulkMarkUnread()"><i class="fas fa-envelope"></i> Unread</button>
                    <button class="btn" onclick="notifications.bulkDelete()"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>

            <div id="notifLoading" class="loading-state"><i class="fas fa-spinner fa-spin"></i><div>Loading notifications...</div></div>
            <div id="notifList" style="display:none"></div>
            <div id="notifEmpty" class="empty-state" style="display:none"><i class="fas fa-bell-slash"></i><div>No notifications to show</div></div>

            <div class="panel-footer">
                <button id="prevBtn" class="pager" onclick="notifications.prevPage()" disabled><i class="fas fa-chevron-left"></i> Prev</button>
                <div id="pageInfo" class="pager" style="pointer-events:none">Page 1</div>
                <button id="nextBtn" class="pager" onclick="notifications.nextPage()" disabled>Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
class NotificationsPage {
    constructor() {
        this.all = [];
        this.filtered = [];
        this.page = 1;
        this.perPage = 10;
        this.activeFilter = 'all';
        this.muted = false;
        this.selected = new Set();
        this.init();
    }

    async init() {
        await this.fetchNotifications();
        this.applyFilters();
    }

    async fetchNotifications() {
        try {
            const res = await fetch('/api/user/notifications');
            if (res.status === 401) { window.location.href = '/login'; return; }
            const data = await res.json();
            if (data.success) {
                this.all = data.notifications || [];
            }
        } catch (e) { console.error(e); }
    }

    switchFilter(el) {
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        this.activeFilter = el.dataset.filter;
        this.page = 1;
        this.applyFilters();
    }

    applyFilters() {
        const q = (document.getElementById('searchBox').value || '').toLowerCase();
        this.filtered = this.all.filter(n => {
            const matchesText = !q || [n.title, n.message, n.type, n?.meta?.order_id, n?.meta?.booking_id]
                .filter(Boolean).join(' ').toLowerCase().includes(q);

            const typeMap = {
                'all': true,
                'unread': n.read === false,
                'orders': n.type === 'order',
                'bookings': n.type === 'booking',
                'coaches': n.type === 'coach',
                'system': n.type === 'system',
                'promotions': n.type === 'promotion'
            };
            const matchesType = typeMap[this.activeFilter];
            return matchesText && matchesType;
        });

        this.updateCounts();
        this.render();
    }

    updateCounts() {
        document.getElementById('inboxCount').textContent = `(${this.filtered.length})`;
    }

    render() {
        const loading = document.getElementById('notifLoading');
        const list = document.getElementById('notifList');
        const empty = document.getElementById('notifEmpty');
        loading.style.display = 'none';

        const total = this.filtered.length;
        if (total === 0) { empty.style.display = 'block'; list.style.display = 'none'; this.updatePager(0); return; }
        empty.style.display = 'none'; list.style.display = 'block';

        const start = (this.page - 1) * this.perPage;
        const slice = this.filtered.slice(start, start + this.perPage);
        list.innerHTML = slice.map(n => this.notifHTML(n)).join('');
        this.updatePager(total);
    }

    notifHTML(n) {
        const icon = this.iconFor(n);
        const when = n.created_at ? new Date(n.created_at).toLocaleString() : '';
        const unread = n.read === false ? 'unread' : '';
        const checked = this.selected.has(n.id) ? 'checked' : '';

        return `
        <div class="notif-item ${unread}">
            <div class="notif-icon">${icon}</div>
            <div class="notif-content">
                <div class="notif-title">
                    <input type="checkbox" onchange="notifications.toggleSelect(${n.id}, this.checked)" ${checked} style="margin-right:.5rem">
                    ${this.badgeFor(n)} ${n.title || 'Notification'}
                </div>
                <div class="notif-meta">
                    <span><i class="fas fa-clock"></i> ${when}</span>
                    ${n.meta?.order_id ? `<span><i class='fas fa-hashtag'></i> Order #${n.meta.order_id}</span>` : ''}
                    ${n.meta?.booking_id ? `<span><i class='fas fa-hashtag'></i> Booking #${n.meta.booking_id}</span>` : ''}
                </div>
                ${n.message ? `<div class="notif-body">${n.message}</div>` : ''}
            </div>
            <div class="notif-actions">
                <button class="icon-btn" title="${n.read ? 'Mark as unread' : 'Mark as read'}" onclick="notifications.toggleRead(${n.id})">
                    <i class="fas ${n.read ? 'fa-envelope' : 'fa-envelope-open'}"></i>
                </button>
                ${this.primaryActionButton(n)}
                <button class="icon-btn" title="Delete" onclick="notifications.delete(${n.id})"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    }

    iconFor(n) {
        const map = { order: 'fa-box', booking: 'fa-calendar-check', coach: 'fa-user-tie', system: 'fa-gear', promotion: 'fa-tags', default: 'fa-bell' };
        return `<i class="fas ${map[n.type] || map.default}"></i>`;
    }

    badgeFor(n) {
        const sev = n.severity || 'info';
        const colors = { info: '#2563eb', success: '#059669', warning: '#d97706', danger: '#dc2626' };
        const label = { info: 'Info', success: 'Success', warning: 'Warning', danger: 'Alert' }[sev];
        return `<span style="display:inline-flex;align-items:center;gap:.35rem;background:${colors[sev]}20;color:${colors[sev]};font-weight:800;font-size:.75rem;padding:.25rem .5rem;border-radius:999px;margin-right:.35rem"><i class='fas fa-circle'></i>${label}</span>`;
    }

    primaryActionButton(n) {
        if (n.type === 'order' && n.meta?.order_id) return `<a class="icon-btn" title="View order" href="/orders/${n.meta.order_id}"><i class="fas fa-eye"></i></a>`;
        if (n.type === 'booking' && n.meta?.booking_id) return `<a class="icon-btn" title="View booking" href="/my-bookings?highlight=${n.meta.booking_id}"><i class="fas fa-eye"></i></a>`;
        if (n.type === 'coach' && n.meta?.coach_id) return `<a class="icon-btn" title="View coach" href="/coaches/${n.meta.coach_id}"><i class="fas fa-user"></i></a>`;
        return '';
    }

    toggleSelect(id, isChecked) { if (isChecked) this.selected.add(id); else this.selected.delete(id); }
    toggleSelectAll(cb) {
        const start = (this.page - 1) * this.perPage;
        const slice = this.filtered.slice(start, start + this.perPage);
        slice.forEach(n => cb.checked ? this.selected.add(n.id) : this.selected.delete(n.id));
        this.render();
    }
    get selectedArray() { return Array.from(this.selected); }
    async bulkMarkRead() { await this.bulk('/api/user/notifications/bulk/read'); }
    async bulkMarkUnread() { await this.bulk('/api/user/notifications/bulk/unread'); }
    async bulkDelete() { if (!confirm('Delete selected notifications?')) return; await this.bulk('/api/user/notifications/bulk/delete', { method: 'DELETE' }); }
    async bulk(url, extra = {}) {
        if (this.selected.size === 0) { alert('Select at least one notification'); return; }
        try {
            const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ ids: this.selectedArray }), ...extra });
            const data = await res.json();
            if (data.success) { await this.fetchNotifications(); this.selected.clear(); this.applyFilters(); }
        } catch (e) { console.error(e); }
    }

    async toggleRead(id) {
        try {
            const notif = this.all.find(n => n.id === id);
            const method = notif.read ? 'unread' : 'read';
            const res = await fetch(`/api/user/notifications/${id}/${method}`, { method: 'PUT' });
            const data = await res.json();
            if (data.success) { notif.read = !notif.read; this.applyFilters(); }
        } catch (e) { console.error(e); }
    }

    async delete(id) {
        if (!confirm('Delete this notification?')) return;
        try {
            const res = await fetch(`/api/user/notifications/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) { this.all = this.all.filter(n => n.id !== id); this.applyFilters(); }
        } catch (e) { console.error(e); }
    }

    async markAllRead() {
        try {
            const res = await fetch('/api/user/notifications/mark-all-read', { method: 'PUT' });
            const data = await res.json();
            if (data.success) { this.all.forEach(n => n.read = true); this.applyFilters(); }
        } catch (e) { console.error(e); }
    }

    async clearAll() {
        if (!confirm('Clear all notifications?')) return;
        try {
            const res = await fetch('/api/user/notifications/clear', { method: 'DELETE' });
            const data = await res.json();
            if (data.success) { this.all = []; this.applyFilters(); }
        } catch (e) { console.error(e); }
    }

    toggleMuted() { this.muted = !this.muted; alert(this.muted ? 'Notifications muted' : 'Notifications unmuted'); }

    updatePager(total) {
        const pages = Math.max(1, Math.ceil(total / this.perPage));
        document.getElementById('pageInfo').textContent = `Page ${this.page} / ${pages}`;
        document.getElementById('prevBtn').disabled = this.page <= 1;
        document.getElementById('nextBtn').disabled = this.page >= pages;
        const sel = document.getElementById('selectAll'); if (sel) sel.checked = false;
    }
    nextPage() { this.page += 1; this.render(); }
    prevPage() { this.page = Math.max(1, this.page - 1); this.render(); }
}

const notifications = new NotificationsPage();
</script>

<!-- ✅ DEMO NOTIFICATIONS ADDED HERE -->
<script>
window.addEventListener('load', () => {
    notifications.all = [
      { id: 1, type: 'order', severity: 'success', title: 'Order #45231 Confirmed', message: 'Your sports gear order was placed successfully.', created_at: new Date().toISOString(), read: false, meta: { order_id: 45231 }},
      { id: 2, type: 'booking', severity: 'info', title: 'Ground Booking Approved', message: 'Your booking for Royal Turf Ground is confirmed.', created_at: new Date().toISOString(), read: false, meta: { booking_id: 9273 }},
      { id: 3, type: 'promotion', severity: 'success', title: '🔥 25% Off Weekend Offer!', message: 'Grab discounts on all football items.', created_at: new Date().toISOString(), read: true },
      { id: 4, type: 'system', severity: 'warning', title: 'Password Change Recommended', message: 'Your password is 90 days old. Consider updating it.', created_at: new Date().toISOString(), read: true }
    ];
    notifications.applyFilters();
});
</script>
