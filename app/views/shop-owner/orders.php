<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load required models
require_once __DIR__ . '/../../../app/models/Order.php';

use App\Models\Order;

$orderModel = new Order();
$shopOwnerId = $_SESSION['user_id'] ?? 0;

// Get filters from URL
$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'payment_method' => $_GET['payment_method'] ?? '',
    'payment_status' => $_GET['payment_status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

// Remove empty filters
$filters = array_filter($filters);

// Get orders and stats with error handling
$orders = [];
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'processing_orders' => 0,
    'shipped_orders' => 0,
    'delivered_orders' => 0,
    'cancelled_orders' => 0,
    'pending_payments' => 0,
    'paid_orders' => 0,
    'total_revenue' => 0
];

try {
    $orders = $orderModel->getOrdersByShopOwner($shopOwnerId, $filters);
    $stats = $orderModel->getShopOwnerOrderStats($shopOwnerId);
} catch (\Exception $e) {
    error_log("Error loading orders: " . $e->getMessage());
    $errorMessage = $e->getMessage();
}

$title = 'Order Management - Shop Owner Dashboard';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css', '/public/css/pages/shop-owner-orders.css'];
$additionalJS = ['/public/js/shop-owner-sidebar.js', '/public/js/pages/shop-owner-orders.js'];
?>
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/shop-owner-orders.css">

