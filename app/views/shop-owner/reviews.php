<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<?php 
$title = 'Shop Reviews - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = [];
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
              <li><a href="products.php"><i class="fas fa-box"></i><span>Products</span></a></li>
              <li><a href="orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span></a></li>
              <li><a href="inventory.php"><i class="fas fa-warehouse"></i><span>Inventory</span></a></li>
              <li><a href="sales.php"><i class="fas fa-chart-line"></i><span>Sales</span></a></li>
              <li><a href="/shop-owner/customers"><i class="fas fa-users"></i><span>Customers</span></a></li>
              <li class="active"><a href="reviews.php"><i class="fas fa-star"></i><span>Reviews</span><span class="badge">45</span></a></li>
              <li class="nav-divider"></li>
              <li><a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
              <li><a href="/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
          </ul>
      </nav>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="products-header">
      <h1>Customer Reviews</h1>
    </header>

    <!-- Search Container -->
    <div class="products-actions-container">
      <div class="products-search">
        <input type="text" placeholder="Search reviews...">
      </div>
    </div>

    <!-- Reviews Cards -->
    <section class="reviews-cards">
      <!-- Example Review Card -->
      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">John Doe</span>
          <span class="review-rating">
            ★★★★☆
          </span>
        </div>
        <div class="review-comment">
          Great product! Delivery was fast and the quality exceeded my expectations.
        </div>
      </div>

      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">Jane Smith</span>
          <span class="review-rating">
            ★★★★★
          </span>
        </div>
        <div class="review-comment">
          Excellent customer service and the product works perfectly. Highly recommend!
        </div>
      </div>

      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">Alex Johnson</span>
          <span class="review-rating">
            ★★★☆☆
          </span>
        </div>
        <div class="review-comment">
          Product is okay, but packaging could be improved.
        </div>
      </div>
    </section>
  </main>
</div>

<style>
/* Reviews Page Layout */
.reviews-cards {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Horizontal Review Card */
.review-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 15px 20px;
  box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.reviewer-name {
  font-weight: bold;
  font-size: 1rem;
}

.review-rating {
  color: #f59e0b; /* Amber stars */
  font-size: 1rem;
}

.review-comment {
  font-size: 0.95rem;
  color: #374151;
  line-height: 1.4;
}
</style>
