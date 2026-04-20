<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- User Dashboard - partial, rendered via main.php layout -->

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
    }

    .dashboard-page {
        background: var(--background-light);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Header */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.9rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .dashboard-header p {
        opacity: 0.9;
        font-size: 1rem;
    }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .stat-info p {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Quick Actions */
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header h2 i {
        color: var(--primary-color);
    }

    .section-body {
        padding: 1.5rem;
    }

    /* Quick action grid */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        padding: 1.25rem 1rem;
        background: var(--background-light);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .action-btn i {
        font-size: 1.5rem;
    }

    /* Two-column layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 900px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Recent bookings list */
    .booking-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .booking-item:last-child {
        border-bottom: none;
    }

    .booking-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .booking-info { flex: 1; min-width: 0; }

    .booking-info .name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .booking-info .meta {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.1rem;
    }

    .badge {
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-confirmed, .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-pending                     { background: #fef3c7; color: #92400e; }
    .badge-cancelled                   { background: #fee2e2; color: #991b1b; }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.4;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }

    .btn-primary { background: var(--primary-color); color: white; }
    .btn-primary:hover { background: var(--primary-dark); color: white; }
    .btn-outline { background: transparent; color: var(--primary-color); border: 1px solid var(--primary-color); }
    .btn-outline:hover { background: var(--primary-light); }

    #recent-bookings-loading,
    #recent-orders-loading { padding: 1rem; text-align: center; color: var(--text-secondary); font-size: 0.9rem; }

    @media (max-width: 640px) {
        .dashboard-header { flex-direction: column; text-align: center; }
        .page-container { padding: 0 1rem; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="dashboard-page">
    <div class="page-container">

        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'there') ?>!</h1>
                <p>Here's an overview of your activity on GoPlay.</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_bookings'] ?? 0) ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="stat-info">
                    <h3>Rs. <?= number_format($stats['total_spent'] ?? 0, 0) ?></h3>
                    <p>Total Spent</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-circle"></i></div>
                <div class="stat-info">
                    <h3><?= ucfirst($user['status'] ?? 'Active') ?></h3>
                    <p>Account Status</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div class="section-body">
                <div class="quick-actions">
                    <a href="<?= $_base ?>/book-ground" class="action-btn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Book Ground</span>
                    </a>
                    <a href="<?= $_base ?>/book-coach" class="action-btn">
                        <i class="fas fa-user-tie"></i>
                        <span>Book Coach</span>
                    </a>
                    <a href="<?= $_base ?>/shop" class="action-btn">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Shop</span>
                    </a>
                    <a href="<?= $_base ?>/my-bookings" class="action-btn">
                        <i class="fas fa-calendar-alt"></i>
                        <span>My Bookings</span>
                    </a>
                    <a href="<?= $_base ?>/my-coach-sessions" class="action-btn">
                        <i class="fas fa-dumbbell"></i>
                        <span>Coach Sessions</span>
                    </a>
                    <a href="<?= $_base ?>/my-orders" class="action-btn">
                        <i class="fas fa-receipt"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="<?= $_base ?>/cart" class="action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Cart</span>
                    </a>
                    <a href="<?= $_base ?>/user/profile" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>My Profile</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent activity: bookings + orders side by side -->
        <div class="dashboard-grid">
            <!-- Recent Bookings -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-check"></i> Recent Bookings</h2>
                    <a href="<?= $_base ?>/my-bookings" class="btn btn-outline">View All</a>
                </div>
                <div class="section-body">
                    <div id="recent-bookings-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                    <div id="recent-bookings-list" style="display:none;"></div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-shopping-bag"></i> Recent Orders</h2>
                    <a href="<?= $_base ?>/my-orders" class="btn btn-outline">View All</a>
                </div>
                <div class="section-body">
                    <div id="recent-orders-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                    <div id="recent-orders-list" style="display:none;"></div>
                </div>
            </div>
        </div>

    </div><!-- /page-container -->
</div><!-- /dashboard-page -->

<script>
(function () {
    function badgeHtml(status) {
        const s = (status || '').toLowerCase();
        let cls = 'badge-pending';
        if (s === 'confirmed' || s === 'completed' || s === 'delivered') cls = 'badge-confirmed';
        if (s === 'cancelled') cls = 'badge-cancelled';
        return `<span class="badge ${cls}">${status}</span>`;
    }

    async function loadRecentBookings() {
        const loading = document.getElementById('recent-bookings-loading');
        const list    = document.getElementById('recent-bookings-list');
        try {
            const res  = await fetch((window.BASE_URL||'')+'/api/user/bookings?limit=5');
            const data = await res.json();
            const bookings = (data.bookings || []).slice(0, 5);
            loading.style.display = 'none';
            list.style.display    = 'block';
            if (!bookings.length) {
                list.innerHTML = '<div class="empty-state"><i class="fas fa-calendar-times"></i><p>No bookings yet</p></div>';
                return;
            }
            list.innerHTML = bookings.map(b => `
                <div class="booking-item">
                    <div class="booking-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="booking-info">
                        <div class="name">${escHtml(b.facility_name || b.ground_name || 'Booking')}</div>
                        <div class="meta">${escHtml(b.booking_date || b.date || '')} &bull; ${escHtml(b.start_time || '')}</div>
                    </div>
                    ${badgeHtml(b.status)}
                </div>`).join('');
        } catch (e) {
            loading.innerHTML = '<span style="color:#dc2626">Failed to load bookings</span>';
        }
    }

    async function loadRecentOrders() {
        const loading = document.getElementById('recent-orders-loading');
        const list    = document.getElementById('recent-orders-list');
        try {
            const res  = await fetch((window.BASE_URL||'')+'/api/user/orders?limit=5');
            const data = await res.json();
            const orders = (data.orders || []).slice(0, 5);
            loading.style.display = 'none';
            list.style.display    = 'block';
            if (!orders.length) {
                list.innerHTML = '<div class="empty-state"><i class="fas fa-shopping-bag"></i><p>No orders yet</p></div>';
                return;
            }
            list.innerHTML = orders.map(o => `
                <div class="booking-item">
                    <div class="booking-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="booking-info">
                        <div class="name">Order #${escHtml(String(o.id || o.order_id || ''))}</div>
                        <div class="meta">Rs. ${Number(o.total_amount || 0).toLocaleString()} &bull; ${escHtml(o.created_at ? o.created_at.split(' ')[0] : '')}</div>
                    </div>
                    ${badgeHtml(o.status)}
                </div>`).join('');
        } catch (e) {
            loading.innerHTML = '<span style="color:#dc2626">Failed to load orders</span>';
        }
    }

    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    loadRecentBookings();
    loadRecentOrders();
})();
</script>