<div class="shop-owner-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                
                <div class="page-title-section">
                    <h1 class="page-title">Order Management</h1>
                    <p class="page-subtitle">Manage and track customer orders</p>
                </div>
            </div>
            <div class="header-right">
                <button class="btn-refresh" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Overview -->
            <div class="stats-overview">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-value"><?= isset($stats['total_orders']) ? $stats['total_orders'] : 0 ?></p>
                        <span class="stat-label">All time orders</span>
                    </div>
                </div>

                <div class="stat-card yellow">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Pending</h3>
                        <p class="stat-value"><?= isset($stats['pending_orders']) ? $stats['pending_orders'] : 0 ?></p>
                        <span class="stat-label">Awaiting processing</span>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Processing</h3>
                        <p class="stat-value"><?= isset($stats['processing_orders']) ? $stats['processing_orders'] : 0 ?></p>
                        <span class="stat-label">Being prepared</span>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Delivered</h3>
                        <p class="stat-value"><?= isset($stats['delivered_orders']) ? $stats['delivered_orders'] : 0 ?></p>
                        <span class="stat-label">Successfully completed</span>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Revenue</h3>
                        <p class="stat-value">Rs. <?= isset($stats['total_revenue']) ? number_format($stats['total_revenue'], 2) : '0.00' ?></p>
                        <span class="stat-label">From your products</span>
                    </div>
                </div>
            </div>

            <?php if (isset($errorMessage)): ?>
            <!-- Error Alert -->
            <div class="alert alert-danger" style="margin: 1.5rem 0; padding: 1rem 1.5rem; background: #fee2e2; border-left: 4px solid #ef4444; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.5rem;"></i>
                    <div>
                        <strong style="color: #991b1b;">Database Error</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #991b1b;"><?= htmlspecialchars($errorMessage) ?></p>
                        <p style="margin: 0.5rem 0 0 0; color: #991b1b; font-size: 0.875rem;">Please ensure the database migration has been run to add the shop_owner_id column to products table.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($orders) && empty($filters) && !isset($errorMessage)): ?>
            <!-- Info Alert for Empty State -->
            <div class="alert alert-info" style="margin: 1.5rem 0; padding: 1rem 1.5rem; background: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 1.5rem;"></i>
                    <div>
                        <strong style="color: #1e40af;">No Orders Yet</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #1e40af;">Orders containing your products will appear here once customers make purchases.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filters and Search -->
            <div class="filters-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orderSearch" placeholder="Search by Order ID, customer name, or email..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>

                <div class="filter-controls">
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>

                    <select class="filter-select" id="paymentMethodFilter">
                        <option value="">All Payment Methods</option>
                        <option value="card" <?= ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Card</option>
                        <option value="cash" <?= ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash on Delivery</option>
                        <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    </select>

                    <select class="filter-select" id="paymentStatusFilter">
                        <option value="">All Payment Status</option>
                        <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= ($filters['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>

                    <input type="date" class="filter-date" id="dateFrom" placeholder="From Date" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                    <input type="date" class="filter-date" id="dateTo" placeholder="To Date" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">

                    <button class="btn-filter-clear" onclick="clearFilters()">
                        <i class="fas fa-redo"></i>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="orders-table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): 
                                $orderDate = new DateTime($order['created_at']);
                                $now = new DateTime();
                                $interval = $now->diff($orderDate);
                                
                                // Format time ago
                                if ($interval->days == 0) {
                                    if ($interval->h == 0) {
                                        $timeAgo = $interval->i . ' minutes ago';
                                    } else {
                                        $timeAgo = $interval->h . ' hours ago';
                                    }
                                } else if ($interval->days == 1) {
                                    $timeAgo = 'Yesterday';
                                } else {
                                    $timeAgo = $interval->days . ' days ago';
                                }
                                
                                // Status badge classes
                                $statusClass = [
                                    'pending' => 'badge-pending',
                                    'processing' => 'badge-processing',
                                    'shipped' => 'badge-shipped',
                                    'delivered' => 'badge-delivered',
                                    'cancelled' => 'badge-cancelled'
                                ][$order['status']] ?? 'badge-default';
                                
                                $paymentStatusClass = [
                                    'pending' => 'badge-payment-pending',
                                    'paid' => 'badge-payment-paid',
                                    'failed' => 'badge-payment-failed'
                                ][$order['payment_status']] ?? 'badge-default';
                                
                                $paymentMethodLabel = [
                                    'card' => 'Card',
                                    'cash' => 'COD',
                                    'bank_transfer' => 'Bank Transfer'
                                ][$order['payment_method']] ?? ucfirst($order['payment_method']);
                            ?>
                            <tr data-order-id="<?= $order['id'] ?>">
                                <td>
                                    <span class="order-number"><?= htmlspecialchars($order['order_number']) ?></span>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <strong><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></strong>
                                        <small><?= htmlspecialchars($order['customer_email'] ?? '') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <span><?= $orderDate->format('M d, Y') ?></span>
                                        <small><?= $orderDate->format('h:i A') ?></small>
                                        <span class="time-ago"><?= $timeAgo ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="item-count"><?= (int)$order['total_items'] ?></span>
                                </td>
                                <td>
                                    <strong class="amount">Rs. <?= number_format($order['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <span class="payment-method"><?= $paymentMethodLabel ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $paymentStatusClass ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewOrder(<?= $order['id'] ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editOrderStatus(<?= $order['id'] ?>)" title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($order['payment_method'] === 'cash'): ?>
                                        <button class="btn-action <?= $order['cod_payment_proof'] ? 'btn-success' : 'btn-warning' ?>"
                                            onclick="openCODUpload(<?= $order['id'] ?>, '<?= htmlspecialchars($order['cod_payment_proof'] ?? '') ?>')"
                                            title="<?= $order['cod_payment_proof'] ? 'Update Payment Proof' : 'Upload Payment Proof' ?>">
                                            <i class="fas fa-<?= $order['cod_payment_proof'] ? 'check-circle' : 'upload' ?>"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (in_array($order['status'], ['pending', 'cancelled'])): ?>
                                        <button class="btn-action btn-delete" onclick="deleteOrder(<?= $order['id'] ?>)" title="Delete Order">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center" style="padding: 2rem;">
                                    <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                                    <p style="color: #64748b; font-size: 1.1rem;">No orders found</p>
                                    <?php if (!empty($filters)): ?>
                                    <button class="btn-secondary" onclick="clearFilters()" style="margin-top: 1rem;">
                                        Clear Filters
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- View Order Details Modal -->
<div id="viewOrderModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Order Details</h2>
            <button class="modal-close" onclick="closeModal('viewOrderModal')">&times;</button>
        </div>
        <div class="modal-body" id="orderDetailsContent">
            <!-- Content will be loaded dynamically -->
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading order details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Status Modal -->
<div id="editOrderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Order Status</h2>
            <button class="modal-close" onclick="closeModal('editOrderModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editOrderForm">
                <input type="hidden" id="editOrderId" name="order_id">
                
                <div class="form-group">
                    <label for="orderStatus">Order Status</label>
                    <select id="orderStatus" name="status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group" id="paymentStatusGroup">
                    <label for="paymentStatus">Payment Status</label>
                    <select id="paymentStatus" name="payment_status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                    <small class="form-hint">Will update both order and payment status</small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('editOrderModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteOrderModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button class="modal-close" onclick="closeModal('deleteOrderModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to delete this order?</p>
                <p class="order-info" id="deleteOrderInfo"></p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <input type="hidden" id="deleteOrderId">
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('deleteOrderModal')">Cancel</button>
                <button type="button" class="btn-danger" onclick="confirmDeleteOrder()">Delete Order</button>
            </div>
        </div>
    </div>
</div>

<!-- COD Payment Proof Upload Modal -->
<div id="codUploadModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-upload" style="margin-right:8px;color:#f59e0b;"></i>Upload COD Payment Proof</h2>
            <button class="modal-close" onclick="closeModal('codUploadModal')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="codUploadOrderId">

            <div id="existingProof" style="display:none;margin-bottom:1rem;padding:0.75rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
                <p style="margin:0 0 0.5rem;font-size:0.85rem;color:#166534;font-weight:600;"><i class="fas fa-check-circle"></i> Proof already uploaded</p>
                <a id="existingProofLink" href="#" target="_blank" style="font-size:0.85rem;color:#059669;">View current proof</a>
            </div>

            <div class="form-group">
                <label style="display:block;margin-bottom:6px;font-size:0.875rem;font-weight:500;">Payment Receipt / Proof (JPG, PNG, PDF — max 5MB)</label>
                <input type="file" id="codProofFile" accept="image/jpeg,image/png,image/gif,application/pdf"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
            </div>

            <div class="form-actions" style="margin-top:1.5rem;">
                <button type="button" class="btn-secondary" onclick="closeModal('codUploadModal')">Cancel</button>
                <button type="button" class="btn-primary" onclick="submitCODProof()" id="codUploadBtn">
                    <i class="fas fa-upload"></i> Upload Proof
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script src="<?= $_base ?>/public/js/pages/shop-owner-orders.js"></script>
<script>
const BASE_URL = '<?= $_base ?>';

function openCODUpload(orderId, existingProof) {
    document.getElementById('codUploadOrderId').value = orderId;
    const existingDiv = document.getElementById('existingProof');
    if (existingProof) {
        existingDiv.style.display = 'block';
        document.getElementById('existingProofLink').href = BASE_URL + existingProof;
    } else {
        existingDiv.style.display = 'none';
    }
    document.getElementById('codProofFile').value = '';
    openModal('codUploadModal');
}

async function submitCODProof() {
    const orderId = document.getElementById('codUploadOrderId').value;
    const file = document.getElementById('codProofFile').files[0];
    if (!file) { showToast('Please select a file', 'error'); return; }

    const btn = document.getElementById('codUploadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

    const formData = new FormData();
    formData.append('proof', file);

    try {
        const res = await fetch(`${BASE_URL}/api/shop-owner/orders/${orderId}/cod-proof`, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('codUploadModal');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Upload failed', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-upload"></i> Upload Proof';
    }
}
</script>
<style>
.btn-warning { background: #f59e0b; color: #fff; }
.btn-warning:hover { background: #d97706; }
.btn-success { background: #059669; color: #fff; }
.btn-success:hover { background: #047857; }
</style>
