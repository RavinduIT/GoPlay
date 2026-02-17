<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load required models
require_once __DIR__ . '/../../../app/models/Product.php';

use App\Models\Product;

$productModel = new Product();
$shopOwnerId = $_SESSION['user_id'] ?? 0;

// Get inventory data with stock status
$inventory = $productModel->getInventoryWithStatus($shopOwnerId);

$title = 'Inventory Management - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/pages/shop-owner-inventory.js'];
?>

<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<?php 
$title = 'Inventory - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/shop-owner-sidebar.js', '/public/js/pages/shop-owner-inventory.js'];
?>

<div class="shop-owner-dashboard">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-main">
    <!-- Top Header -->
    <header class="dashboard-header">
      <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <div class="page-title-section">
          <h1 class="page-title">Inventory Management</h1>
          <p class="page-subtitle">Track and manage your product stock levels</p>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard-content">

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
      <i class="fas fa-exclamation-circle"></i>
      <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- 🔹 Search + Add Product Container -->
    <section class="search-add-container">
      <div class="products-search">
        <input type="text" id="inventorySearch" placeholder="Search inventory by name or SKU...">
      </div>
    </section>

    <!-- Inventory Cards (Header row + rows) -->
    <section class="content">
      <div class="inventory-list">

        <!-- Header Card (column titles) -->
        <div class="inventory-card inventory-header-card">
          <div class="col-product">Product</div>
          <div class="col-sku">SKU</div>
          <div class="col-stock">Available Stock</div>
          <div class="col-reorder">Reorder Level</div>
          <div class="col-status">Status</div>
          <div class="col-updated">Last Updated</div>
          <div class="col-actions">Actions</div>
        </div>

        <?php if (!empty($inventory)): ?>
          <?php foreach ($inventory as $item): 
            $stockClass = match($item['stock_status']) {
                'out-of-stock' => 'stock-out',
                'low-stock' => 'stock-low',
                default => ''
            };
            $statusBadgeClass = match($item['stock_status']) {
                'out-of-stock' => 'out-of-stock',
                'low-stock' => 'low-stock',
                default => 'in-stock'
            };
            $statusText = match($item['stock_status']) {
                'out-of-stock' => 'Out of Stock',
                'low-stock' => 'Low Stock',
                default => 'In Stock'
            };
          ?>
          <!-- Item Card -->
          <div class="inventory-card inventory-item-card">
            <div class="col-product"><?= htmlspecialchars($item['name']) ?></div>
            <div class="col-sku"><?= htmlspecialchars($item['sku'] ?: 'N/A') ?></div>
            <div class="col-stock"><span class="stock-chip <?= $stockClass ?>"><?= $item['stock_quantity'] ?></span></div>
            <div class="col-reorder"><?= $item['min_stock_level'] ?></div>
            <div class="col-status"><span class="status-badge <?= $statusBadgeClass ?>"><?= $statusText ?></span></div>
            <div class="col-updated"><?= date('Y-m-d', strtotime($item['updated_at'])) ?></div>
            <div class="col-actions">
              <a href="#" class="btn-update" onclick="openAddStockModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>', <?= $item['stock_quantity'] ?>); return false;">Add Stock</a>
              <a href="#" class="btn-delete" onclick="openRemoveStockModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>', <?= $item['stock_quantity'] ?>); return false;">Remove</a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Empty State -->
          <div class="empty-state">
            <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; color: #666;">No products found in inventory.</p>
            <p style="color: #999;">Add products in <a href="/shop-owner/products" style="color: #0fa930;">Product Management</a> first.</p>
          </div>
        <?php endif; ?>

      </div>
    </section>
    </div>
  </main>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddStockModal()">&times;</span>
    <h2><i class="fas fa-plus-circle"></i> Add Stock</h2>
    <form method="POST" action="/shop-owner/inventory/add-stock">
      <input type="hidden" name="product_id" id="add_product_id">
      <div class="form-group">
        <label><strong>Product:</strong></label>
        <p id="add_product_name" style="color: #333; font-weight: 600;"></p>
      </div>
      <div class="form-group">
        <label><strong>Current Stock:</strong></label>
        <p id="add_current_stock" style="color: #0fa930; font-weight: 600;"></p>
      </div>
      <div class="form-group">
        <label for="add_quantity">Quantity to Add: <span style="color: red;">*</span></label>
        <input type="number" name="quantity" id="add_quantity" required min="1" placeholder="Enter quantity">
      </div>
      <button type="submit" class="btn-primary">
        <i class="fas fa-check"></i> Add Stock
      </button>
    </form>
  </div>
</div>

