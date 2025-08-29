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
              <li class="active"><a href="/shop-owner/products.php"><i class="fas fa-box"></i><span>Products</span><span class="badge">156</span></a></li>
              <li><a href="/shop-owner/orders"><i class="fas fa-shopping-cart"></i><span>Orders</span><span class="badge new">12</span></a></li>
              <li><a href="/shop-owner/inventory"><i class="fas fa-warehouse"></i><span>Inventory</span><span class="badge warning">5</span></a></li>
              <li><a href="/shop-owner/sales"><i class="fas fa-chart-line"></i><span>Sales</span></a></li>
              <li><a href="/shop-owner/customers"><i class="fas fa-users"></i><span>Customers</span><span class="badge">234</span></a></li>
              <li><a href="/shop-owner/reviews"><i class="fas fa-star"></i><span>Reviews</span><span class="badge">45</span></a></li>
              <li class="nav-divider"></li>
              <li><a href="/shop-owner/profile"><i class="fas fa-user"></i><span>Profile</span></a></li>
              <li><a href="/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
          </ul>
      </nav>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="products-header">
      <h1>Products</h1>
      <div class="products-search">
        <input type="text" placeholder="Search products...">
        <button>+ Add Product</button>
      </div>
    </header>

    <section class="content">
      <table class="products-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Status</th>
            <th>Brand</th>
            <th colspan="2" style="width:140px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Football</td>
            <td>FB-001</td>
            <td><span class="stock-chip">25</span></td>
            <td>₹1200</td>
            <td><span style="color:green;">Active</span></td>
            <td>Nike</td>
            <td class="action-links"><a href="#">Edit</a></td>
            <td class="action-links-delete"><a href="#">Delete</a></td>
          </tr>
          <tr>
            <td>Basketball</td>
            <td>BB-014</td>
            <td><span class="stock-chip stock-low">10</span></td>
            <td>₹1450</td>
            <td><span style="color:orange;">Draft</span></td>
            <td>Puma</td>
            <td class="action-links"><a href="#">Edit</a></td>
            <td class="action-links-delete"><a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
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
  margin-bottom: 1.5rem;
}

.products-search {
  display: flex;
  gap: 1rem;
}

.products-search input {
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

/* Table */
.products-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.products-table th,
.products-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
  text-align: center;
}

.products-table th {
  background: #efeeeeff;
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

.action-links a {
  padding: .25rem 2rem;
  border-radius: 5px;
  margin-right: .5rem;
  text-decoration: none;
  background: #0fa930ff;
  color: #ffffffff;
}

.action-links-delete a {
  padding: .25rem 2rem;
  border-radius: 5px;
  margin-right: .5rem;
  text-decoration: none;
  background: #b60909ff;
  color: #ffffffff;
}

.action-links a :hover {
  text-decoration: underline;
}



</style>
