<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../app/models/Product.php';
use App\Models\Product;

$productModel = new Product();
$shopOwnerId  = $_SESSION['user_id'] ?? 0;

$inventory = $productModel->getInventoryWithStatus($shopOwnerId);
$stats     = $productModel->getInventoryStats($shopOwnerId);

$title        = 'Inventory Management – Shop Owner';
$additionalCSS = [
    '/public/css/pages/shop-owner-dashboard.css',
    '/public/css/pages/shop-owner-inventory.css',
];
$additionalJS = [
    '/public/js/shop-owner-sidebar.js',
    '/public/js/pages/shop-owner-inventory.js',
];

/* counts per tab */
$cntIn  = (int)($stats['in_stock']    ?? 0);
$cntLow = (int)($stats['low_stock']   ?? 0);
$cntOut = (int)($stats['out_of_stock'] ?? 0);
$cntAll = (int)($stats['total_products'] ?? 0);
?>
<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/shop-owner-inventory.css">

<div class="shop-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!-- ── Header ── -->
        <header class="si-header">
            <div class="si-header-left">
                <div class="si-page-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <h1 class="si-page-title">Inventory</h1>
                    <p class="si-page-sub">Track stock levels and manage reorder thresholds</p>
                </div>
            </div>
        </header>

        <div class="si-content">

            <!-- ── Alerts ── -->
            <?php if (isset($_SESSION['success'])): ?>
            <div class="si-alert si-alert--success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="si-alert si-alert--error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <!-- ── Stats ── -->
            <div class="si-stats">
                <div class="si-stat si-stat--total">
                    <div class="si-stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="si-stat-body">
                        <div class="si-stat-label">Total Products</div>
                        <p class="si-stat-val"><?= $cntAll ?></p>
                    </div>
                </div>
                <div class="si-stat si-stat--in">
                    <div class="si-stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="si-stat-body">
                        <div class="si-stat-label">In Stock</div>
                        <p class="si-stat-val"><?= $cntIn ?></p>
                    </div>
                </div>
                <div class="si-stat si-stat--low">
                    <div class="si-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="si-stat-body">
                        <div class="si-stat-label">Low Stock</div>
                        <p class="si-stat-val"><?= $cntLow ?></p>
                    </div>
                </div>
                <div class="si-stat si-stat--out">
                    <div class="si-stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="si-stat-body">
                        <div class="si-stat-label">Out of Stock</div>
                        <p class="si-stat-val"><?= $cntOut ?></p>
                    </div>
                </div>
                <div class="si-stat si-stat--units">
                    <div class="si-stat-icon"><i class="fas fa-cubes"></i></div>
                    <div class="si-stat-body">
                        <div class="si-stat-label">Total Units</div>
                        <p class="si-stat-val"><?= number_format((int)($stats['total_units'] ?? 0)) ?></p>
                    </div>
                </div>
            </div>

            <!-- ── Toolbar ── -->
            <div class="si-toolbar">
                <div class="si-tabs">
                    <button class="si-tab active" data-filter="all">
                        All <span class="si-tab-badge"><?= $cntAll ?></span>
                    </button>
                    <button class="si-tab" data-filter="in-stock">
                        In Stock <span class="si-tab-badge"><?= $cntIn ?></span>
                    </button>
                    <button class="si-tab" data-filter="low-stock">
                        Low Stock <span class="si-tab-badge"><?= $cntLow ?></span>
                    </button>
                    <button class="si-tab" data-filter="out-of-stock">
                        Out of Stock <span class="si-tab-badge"><?= $cntOut ?></span>
                    </button>
                </div>
                <div class="si-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="inventorySearch" placeholder="Search by product name or SKU…">
                </div>
            </div>

            <!-- ── Table ── -->
            <div class="si-table-wrap">
                <div class="si-table-head">
                    <span class="si-table-title">Products</span>
                    <span class="si-table-count" id="siCount"><?= $cntAll ?> product<?= $cntAll !== 1 ? 's' : '' ?></span>
                </div>
                <table class="si-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="si-th-center">Stock</th>
                            <th class="si-th-center">Reorder Level</th>
                            <th class="si-th-center">Status</th>
                            <th>Last Updated</th>
                            <th class="si-th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryBody">
                    <?php if (!empty($inventory)): ?>
                        <?php foreach ($inventory as $item):
                            $ss  = $item['stock_status']; // 'in-stock' | 'low-stock' | 'out-of-stock'
                            $qty = (int)$item['stock_quantity'];
                            $min = (int)$item['min_stock_level'];
                            $qtyClass   = match($ss) { 'out-of-stock'=>'si-qty--out', 'low-stock'=>'si-qty--low', default=>'si-qty--ok' };
                            $badgeClass = match($ss) { 'out-of-stock'=>'si-badge--out', 'low-stock'=>'si-badge--low', default=>'si-badge--ok' };
                            $badgeText  = match($ss) { 'out-of-stock'=>'Out of Stock', 'low-stock'=>'Low Stock', default=>'In Stock' };
                            $stockClass = $qty === 0 ? 'has-out' : ($ss === 'low-stock' ? 'has-low' : '');
                        ?>
                        <tr data-status="<?= $ss ?>">
                            <td>
                                <div class="si-product-name"><?= htmlspecialchars($item['name']) ?></div>
                                <?php if (!empty($item['category_name'])): ?>
                                <div class="si-product-cat"><?= htmlspecialchars($item['category_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['sku'])): ?>
                                <span class="si-sku"><?= htmlspecialchars($item['sku']) ?></span>
                                <?php else: ?>
                                <span style="color:var(--si-slate5);font-size:.78rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="si-td-center">
                                <span class="si-qty <?= $qtyClass ?>"><?= $qty ?></span>
                            </td>
                            <td class="si-td-center">
                                <span class="si-reorder">
                                    <span id="reorder-val-<?= $item['id'] ?>"><?= $min ?></span>
                                    <button class="si-reorder-edit"
                                            onclick="openReorderModal(<?= $item['id'] ?>, <?= htmlspecialchars(json_encode($item['name'])) ?>, <?= $min ?>)"
                                            title="Edit reorder level">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </span>
                            </td>
                            <td class="si-td-center">
                                <span class="si-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            </td>
                            <td>
                                <span class="si-date"><?= date('M d, Y', strtotime($item['updated_at'])) ?></span>
                            </td>
                            <td class="si-td-center">
                                <div class="si-actions">
                                    <button class="si-btn-act si-btn-add"
                                            onclick="openAddModal(<?= $item['id'] ?>, <?= htmlspecialchars(json_encode($item['name'])) ?>, <?= $qty ?>)">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                    <button class="si-btn-act si-btn-rem"
                                            onclick="openRemoveModal(<?= $item['id'] ?>, <?= htmlspecialchars(json_encode($item['name'])) ?>, <?= $qty ?>)">
                                        <i class="fas fa-minus"></i> Remove
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="si-empty">
                                    <i class="fas fa-box-open"></i>
                                    <p>No products in inventory yet.</p>
                                    <p>Add products via <a href="/shop-owner/products">Product Management</a> first.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /si-content -->
    </main>
</div>

<!-- ── Add Stock Modal ── -->
<div id="addStockModal" class="si-modal">
    <div class="si-modal-box">
        <div class="si-modal-head">
            <h2><i class="fas fa-plus-circle"></i> Add Stock</h2>
            <button class="si-modal-close" onclick="closeModal('addStockModal')">&times;</button>
        </div>
        <div class="si-modal-body">
            <div class="si-modal-product">
                <div>
                    <div class="si-modal-pname" id="add-pname">—</div>
                    <div class="si-modal-pstock" id="add-pstock">Current stock: <strong>0</strong> units</div>
                </div>
                <i class="fas fa-boxes" style="color:var(--si-slate5);font-size:1.5rem;"></i>
            </div>
            <form method="POST" action="/shop-owner/inventory/add-stock" id="addStockForm">
                <input type="hidden" name="product_id" id="add-product-id">
                <div class="si-form-group">
                    <label for="add-quantity">Quantity to Add <span>*</span></label>
                    <input type="number" name="quantity" id="add-quantity"
                           class="si-form-input" min="1" placeholder="e.g. 50" required>
                </div>
                <div class="si-form-actions">
                    <button type="button" class="si-btn si-btn--ghost" onclick="closeModal('addStockModal')">Cancel</button>
                    <button type="submit" class="si-btn si-btn--primary">
                        <i class="fas fa-check"></i> Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Remove Stock Modal ── -->
<div id="removeStockModal" class="si-modal">
    <div class="si-modal-box">
        <div class="si-modal-head">
            <h2><i class="fas fa-minus-circle"></i> Remove Stock</h2>
            <button class="si-modal-close" onclick="closeModal('removeStockModal')">&times;</button>
        </div>
        <div class="si-modal-body">
            <div class="si-modal-product">
                <div>
                    <div class="si-modal-pname" id="rem-pname">—</div>
                    <div class="si-modal-pstock" id="rem-pstock">Current stock: <strong>0</strong> units</div>
                </div>
                <i class="fas fa-minus-circle" style="color:var(--si-red);font-size:1.5rem;"></i>
            </div>
            <form method="POST" action="/shop-owner/inventory/remove-stock" id="removeStockForm">
                <input type="hidden" name="product_id" id="rem-product-id">
                <div class="si-form-group">
                    <label for="rem-quantity">Quantity to Remove <span>*</span></label>
                    <input type="number" name="quantity" id="rem-quantity"
                           class="si-form-input" min="1" placeholder="e.g. 10" required>
                </div>
                <div class="si-form-group">
                    <label for="rem-reason">Reason <span>*</span></label>
                    <select name="reason" id="rem-reason" class="si-form-sel" required>
                        <option value="">— Select reason —</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                        <option value="returned">Returned by customer</option>
                        <option value="lost">Lost / Stolen</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="si-form-actions">
                    <button type="button" class="si-btn si-btn--ghost" onclick="closeModal('removeStockModal')">Cancel</button>
                    <button type="submit" class="si-btn si-btn--danger">
                        <i class="fas fa-trash"></i> Remove Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Reorder Level Modal ── -->
<div id="reorderModal" class="si-modal">
    <div class="si-modal-box">
        <div class="si-modal-head">
            <h2><i class="fas fa-sliders-h"></i> Set Reorder Level</h2>
            <button class="si-modal-close" onclick="closeModal('reorderModal')">&times;</button>
        </div>
        <div class="si-modal-body">
            <div class="si-modal-product">
                <div>
                    <div class="si-modal-pname" id="reorder-pname">—</div>
                    <div class="si-modal-pstock" style="color:var(--si-slate4);">
                        Alert when stock falls at or below this level
                    </div>
                </div>
                <i class="fas fa-bell" style="color:var(--si-amber);font-size:1.5rem;"></i>
            </div>
            <form method="POST" action="/shop-owner/inventory/update-min-stock" id="reorderForm">
                <input type="hidden" name="product_id" id="reorder-product-id">
                <div class="si-form-group">
                    <label for="reorder-level">Reorder Level <span>*</span></label>
                    <input type="number" name="min_stock_level" id="reorder-level"
                           class="si-form-input" min="0" placeholder="e.g. 10" required>
                </div>
                <div class="si-form-actions">
                    <button type="button" class="si-btn si-btn--ghost" onclick="closeModal('reorderModal')">Cancel</button>
                    <button type="submit" class="si-btn si-btn--primary">
                        <i class="fas fa-check"></i> Save Level
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="siToast" class="si-toast"></div>

<script src="/public/js/shop-owner-sidebar.js"></script>
<script src="/public/js/pages/shop-owner-inventory.js"></script>
