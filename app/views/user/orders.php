<!-- My Orders Page -->

<div class="orders-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-receipt"></i> My Orders</h1>
            <p class="subtitle">Track and manage all your product orders</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-outline" onclick="window.location.href='/shop'">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="orders-filters">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="search-orders" placeholder="Search orders by number or product...">
        </div>
        <div class="filter-tabs">
            <button class="filter-tab active" data-status="all">All Orders</button>
            <button class="filter-tab" data-status="pending">Pending</button>
            <button class="filter-tab" data-status="processing">Processing</button>
            <button class="filter-tab" data-status="shipped">Shipped</button>
            <button class="filter-tab" data-status="delivered">Delivered</button>
            <button class="filter-tab" data-status="cancelled">Cancelled</button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="orders-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-orders">0</h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 id="pending-orders">0</h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="delivered-orders">0</h3>
                <p>Delivered</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-spent">LKR 0</h3>
                <p>Total Spent</p>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="orders-list" id="orders-list">
        <!-- Loading State -->
        <div class="loading-state" id="loading-state">
            <div class="spinner"></div>
            <p>Loading your orders...</p>
        </div>

        <!-- Empty State -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <i class="fas fa-box-open"></i>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <button class="btn btn-primary" onclick="window.location.href='/shop'">
                <i class="fas fa-shopping-bag"></i> Browse Products
            </button>
        </div>

        <!-- Orders will be loaded here dynamically -->
    </div>
</div>

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #dbeafe;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-gray: #f9fafb;
}

.orders-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.header-content h1 i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-outline {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-light);
}

.orders-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--background-gray);
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.search-box i {
    color: var(--text-secondary);
}

.search-box input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 0.875rem;
    outline: none;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-color);
    background: white;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-tab:hover {
    background: var(--background-gray);
}

.filter-tab.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.orders-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 3.5rem;
    height: 3.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-info p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.orders-list {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.order-card {
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
}

.order-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.order-number {
    font-weight: 700;
    color: var(--text-primary);
}

.order-date {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.order-status {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pending { background: #fef3c7; color: #f59e0b; }
.status-processing { background: #dbeafe; color: #2563eb; }
.status-shipped { background: #e0e7ff; color: #6366f1; }
.status-delivered { background: #d1fae5; color: #10b981; }
.status-cancelled { background: #fee2e2; color: #ef4444; }

.order-items {
    margin-bottom: 1rem;
}

.order-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.order-item-name {
    flex: 1;
    font-weight: 500;
}

.order-item-qty {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.order-item-price {
    font-weight: 600;
    color: var(--text-primary);
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.order-total {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.order-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.loading-state, .empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid var(--border-color);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .orders-stats {
        grid-template-columns: 1fr;
    }

    .order-header, .order-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
let allOrders = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
    loadOrders();

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.status;
            filterOrders();
        });
    });

    // Search
    document.getElementById('search-orders').addEventListener('input', function(e) {
        filterOrders(e.target.value);
    });
});

async function loadOrders() {
    try {
        const response = await fetch('/api/user/orders');
        const data = await response.json();

        if (data.success) {
            allOrders = data.orders || [];
            updateStats(allOrders);
            displayOrders(allOrders);
        } else {
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        showEmptyState();
    }
}

function updateStats(orders) {
    const stats = {
        total: orders.length,
        pending: orders.filter(o => o.status === 'pending').length,
        delivered: orders.filter(o => o.status === 'delivered').length,
        totalSpent: orders.reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0)
    };

    document.getElementById('total-orders').textContent = stats.total;
    document.getElementById('pending-orders').textContent = stats.pending;
    document.getElementById('delivered-orders').textContent = stats.delivered;
    document.getElementById('total-spent').textContent = 'LKR ' + stats.totalSpent.toFixed(2);
}

function displayOrders(orders) {
    const container = document.getElementById('orders-list');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    loadingState.style.display = 'none';

    if (orders.length === 0) {
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';

    const ordersHTML = orders.map(order => createOrderCard(order)).join('');
    const existingCards = container.querySelectorAll('.order-card');
    existingCards.forEach(card => card.remove());

    container.insertAdjacentHTML('beforeend', ordersHTML);
}

function createOrderCard(order) {
    const statusClass = `status-${order.status}`;
    const items = order.items || [];

    return `
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-number">#${order.order_number}</div>
                    <div class="order-date">${formatDate(order.created_at)}</div>
                </div>
                <span class="order-status ${statusClass}">${capitalizeFirst(order.status)}</span>
            </div>
            <div class="order-items">
                ${items.map(item => `
                    <div class="order-item">
                        <div class="order-item-name">${item.item_name}</div>
                        <div class="order-item-qty">Qty: ${item.quantity}</div>
                        <div class="order-item-price">LKR ${parseFloat(item.total_price).toFixed(2)}</div>
                    </div>
                `).join('')}
            </div>
            <div class="order-footer">
                <div class="order-total">Total: LKR ${parseFloat(order.total_amount).toFixed(2)}</div>
                <div class="order-actions">
                    <button class="btn btn-outline btn-sm" onclick="viewOrderDetails('${order.id}')">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                    ${order.status === 'delivered' ? `
                        <button class="btn btn-primary btn-sm" onclick="reorder('${order.id}')">
                            <i class="fas fa-redo"></i> Reorder
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function filterOrders(searchTerm = '') {
    let filtered = allOrders;

    if (currentFilter !== 'all') {
        filtered = filtered.filter(order => order.status === currentFilter);
    }

    if (searchTerm) {
        searchTerm = searchTerm.toLowerCase();
        filtered = filtered.filter(order =>
            order.order_number.toLowerCase().includes(searchTerm) ||
            (order.items || []).some(item => item.item_name.toLowerCase().includes(searchTerm))
        );
    }

    displayOrders(filtered);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function showEmptyState() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('empty-state').style.display = 'block';
}

function viewOrderDetails(orderId) {
    // TODO: Implement order details modal or page
    alert('Order details for order #' + orderId);
}

function reorder(orderId) {
    // TODO: Implement reorder functionality
    alert('Reorder functionality coming soon!');
}
</script>
