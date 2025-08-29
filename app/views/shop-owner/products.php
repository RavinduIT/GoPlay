<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<?php 
$title = 'Shop Products - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/pages/shop-owner-products.js'];
?>

<div class="shop-owner-dashboard">
  <!-- Sidebar -->
  <aside class="dashboard-sidebar" id="dashboardSidebar">
      <div class="sidebar-header">
          <div class="logo">
              <i class="fas fa-store"></i>
              <span>Shop Manager</span>
          </div>
          <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
              <i class="fas fa-times"></i>
          </button>
      </div>

      <nav class="sidebar-nav">
          <ul>
              <li><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
              <li class="active"><a href="products.php"><i class="fas fa-box"></i><span>Products</span><span class="badge">156</span></a></li>
              <li><a href="orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span><span class="badge new">12</span></a></li>
              <li><a href="Inventory.php"><i class="fas fa-warehouse"></i><span>Inventory</span><span class="badge warning">5</span></a></li>
              <li><a href="sales.php"><i class="fas fa-chart-line"></i><span>Sales</span></a></li>
              <li><a href="/shop-owner/customers"><i class="fas fa-users"></i><span>Customers</span><span class="badge">234</span></a></li>
              <li><a href="reviews.php"><i class="fas fa-star"></i><span>Reviews</span><span class="badge">45</span></a></li>
              <li class="nav-divider"></li>
              <li><a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
              <li><a href="/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
          </ul>
      </nav>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="products-header">
      <h1>Products</h1>
    </header>

    <!-- Search + Add Container -->
    <div class="products-actions-container">
      <div class="products-search">
        <input type="text" placeholder="Search products...">
        <button>+ Add Product</button>
      </div>
    </div>

    <!-- Product Cards -->
    <section class="products-cards">
      <!-- Header Card -->
      <div class="product-card header-card">
        <div>Product</div>
        <div>SKU</div>
        <div>Stock</div>
        <div>Price</div>
        <div>Status</div>
        <div>Brand</div>
        <div>Last Updated</div>
        <div class="col-actions">Actions</div>
      </div>

      <!-- Example Data Cards -->
      <div class="product-card">
        <div>Football</div>
        <div>FB-001</div>
        <div><span class="stock-chip">25</span></div>
        <div>₹1200</div>
        <div><span style="color:green;">Active</span></div>
        <div>Nike</div>
        <div>2025-08-28</div>
        <div class="card-actions">
          <a href="#" class="edit-btn">Edit</a>
          <a href="#" class="delete-btn">Delete</a>
        </div>
      </div>

      <div class="product-card">
        <div>Basketball</div>
        <div>BB-014</div>
        <div><span class="stock-chip stock-low">10</span></div>
        <div>₹1450</div>
        <div><span style="color:orange;">Draft</span></div>
        <div>Puma</div>
        <div>2025-08-27</div>
        <div class="card-actions">
          <a href="#" class="edit-btn">Edit</a>
          <a href="#" class="delete-btn">Delete</a>
        </div>
      </div>
    </section>
  </main>
</div>

<style>
/* Dashboard Layout */
.shop-owner-dashboard {
  display: flex;
  min-height: 100vh;
}

.dashboard-sidebar {
  width: 240px;
  background: #1e293b;
  color: white;
  position: fixed;
  top:0;
  left:0;
  bottom:0;
}

.dashboard-content {
  flex: 1;
  margin-left:240px;
  padding: 20px;
  background: #f9f9f9;
}

/* Header */
.products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

/* Search + Add Container */
.products-actions-container {
  background: white;
  border: 1px solid #e3e3e3;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.products-search {
  display: flex;
  gap: 1rem;
  width: 100%;
}

.products-search input {
  flex: 1;
  padding: .55rem .75rem;
  border: 1px solid #e3e3e3;
  border-radius: 6px;
}

.products-search button {
  padding: .55rem .9rem;
  border: none;
  border-radius: 6px;
  background: #0fa930ff;
  color: white;
  cursor: pointer;
}

/* Cards Layout */
.products-cards {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.col-actions {
  display: flex; justify-content: center; gap: 8px;
}
.product-card {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  align-items: center;
  background: white;
  padding: 12px 15px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

.header-card {
  background: #f7f7f7ff;
  font-weight: bold;
}

.stock-chip {
  padding: .25rem .8rem;
  border-radius: 999px;
  font-size: .85rem;
  background: #eef7ff;
  color: #0b6bd3;
}

.stock-low {
  background: #fff2f0;
  color: #d93025;
}

.card-actions {
  display: flex;
  gap: 1rem;
}

.edit-btn, .delete-btn {
  padding: .3rem .9rem;
  border-radius: 5px;
  text-decoration: none;
  font-size: .85rem;
  color: white;
  gap: 0.5em;
}

.edit-btn {
  background: #0fa930ff;
}

.delete-btn {
  background: #b60909ff;
}
</style>
