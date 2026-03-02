<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../app/models/Order.php';
use App\Models\Order;

$orderModel  = new Order();
$shopOwnerId = $_SESSION['user_id'] ?? 0;

$filters = array_filter([
    'search'         => $_GET['search']         ?? '',
    'status'         => $_GET['status']         ?? '',
    'payment_method' => $_GET['payment_method'] ?? '',
    'payment_status' => $_GET['payment_status'] ?? '',
    'date_from'      => $_GET['date_from']      ?? '',
    'date_to'        => $_GET['date_to']        ?? '',
]);

$orders = [];
$stats  = [
    'total_orders'      => 0, 'pending_orders'   => 0,
    'processing_orders' => 0, 'delivered_orders' => 0,
    'total_revenue'     => 0,
];

try {
    $orders = $orderModel->getOrdersByShopOwner($shopOwnerId, $filters);
    $stats  = $orderModel->getShopOwnerOrderStats($shopOwnerId);
} catch (\Exception $e) {
    error_log('Orders page error: ' . $e->getMessage());
    $errorMessage = $e->getMessage();
}

$title         = 'Order Management – Shop Owner';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css', '/public/css/pages/shop-owner-orders.css'];
$additionalJS  = ['/public/js/shop-owner-sidebar.js', '/public/js/pages/shop-owner-orders.js'];

/* helpers */
$statusBadge = [
    'pending'    => 'so-badge--pending',
    'processing' => 'so-badge--processing',
    'shipped'    => 'so-badge--shipped',
    'delivered'  => 'so-badge--delivered',
    'cancelled'  => 'so-badge--cancelled',
];
$payBadge = [
    'pending' => 'so-badge--pay-pending',
    'paid'    => 'so-badge--pay-paid',
    'failed'  => 'so-badge--pay-failed',
];
$pmLabel = [
    'card'          => 'Card',
    'cash'          => 'COD',
    'bank_transfer' => 'Bank Transfer',
];
?>
<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/shop-owner-orders.css">

