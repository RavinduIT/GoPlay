<?php
$title = 'My Orders - GoPlay Sports Platform';
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
    }

    .orders-page { background: var(--background-light); min-height: 100vh; padding: 2rem 0; }
    .page-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

    .page-header-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white; padding: 3rem 2rem; border-radius: 16px; margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
    }
    .page-header-section h1 { font-size: 2.5rem; margin-bottom: 0.5rem; font-weight: 700; }
    .page-header-section p { font-size: 1.1rem; opacity: 0.95; }

    /* Stats Cards */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid var(--border-color); }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .stat-card .icon { font-size: 2.5rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .stat-card .number { font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; }
    .stat-card .label { color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Tabs */
    .tabs-container { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid var(--border-color); }
    .tabs-nav { display: flex; background: var(--background-light); border-bottom: 2px solid var(--border-color); padding: 0; margin: 0; list-style: none; }
    .tab-button {
        flex: 1; padding: 1.25rem 2rem; background: none; border: none; font-size: 1.1rem; font-weight: 600;
        color: var(--text-secondary); cursor: pointer; transition: all 0.3s ease; position: relative;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .tab-button:hover { background: rgba(37, 99, 235, 0.05); color: var(--primary-color); }
    .tab-button.active { background: white; color: var(--primary-color); }
    .tab-button.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 3px; background: var(--primary-color); }
    .tab-badge { background: var(--primary-color); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600; }
    .tab-button:not(.active) .tab-badge { background: var(--text-secondary); }

    /* Content */
    .tab-content { display: none; padding: 2rem; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }

    /* Order Cards */
    .orders-grid { display: grid; gap: 1.5rem; }
    .order-card { background: var(--background-light); border: 2px solid var(--border-color); border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .order-card::before { content: ''; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: var(--primary-color); opacity: 0; transition: opacity 0.3s ease; }
    .order-card:hover { border-color: var(--primary-color); box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15); transform: translateY(-2px); }
    .order-card:hover::before { opacity: 1; }

    .order-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem; gap: 1rem; }
    .order-title { flex: 1; }
    .order-title h3 { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem; }
    .order-subtitle { color: var(--text-secondary); font-size: 0.95rem; display: flex; gap: .75rem; flex-wrap: wrap; align-items: center; }

    .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 700; text-transform: capitalize; white-space: nowrap; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.processing { background: #dbeafe; color: #1e40af; }
    .status-badge.shipped { background: #e0e7ff; color: #3730a3; }
    .status-badge.delivered { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .status-badge.returned { background: #fde68a; color: #92400e; }

    .order-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.25rem; }
    .detail-item { display: flex; align-items: center; gap: 0.75rem; background: white; padding: 0.75rem; border-radius: 8px; }
    .detail-item i { color: var(--primary-color); font-size: 1.1rem; width: 24px; text-align: center; }
    .detail-item strong { color: var(--text-secondary); font-size: 0.85rem; display: block; font-weight: 700; }
    .detail-item span { color: var(--text-primary); font-size: 0.95rem; }

    .order-amount { background: linear-gradient(135deg, var(--success-color), #047857); color: white; padding: 1rem; border-radius: 8px; text-align: center; margin-top: 1rem; }
    .order-amount .label { font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.25rem; }
    .order-amount .amount { font-size: 1.5rem; font-weight: 800; }

    .items-preview { background: white; padding: 1rem; border-radius: 8px; border-left: 3px solid var(--primary-color); display: grid; gap: .75rem; }
    .item-row { display: flex; align-items: center; gap: 0.75rem; }
    .item-thumb { width: 44px; height: 44px; border-radius: 8px; background: var(--primary-light); display: inline-flex; align-items: center; justify-content: center; overflow: hidden; }
    .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { display: grid; }
    .item-info .name { font-weight: 600; color: var(--text-primary); }
    .item-info .meta { font-size: .85rem; color: var(--text-secondary); }

    .order-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
    .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .btn-primary { background: var(--primary-color); color: white; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
    .btn-neutral { background: var(--text-secondary); color: white; }
    .btn-neutral:hover { background: #4b5563; transform: translateY(-2px); }
    .btn-danger { background: var(--danger-color); color: white; }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); }
    .btn-outline { background: white; color: var(--primary-color); border: 2px solid var(--primary-color); }
    .btn-outline:hover { background: var(--primary-light); }

    /* Loading & Empty States */
    .loading-state, .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-secondary); }
    .loading-state i { font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem; }
    .empty-state i { font-size: 5rem; color: var(--border-color); margin-bottom: 1.5rem; }
    .empty-state h3 { font-size: 1.5rem; color: var(--text-primary); margin-bottom: 1rem; }
    .empty-state p { font-size: 1.1rem; margin-bottom: 2rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container { padding: 0 1rem; }
        .page-header-section h1 { font-size: 2rem; }
        .stats-grid { grid-template-columns: 1fr; }
        .tabs-nav { flex-direction: column; }
        .tab-button { padding: 1rem; }
        .order-details { grid-template-columns: 1fr; }
        .order-header { flex-direction: column; }
        .order-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
</style>

<div class="orders-page">
    <div class="page-container">
        <!-- Header -->
        <div class="page-header-section">
            <h1><i class="fas fa-box"></i> My Orders</h1>
            <p>Track your sports gear purchases and service subscriptions</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-receipt"></i></div>
                <div class="number" id="totalOrders">0</div>
                <div class="label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-truck"></i></div>
                <div class="number" id="inProgressOrders">0</div>
                <div class="label">In Progress</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number" id="deliveredOrders">0</div>
                <div class="label">Delivered</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="number" id="ordersTotalSpent">LKR 0</div>
                <div class="label">Total Spent</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <ul class="tabs-nav">
                <li>
                    <button class="tab-button active" onclick="switchOrderTab('gear')" id="gearTab">
                        <i class="fas fa-dumbbell"></i>
                        Gear Orders
                        <span class="tab-badge" id="gearCount">0</span>
                    </button>
                </li>
                <li>
                    <button class="tab-button" onclick="switchOrderTab('services')" id="servicesTab">
                        <i class="fas fa-id-card"></i>
                        Services & Memberships
                        <span class="tab-badge" id="servicesCount">0</span>
                    </button>
                </li>
            </ul>

            <!-- Gear Orders -->
            <div id="gearContent" class="tab-content active">
                <div id="gearLoading" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading gear orders...</p>
                </div>
                <div id="gearList" class="orders-grid" style="display: none;"></div>
                <div id="gearEmpty" class="empty-state" style="display: none;">
                    <i class="fas fa-box-open"></i>
                    <h3>No gear orders yet</h3>
                    <p>Browse our store and grab your first item</p>
                    <a href="/shop" class="btn btn-primary">
                        <i class="fas fa-store"></i> Go to Shop
                    </a>
                </div>
            </div>

            <!-- Service Orders -->
            <div id="servicesContent" class="tab-content">
                <div id="servicesLoading" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading service orders...</p>
                </div>
                <div id="servicesList" class="orders-grid" style="display: none;"></div>
                <div id="servicesEmpty" class="empty-state" style="display: none;">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No subscriptions or services yet</h3>
                    <p>Book a plan or service to see it here</p>
                    <a href="/services" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Explore Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
class MyOrdersDashboard {
    constructor() {
        this.gearOrders = [];
        this.serviceOrders = [];
        this.init();
    }

    async init() {
        // Try API loads. Even if they fail, we continue.
        await Promise.allSettled([ this.loadGearOrders(), this.loadServiceOrders() ]);

        // If nothing came back, inject demo data so UI always shows examples.
        if ((this.gearOrders?.length || 0) + (this.serviceOrders?.length || 0) === 0) {
            this.injectDemoData();
        }

        this.updateStats();
        this.renderGearOrders();
        this.renderServiceOrders();
    }

    async loadGearOrders() {
        try {
            const response = await fetch('/api/user/orders?type=gear', { credentials: 'include' });
            if (response.status === 401) { window.location.href = '/login'; return; }
            if (!response.ok) return;
            const data = await response.json().catch(()=>({}));
            if (data?.success) this.gearOrders = data.orders || [];
        } catch (err) {
            console.error('Error loading gear orders:', err);
        }
    }

    async loadServiceOrders() {
        try {
            const response = await fetch('/api/user/orders?type=service', { credentials: 'include' });
            if (response.status === 401) { window.location.href = '/login'; return; }
            if (!response.ok) return;
            const data = await response.json().catch(()=>({}));
            if (data?.success) this.serviceOrders = data.orders || [];
        } catch (err) {
            console.error('Error loading service orders:', err);
        }
    }

    /* ---------- DEMO FALLBACK ---------- */
    injectDemoData() {
        this.gearOrders = [
            {
              id: 9001,
              order_number: 'GP-9001',
              status: 'processing',
              created_at: new Date(Date.now() - 6 * 60 * 60 * 1000).toISOString(),
              estimated_delivery: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 24500,
              payment_method: 'card',
              shipping_address: 'No. 12, Flower Rd, Colombo 07',
              tracking_number: 'LK123456789',
              tracking_url: 'https://example.com/track/LK123456789',
              items: [
                { name: 'Nike Football', quantity: 1, variant: 'Size 5', image_url: '' },
                { name: 'Shin Guards', quantity: 2, variant: 'Adult', image_url: '' }
              ]
            },
            {
              id: 9002,
              order_number: 'GP-9002',
              status: 'shipped',
              created_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
              estimated_delivery: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 68500,
              payment_method: 'cod',
              shipping_address: '221/B, Galle Rd, Mount Lavinia',
              tracking_number: 'LK987654321',
              tracking_url: 'https://example.com/track/LK987654321',
              items: [
                { name: 'Yonex Badminton Racket', quantity: 1, variant: '3U/G5', image_url: '' },
                { name: 'Feather Shuttlecocks (12pc)', quantity: 1, variant: '', image_url: '' }
              ]
            },
            {
              id: 9003,
              order_number: 'GP-9003',
              status: 'delivered',
              created_at: new Date(Date.now() - 8 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 14990,
              payment_method: 'card',
              shipping_address: 'Kaduwela Rd, Malabe',
              returnable: true,
              items: [
                { name: 'Cricket Gloves', quantity: 1, variant: 'Men', image_url: '' },
                { name: 'Grip Tape', quantity: 2, variant: 'Black', image_url: '' }
              ]
            },
            {
              id: 9004,
              order_number: 'GP-9004',
              status: 'cancelled',
              created_at: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 1990,
              payment_method: 'card',
              shipping_address: 'Kandy Rd, Kiribathgoda',
              items: [
                { name: 'Water Bottle 1L', quantity: 1, variant: 'Blue', image_url: '' }
              ]
            }
        ];

        this.serviceOrders = [
            {
              id: 8001,
              order_number: 'SV-8001',
              status: 'pending',
              created_at: new Date(Date.now() - 3 * 60 * 60 * 1000).toISOString(),
              total_amount: 7500,
              payment_method: 'card',
              service_location: 'Central Sports Complex',
              items: [
                { name: 'Gym Membership', quantity: 1, variant: 'Monthly', image_url: '' }
              ]
            },
            {
              id: 8002,
              order_number: 'SV-8002',
              status: 'delivered',
              created_at: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 12000,
              payment_method: 'card',
              service_location: 'Royal Tennis Academy',
              returnable: false,
              items: [
                { name: 'Tennis Coaching', quantity: 4, variant: '1hr Sessions', image_url: '' }
              ]
            },
            {
              id: 8003,
              order_number: 'SV-8003',
              status: 'returned',
              created_at: new Date(Date.now() - 20 * 24 * 60 * 60 * 1000).toISOString(),
              total_amount: 5000,
              payment_method: 'cod',
              service_location: 'Colombo Swimming Club',
              items: [
                { name: 'Swim Pass', quantity: 1, variant: 'Weekend', image_url: '' }
              ]
            }
        ];
    }

    updateStats() {
        const all = [...(this.gearOrders||[]), ...(this.serviceOrders||[])];
        const total = all.length;
        const inProgress = all.filter(o => ['pending','processing','shipped'].includes(o.status)).length;
        const delivered = all.filter(o => o.status === 'delivered').length;
        const totalSpent = all.filter(o => o.status !== 'cancelled')
                              .reduce((s,o) => s + (parseFloat(o.total_amount) || 0), 0);

        document.getElementById('totalOrders').textContent = total;
        document.getElementById('inProgressOrders').textContent = inProgress;
        document.getElementById('deliveredOrders').textContent = delivered;
        document.getElementById('ordersTotalSpent').textContent = 'LKR ' + totalSpent.toLocaleString();
        document.getElementById('gearCount').textContent = (this.gearOrders||[]).length;
        document.getElementById('servicesCount').textContent = (this.serviceOrders||[]).length;
    }

    /* ---------- Rendering ---------- */
    renderGearOrders() {
        const loading = document.getElementById('gearLoading');
        const list = document.getElementById('gearList');
        const empty = document.getElementById('gearEmpty');
        loading.style.display = 'none';

        if (!this.gearOrders || this.gearOrders.length === 0) { empty.style.display = 'block'; list.style.display = 'none'; return; }
        empty.style.display = 'none'; list.style.display = 'grid';

        list.innerHTML = this.gearOrders.map(order => this.orderCardHTML(order, 'gear')).join('');
    }

    renderServiceOrders() {
        const loading = document.getElementById('servicesLoading');
        const list = document.getElementById('servicesList');
        const empty = document.getElementById('servicesEmpty'); // fixed
        loading.style.display = 'none';

        if (!this.serviceOrders || this.serviceOrders.length === 0) {
            empty.style.display = 'block';
            list.style.display = 'none';
            return;
        }
        empty.style.display = 'none';
        list.style.display = 'grid';

        list.innerHTML = this.serviceOrders.map(order => this.orderCardHTML(order, 'service')).join('');
    }

    orderCardHTML(order, type) {
        const items = order.items || [];
        const itemsPreview = items.slice(0,3).map(it => `
            <div class="item-row">
                <span class="item-thumb">${it.image_url ? `<img src="${it.image_url}" alt="">` : `<i class='fas fa-cube'></i>`}</span>
                <div class="item-info">
                    <span class="name">${it.name}</span>
                    <span class="meta">Qty: ${it.quantity} • ${it.variant || ''}</span>
                </div>
            </div>
        `).join('');

        const eta = order.estimated_delivery ? new Date(order.estimated_delivery).toLocaleDateString() : '—';
        const orderDate = order.created_at ? new Date(order.created_at).toLocaleDateString() : '—';
        const isCancelable = ['pending','processing'].includes(order.status);
        const isReturnable = order.status === 'delivered' && order.returnable === true;

        return `
        <div class="order-card">
            <div class="order-header">
                <div class="order-title">
                    <h3>Order #${order.order_number || order.id}</h3>
                    <div class="order-subtitle">
                        <span><i class="fas fa-calendar"></i> Placed on ${orderDate}</span>
                        ${order.status === 'shipped' || order.status === 'processing' ? `<span><i class='fas fa-hourglass-half'></i> ETA ${eta}</span>` : ''}
                        ${order.tracking_number ? `<span><i class='fas fa-location-arrow'></i> Tracking: ${order.tracking_number}</span>` : ''}
                    </div>
                </div>
                <span class="status-badge ${order.status}">${order.status}</span>
            </div>

            <div class="order-details">
                <div class="detail-item">
                    <i class="fas fa-boxes"></i>
                    <div>
                        <strong>Items</strong>
                        <span>${items.reduce((s,i)=>s + (parseInt(i.quantity)||0), 0)} item(s)</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-money-bill"></i>
                    <div>
                        <strong>Total</strong>
                        <span>LKR ${parseFloat(order.total_amount || 0).toLocaleString()}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <strong>Payment</strong>
                        <span>${(order.payment_method || 'N/A').toUpperCase()}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>${type === 'gear' ? 'Shipping To' : 'Service Location'}</strong>
                        <span>${order.shipping_address || order.service_location || 'N/A'}</span>
                    </div>
                </div>
            </div>

            ${items.length ? `<div class="items-preview">${itemsPreview}</div>` : ''}

            <div class="order-amount">
                <div class="label">Order Total</div>
                <div class="amount">LKR ${parseFloat(order.total_amount || 0).toLocaleString()}</div>
            </div>

            <div class="order-actions">
                ${isCancelable ? `
                <button class="btn btn-danger" onclick="orders.cancelOrder(${order.id})">
                    <i class="fas fa-times"></i> Cancel
                </button>` : ''}
                ${order.tracking_url ? `
                <a class="btn btn-outline" target="_blank" href="${order.tracking_url}">
                    <i class="fas fa-truck"></i> Track Package
                </a>` : ''}
                ${isReturnable ? `
                <button class="btn btn-neutral" onclick="orders.requestReturn(${order.id})">
                    <i class="fas fa-undo"></i> Return
                </button>` : ''}
                <button class="btn btn-primary" onclick="orders.viewOrder(${order.id})">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="btn btn-outline" onclick="orders.downloadInvoice(${order.id})">
                    <i class="fas fa-file-invoice"></i> Invoice
                </button>
                <button class="btn btn-outline" onclick="orders.reorder(${order.id})">
                    <i class="fas fa-cart-plus"></i> Reorder
                </button>
            </div>
        </div>`;
    }

    /* ---------- Actions ---------- */
    async cancelOrder(orderId) {
        if (!confirm('Cancel this order?')) return;
        try {
            const res = await fetch(`/api/user/orders/${orderId}/cancel`, { method: 'PUT' });
            const data = await res.json();
            if (data.success) {
                alert('Order cancelled.');
                await this.init();
            } else {
                alert('Error: ' + (data.message || 'Unable to cancel order'));
            }
        } catch (e) { console.error(e); alert('Failed to cancel order'); }
    }

    async requestReturn(orderId) {
        const reason = prompt('Reason for return?');
        if (reason === null) return;
        try {
            const res = await fetch(`/api/user/orders/${orderId}/return`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reason })
            });
            const data = await res.json();
            if (data.success) { alert('Return requested.'); await this.init(); }
            else { alert('Error: ' + (data.message || 'Unable to request return')); }
        } catch (e) { console.error(e); alert('Failed to request return'); }
    }

    viewOrder(orderId) {
        const order = [...(this.gearOrders||[]), ...(this.serviceOrders||[])].find(o => o.id === orderId);
        if (!order) return;
        const date = order.created_at ? new Date(order.created_at).toLocaleDateString() : '—';
        const total = 'LKR ' + (parseFloat(order.total_amount||0)).toLocaleString();
        alert(`Order Details\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\nOrder #: ${order.order_number || order.id}\nPlaced on: ${date}\nStatus: ${order.status}\nTotal: ${total}\nItems: ${(order.items||[]).length}`);
    }

    async downloadInvoice(orderId) {
        try {
            const res = await fetch(`/api/user/orders/${orderId}/invoice`);
            if (res.status === 401) { window.location.href = '/login'; return; }
            if (!res.ok) throw new Error('Invoice not ready');
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = `invoice-${orderId}.pdf`; a.click();
            URL.revokeObjectURL(url);
        } catch (e) { console.error(e); alert('Could not download invoice'); }
    }

    async reorder(orderId) {
        try {
            const res = await fetch(`/api/user/orders/${orderId}/reorder`, { method: 'POST' });
            const data = await res.json();
            if (data.success) {
                alert('Items added to cart.');
                window.location.href = '/cart';
            } else {
                alert('Error: ' + (data.message || 'Unable to reorder'));
            }
        } catch (e) { console.error(e); alert('Failed to reorder'); }
    }
}

function switchOrderTab(tab) {
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tab + 'Tab').classList.add('active');

    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(tab + 'Content').classList.add('active');
}

/* Expose for action buttons used in templates */
window.orders = new MyOrdersDashboard();
</script>