<!-- Remove Stock Modal -->
<div id="removeStockModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeRemoveStockModal()">&times;</span>
    <h2><i class="fas fa-minus-circle"></i> Remove Stock</h2>
    <form method="POST" action="/shop-owner/inventory/remove-stock">
      <input type="hidden" name="product_id" id="remove_product_id">
      <div class="form-group">
        <label><strong>Product:</strong></label>
        <p id="remove_product_name" style="color: #333; font-weight: 600;"></p>
      </div>
      <div class="form-group">
        <label><strong>Current Stock:</strong></label>
        <p id="remove_current_stock" style="color: #d93025; font-weight: 600;"></p>
      </div>
      <div class="form-group">
        <label for="remove_quantity">Quantity to Remove: <span style="color: red;">*</span></label>
        <input type="number" name="quantity" id="remove_quantity" required min="1" placeholder="Enter quantity">
      </div>
      <div class="form-group">
        <label for="remove_reason">Reason: <span style="color: red;">*</span></label>
        <select name="reason" id="remove_reason" required>
          <option value="">-- Select Reason --</option>
          <option value="damaged">Damaged</option>
          <option value="expired">Expired</option>
          <option value="returned">Returned</option>
          <option value="lost">Lost/Stolen</option>
          <option value="other">Other</option>
        </select>
      </div>
      <button type="submit" class="btn-danger">
        <i class="fas fa-trash"></i> Remove Stock
      </button>
    </form>
  </div>
</div>

<style>
/* Alert Messages */
.alert {
  padding: 1rem 1.5rem;
  margin-bottom: 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border-left: 4px solid #28a745;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border-left: 4px solid #dc3545;
}

.alert i { font-size: 1.2rem; }

/* 🔹 Search + Add Product container */
.search-add-container {
  background: #fff;
  border: 1px solid #e3e3e3;
  border-radius: 10px;
  padding: 16px;
  margin-bottom: 1.5rem;
}
.products-search { display: flex; gap: 1rem; }
.products-search input {
  flex: 1;
  padding: .55rem .75rem;
  border: 1px solid #e3e3e3;
  border-radius: 6px;
}

/* ----- Inventory cards ----- */
.inventory-list { display: flex; flex-direction: column; gap: 10px; }

.inventory-card {
  display: grid;
  grid-template-columns: 2fr 1fr 1.2fr 1.2fr 1fr 1.2fr 1.3fr;
  align-items: center;
  gap: 12px;
  background: #fff;
  border: 1px solid #e3e3e3;
  border-radius: 10px;
  padding: 14px 16px;
}

.inventory-header-card { 
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-weight: 700;
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.8rem;
}
.inventory-card > div { text-align: center; }
.inventory-card .col-product { text-align: left; }

/* Chips / badges */
.stock-chip {
  padding: .25rem .8rem;
  border-radius: 999px;
  font-size: .85rem;
  background: #eef7ff;
  color: #0b6bd3;
  display: inline-block;
  font-weight: 600;
}
.stock-low { background: #fff2f0; color: #d93025; }
.stock-out { background: #fce8e6; color: #b60909; font-weight: 700; }

.status-badge { font-weight: 600; font-size: .9rem; }
.status-badge.in-stock { color: #0fa930; }
.status-badge.low-stock { color: #ff8b00; }
.status-badge.out-of-stock { color: #b60909; }

/* Actions */
.col-actions {
  display: flex; justify-content: center; gap: 8px;
}
.btn-update, .btn-delete {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  text-decoration: none;
}

.btn-update {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.btn-update:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-delete {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.btn-delete:hover {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  border-color: #ef4444;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background: #fff;
  border-radius: 10px;
  border: 2px dashed #e3e3e3;
  margin-top: 1rem;
}

/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  align-items: center;
  justify-content: center;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-content h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #333;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.modal-close {
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  color: #999;
  transition: color 0.2s;
}

.modal-close:hover { color: #333; }

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e3e3e3;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-group input:focus, .form-group select:focus {
  outline: none;
  border-color: #0fa930;
}

.btn-primary, .btn-danger {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1rem;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #0fa930;
  color: white;
}

.btn-primary:hover {
  background: #0d8f2b;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(15, 169, 48, 0.3);
}

.btn-danger {
  background: #b60909;
  color: white;
}

.btn-danger:hover {
  background: #9a0707;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(182, 9, 9, 0.3);
}

/* Responsive */
@media (max-width: 920px) {
  .inventory-card {
    grid-template-columns: 1.6fr 1fr 1fr 1fr;
    row-gap: 8px;
  }
  .inventory-card .col-status { order: 5; }
  .inventory-card .col-updated { order: 6; }
  .inventory-card .col-actions { order: 7; justify-content: flex-start; }
}
@media (max-width: 600px) {
  .inventory-card { grid-template-columns: 1fr 1fr; }
  .inventory-card .col-product { grid-column: 1 / -1; }
}
</style>

<script src="/public/js/pages/shop-owner-inventory.js"></script>