<div class="shop-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!-- ── Header ── -->
        <header class="so-header">
            <div class="so-header-left">
                <div class="so-page-icon"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <h1 class="so-page-title">Order Management</h1>
                    <p class="so-page-sub">View and manage customer orders for your products</p>
                </div>
            </div>
            <div class="so-header-right">
                <button class="so-btn-refresh" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </header>

        <div class="so-content">

            <!-- ── Stats ── -->
            <div class="so-stats">
                <div class="so-stat so-stat--blue">
                    <div class="so-stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="so-stat-body">
                        <div class="so-stat-label">Total Orders</div>
                        <p class="so-stat-val"><?= (int)($stats['total_orders'] ?? 0) ?></p>
                        <span class="so-stat-hint">All time</span>
                    </div>
                </div>
                <div class="so-stat so-stat--amber">
                    <div class="so-stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="so-stat-body">
                        <div class="so-stat-label">Pending</div>
                        <p class="so-stat-val"><?= (int)($stats['pending_orders'] ?? 0) ?></p>
                        <span class="so-stat-hint">Awaiting action</span>
                    </div>
                </div>
                <div class="so-stat so-stat--orange">
                    <div class="so-stat-icon"><i class="fas fa-cog"></i></div>
                    <div class="so-stat-body">
                        <div class="so-stat-label">Processing</div>
                        <p class="so-stat-val"><?= (int)($stats['processing_orders'] ?? 0) ?></p>
                        <span class="so-stat-hint">Being prepared</span>
                    </div>
                </div>
                <div class="so-stat so-stat--green">
                    <div class="so-stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="so-stat-body">
                        <div class="so-stat-label">Delivered</div>
                        <p class="so-stat-val"><?= (int)($stats['delivered_orders'] ?? 0) ?></p>
                        <span class="so-stat-hint">Completed</span>
                    </div>
                </div>
                <div class="so-stat so-stat--indigo">
                    <div class="so-stat-icon"><i class="fas fa-coins"></i></div>
                    <div class="so-stat-body">
                        <div class="so-stat-label">Revenue</div>
                        <p class="so-stat-val" style="font-size:1.2rem;">Rs.&nbsp;<?= number_format((float)($stats['total_revenue'] ?? 0), 0) ?></p>
                        <span class="so-stat-hint">From your products</span>
                    </div>
                </div>
            </div>

            <?php if (isset($errorMessage)): ?>
            <div class="so-alert so-alert--error">
                <i class="fas fa-exclamation-triangle so-alert-icon"></i>
                <div>
                    <strong>Database Error</strong>
                    <p><?= htmlspecialchars($errorMessage) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($orders) && empty($filters) && !isset($errorMessage)): ?>
            <div class="so-alert so-alert--info">
                <i class="fas fa-info-circle so-alert-icon"></i>
                <div>
                    <strong>No Orders Yet</strong>
                    <p>Orders that include your products will appear here once customers place purchases.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Toolbar / Filters ── -->
            <div class="so-toolbar">
                <div class="so-toolbar-top">
                    <div class="so-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="orderSearch"
                               placeholder="Search by Order ID, customer name or email…"
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>

                    <select class="so-filter-sel" id="statusFilter">
                        <option value="">All Status</option>
                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="so-filter-sel" id="paymentMethodFilter">
                        <option value="">All Methods</option>
                        <option value="card"          <?= ($filters['payment_method'] ?? '') === 'card'          ? 'selected' : '' ?>>Card</option>
                        <option value="cash"          <?= ($filters['payment_method'] ?? '') === 'cash'          ? 'selected' : '' ?>>Cash on Delivery</option>
                        <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    </select>

                    <select class="so-filter-sel" id="paymentStatusFilter">
                        <option value="">All Payments</option>
                        <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid"    <?= ($filters['payment_status'] ?? '') === 'paid'    ? 'selected' : '' ?>>Paid</option>
                        <option value="failed"  <?= ($filters['payment_status'] ?? '') === 'failed'  ? 'selected' : '' ?>>Failed</option>
                    </select>

                    <input type="date" class="so-filter-date" id="dateFrom"
                           placeholder="From" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                    <input type="date" class="so-filter-date" id="dateTo"
                           placeholder="To" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">

                    <button class="so-btn-clear" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>

            <!-- ── Orders Table ── -->
            <div class="so-table-wrap">
                <div class="so-table-header">
                    <span class="so-table-title">Orders</span>
                    <span class="so-table-count"><?= count($orders) ?> result<?= count($orders) !== 1 ? 's' : '' ?></span>
                </div>
                <table class="so-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Pay Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order):
                            $dt  = new DateTime($order['created_at']);
                            $now = new DateTime();
                            $iv  = $now->diff($dt);
                            if     ($iv->days === 0 && $iv->h === 0) $ago = $iv->i . 'm ago';
                            elseif ($iv->days === 0)                  $ago = $iv->h . 'h ago';
                            elseif ($iv->days === 1)                  $ago = 'Yesterday';
                            else                                      $ago = $iv->days . 'd ago';

                            $sBadge = $statusBadge[$order['status']] ?? 'so-badge--default';
                            $pBadge = $payBadge[$order['payment_status']] ?? 'so-badge--default';
                            $pm     = $pmLabel[$order['payment_method']] ?? ucfirst($order['payment_method']);
                        ?>
                        <tr data-order-id="<?= $order['id'] ?>">
                            <td>
                                <span class="so-order-num" onclick="viewOrder(<?= $order['id'] ?>)">
                                    #<?= htmlspecialchars($order['order_number']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="so-customer">
                                    <strong><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></strong>
                                    <small><?= htmlspecialchars($order['customer_email'] ?? '') ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="so-date">
                                    <span><?= $dt->format('M d, Y') ?></span>
                                    <small><?= $dt->format('h:i A') ?></small>
                                    <em><?= $ago ?></em>
                                </div>
                            </td>
                            <td><span class="so-item-chip"><?= (int)$order['total_items'] ?></span></td>
                            <td><span class="so-amount">Rs. <?= number_format((float)$order['total_amount'], 2) ?></span></td>
                            <td><span class="so-pm"><?= $pm ?></span></td>
                            <td><span class="so-badge <?= $pBadge ?>"><?= ucfirst($order['payment_status']) ?></span></td>
                            <td><span class="so-badge <?= $sBadge ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td>
                                <div class="so-actions">
                                    <button class="so-btn-act so-btn-act--view" onclick="viewOrder(<?= $order['id'] ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="so-btn-act so-btn-act--edit" onclick="editOrderStatus(<?= $order['id'] ?>)" title="Update Status">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <?php if (in_array($order['status'], ['pending', 'cancelled'])): ?>
                                    <button class="so-btn-act so-btn-act--del" onclick="deleteOrder(<?= $order['id'] ?>)" title="Delete Order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="so-empty">
                                    <i class="fas fa-inbox"></i>
                                    <p>No orders found<?= !empty($filters) ? ' matching your filters' : '' ?></p>
                                    <?php if (!empty($filters)): ?>
                                    <button class="so-empty-btn" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /so-content -->
    </main>
