<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<?php 
$title = 'Inventory - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/pages/shop-owner-inventory.js'];
?>

<div class="shop-owner-dashboard">
  <!-- Sidebar -->
<?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="products-header">
      <h1>Inventory</h1>
    </header>

    <!-- 🔹 Search + Add Product Container -->
    <section class="search-add-container">
      <div class="products-search">
        <input type="text" placeholder="Search inventory...">
        <button class="btn-add">+ Add Product</button>
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

        <!-- Item Card 1 -->
        <div class="inventory-card inventory-item-card">
          <div class="col-product">Football</div>
          <div class="col-sku">FB-001</div>
          <div class="col-stock"><span class="stock-chip">25</span></div>
          <div class="col-reorder">10</div>
          <div class="col-status"><span class="status-badge in-stock">In Stock</span></div>
          <div class="col-updated">2025-08-25</div>
          <div class="col-actions">
            <a href="#" class="btn-update">Update</a>
            <a href="#" class="btn-delete">Remove</a>
          </div>
        </div>

        <!-- Item Card 2 -->
        <div class="inventory-card inventory-item-card">
          <div class="col-product">Basketball</div>
          <div class="col-sku">BB-014</div>
          <div class="col-stock"><span class="stock-chip stock-low">8</span></div>
          <div class="col-reorder">12</div>
          <div class="col-status"><span class="status-badge low-stock">Low Stock</span></div>
          <div class="col-updated">2025-08-20</div>
          <div class="col-actions">
            <a href="#" class="btn-update">Update</a>
            <a href="#" class="btn-delete">Remove</a>
          </div>
        </div>

      </div>
    </section>
  </main>
</div>

<style>
/* ----- Layout base (unchanged) ----- */
.shop-owner-dashboard { display: flex; min-height: 100vh; }
.dashboard-sidebar {
  width: 280px; background: #1e293b; color: #fff;
  position: fixed; top: 70px; left: 0; height: calc(100vh - 70px);
}
.dashboard-content { flex: 1; margin-left: 280px; padding: 20px; background: #f9f9f9; }

/* Header */
.products-header { margin-bottom: 1rem; }

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
.products-search .btn-add {
  padding: .55rem 1rem;
  border: none;
  border-radius: 6px;
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}
.products-search .btn-add:hover { background: #0d8f2b; }

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

.inventory-header-card { background: #f7f7f7ff; font-weight: 700; }
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
}
.stock-low { background: #fff2f0; color: #d93025; }

.status-badge { font-weight: 600; font-size: .9rem; }
.status-badge.in-stock { color: #0fa930; }
.status-badge.low-stock { color: #ff8b00; }

/* Actions */
.col-actions {
  display: flex; justify-content: center; gap: 8px;
}
.btn-update, .btn-delete {
  padding: 6px 12px; border-radius: 6px; text-decoration: none;
  font-size: .85rem; font-weight: 600; color: #fff;
}
.btn-update { background: #2563eb; }
.btn-delete { background: #b60909; }

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
