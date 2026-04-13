<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'My Orders - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/my-orders.css">

<div class="orders-page">
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header-section">
            <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
            <p>Track and manage your product orders</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="number" id="totalOrders">0</div>
                <div class="label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="number" id="pendingOrders">0</div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number" id="deliveredOrders">0</div>
                <div class="label">Delivered</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="number" id="totalSpent">Rs. 0</div>
                <div class="label">Total Spent</div>
            </div>
        </div>

        <!-- Filters & Search Section -->
        <div class="filters-container">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filter Orders</h3>
                <button class="btn-clear-filters" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="searchOrders"><i class="fas fa-search"></i> Search</label>
                    <input type="text" id="searchOrders" placeholder="Order number or product name...">
                </div>
                <div class="filter-group">
                    <label for="statusFilter"><i class="fas fa-tag"></i> Order Status</label>
                    <select id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="paymentStatusFilter"><i class="fas fa-credit-card"></i> Payment Status</label>
                    <select id="paymentStatusFilter">
                        <option value="">All Payment Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="refund_pending">Refund Pending</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sortOrders"><i class="fas fa-sort"></i> Sort By</label>
                    <select id="sortOrders">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Total</option>
                        <option value="lowest">Lowest Total</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Orders Container -->
        <div class="orders-container">
            <!-- Loading State -->
            <div id="ordersLoading" class="loading-state">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading your orders...</p>
            </div>

            <!-- Orders List -->
            <div id="ordersList" class="orders-list" style="display: none;"></div>

            <!-- Empty State -->
            <div id="ordersEmpty" class="empty-state" style="display: none;">
                <i class="fas fa-shopping-bag"></i>
                <h3>No orders yet</h3>
                <p>You haven't placed any orders yet</p>
                <a href="<?= $_base ?>/shop" class="btn btn-primary">
                    <i class="fas fa-store"></i> Start Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<!-- View Order Details Modal -->
<div id="viewOrderModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-receipt"></i> Order Details</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="orderDetailsContent">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading order details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Confirmation Modal -->
<div id="cancelOrderModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Cancel Order</h2>
            <button class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="cancel-warning">
                <p>Are you sure you want to cancel this order?</p>
                <p class="order-info" id="cancelOrderInfo"></p>
            </div>
            <div class="form-group">
                <label for="cancelReason">Reason for cancellation (optional)</label>
                <textarea id="cancelReason" rows="3" placeholder="Tell us why you're cancelling..."></textarea>
            </div>
            <input type="hidden" id="cancelOrderId">
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">
                    <i class="fas fa-check"></i> Keep Order
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmCancelOrder()">
                    <i class="fas fa-times-circle"></i> Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script src="<?= $_base ?>/public/js/pages/my-orders.js"></script>