</div>

<!-- ── View Order Modal ── -->
<div id="viewOrderModal" class="so-modal">
    <div class="so-modal-box so-modal-box--lg">
        <div class="so-modal-head">
            <h2><i class="fas fa-receipt"></i> Order Details</h2>
            <button class="so-modal-close" onclick="closeModal('viewOrderModal')">&times;</button>
        </div>
        <div class="so-modal-body" id="orderDetailsContent">
            <div class="so-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading order details…</p>
            </div>
        </div>
    </div>
</div>

<!-- ── Edit Status Modal ── -->
<div id="editOrderModal" class="so-modal">
    <div class="so-modal-box">
        <div class="so-modal-head">
            <h2><i class="fas fa-pen"></i> Update Order Status</h2>
            <button class="so-modal-close" onclick="closeModal('editOrderModal')">&times;</button>
        </div>
        <div class="so-modal-body">
            <form id="editOrderForm">
                <input type="hidden" id="editOrderId">
                <div class="so-form-group">
                    <label for="orderStatus">Order Status</label>
                    <select id="orderStatus" class="so-form-sel" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="so-form-group" id="paymentStatusGroup">
                    <label for="paymentStatus">Payment Status</label>
                    <select id="paymentStatus" class="so-form-sel" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                    <small class="so-form-hint" id="payHint">Updates payment status alongside order status</small>
                </div>
                <div class="so-form-actions">
                    <button type="button" class="so-btn so-btn--ghost" onclick="closeModal('editOrderModal')">Cancel</button>
                    <button type="submit" class="so-btn so-btn--primary"><i class="fas fa-check"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Delete Modal ── -->
<div id="deleteOrderModal" class="so-modal">
    <div class="so-modal-box so-modal-box--sm">
        <div class="so-modal-head">
            <h2><i class="fas fa-trash"></i> Delete Order</h2>
            <button class="so-modal-close" onclick="closeModal('deleteOrderModal')">&times;</button>
        </div>
        <div class="so-modal-body">
            <div class="so-del-warn">
                <i class="fas fa-exclamation-circle"></i>
                <p>Are you sure you want to permanently delete this order?</p>
                <p class="so-del-order" id="deleteOrderInfo"></p>
                <p class="so-del-note">This action cannot be undone.</p>
            </div>
            <input type="hidden" id="deleteOrderId">
            <div class="so-form-actions">
                <button class="so-btn so-btn--ghost" onclick="closeModal('deleteOrderModal')">Cancel</button>
                <button class="so-btn so-btn--danger" onclick="confirmDeleteOrder()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="soToast" class="so-toast"></div>

<script src="/public/js/pages/shop-owner-orders.js"></script>
