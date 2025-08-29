<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<?php 
$title = 'Shop Owner Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/pages/shop-owner-dashboard.js'];
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
                <li class="active">
                    <a href="/shop-owner/dashboard">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                        <span class="badge">156</span>
                    </a>
                </li>
                <li>
                    <a href="/shop-owner/orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                        <span class="badge new">12</span>
                    </a>
                </li>
                <li>
                    <a href="Inventory.php">
                        <i class="fas fa-warehouse"></i>
                        <span>Inventory</span>
                        <span class="badge warning">5</span>
                    </a>
                </li>
                <li>
                    <a href="/shop-owner/sales">
                        <i class="fas fa-chart-line"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li>
                    <a href="/shop-owner/customers">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                        <span class="badge">234</span>
                    </a>
                </li>
                <li>
                    <a href="/shop-owner/reviews">
                        <i class="fas fa-star"></i>
                        <span>Reviews</span>
                        <span class="badge">45</span>
                    </a>
                </li>
                <li class="nav-divider"></li>
                <li>
                    <a href="/shop-owner/profile">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="/logout" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Shop Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">7</span>
                    </button>
                </div>
                <div class="shop-profile">
                    <div class="profile-info">
                        <span class="profile-name">Rohan Gunaratne</span>
                        <span class="profile-role">Shop Owner</span>
                    </div>
                    <img src="/public/assets/images/shop-owner-avatar.jpg" alt="Shop Owner" class="profile-avatar">
                    <button class="profile-dropdown">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Monthly Revenue</h3>
                        <p class="stat-number">₹1,23,450</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>18% this month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-number">89</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>12 new orders</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Products</h3>
                        <p class="stat-number">156</p>
                        <div class="stat-change neutral">
                            <i class="fas fa-exclamation"></i>
                            <span>5 low stock</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Customers</h3>
                        <p class="stat-number">234</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>15 new customers</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Sales Overview -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Sales Overview</h3>
                        <div class="card-actions">
                            <select class="time-filter" id="salesFilter">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-card recent-orders">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                        <a href="/shop-owner/orders" class="view-all">View All</a>
                    </div>
                    <div class="orders-list">
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1001</h4>
                                <p>Kavinda Ranasighe</p>
                                <span class="order-details">3 items • ₹2,450</span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge processing">Processing</span>
                                <span class="order-time">2 hours ago</span>
                            </div>
                        </div>
                        
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1002</h4>
                                <p>Sanduni Rajapakse</p>
                                <span class="order-details">2 items • ₹1,800</span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge shipped">Shipped</span>
                                <span class="order-time">5 hours ago</span>
                            </div>
                        </div>
                        
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Order #ORD-1003</h4>
                                <p>Dilan Wijesinghe</p>
                                <span class="order-details">1 item • ₹650</span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge delivered">Delivered</span>
                                <span class="order-time">1 day ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="dashboard-card top-products">
                    <div class="card-header">
                        <h3>Top Selling Products</h3>
                        <div class="performance-period">This Month</div>
                    </div>
                    <div class="products-list">
                        <div class="product-item">
                            <div class="product-info">
                                <img src="/public/assets/images/product1.jpg" alt="Product" class="product-image">
                                <div class="product-details">
                                    <h4>Cricket Bat - Professional</h4>
                                    <p>₹3,500 • 23 sold</p>
                                </div>
                            </div>
                            <div class="product-performance">
                                <div class="performance-bar">
                                    <div class="performance-fill" style="width: 92%"></div>
                                </div>
                                <span class="performance-score">92%</span>
                            </div>
                        </div>
                        
                        <div class="product-item">
                            <div class="product-info">
                                <img src="/public/assets/images/product2.jpg" alt="Product" class="product-image">
                                <div class="product-details">
                                    <h4>Football - Official Size</h4>
                                    <p>₹1,200 • 18 sold</p>
                                </div>
                            </div>
                            <div class="product-performance">
                                <div class="performance-bar">
                                    <div class="performance-fill" style="width: 78%"></div>
                                </div>
                                <span class="performance-score">78%</span>
                            </div>
                        </div>
                        
                        <div class="product-item">
                            <div class="product-info">
                                <img src="/public/assets/images/product3.jpg" alt="Product" class="product-image">
                                <div class="product-details">
                                    <h4>Tennis Racket - Pro</h4>
                                    <p>₹4,200 • 15 sold</p>
                                </div>
                            </div>
                            <div class="product-performance">
                                <div class="performance-bar">
                                    <div class="performance-fill" style="width: 65%"></div>
                                </div>
                                <span class="performance-score">65%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Alerts -->
                <div class="dashboard-card inventory-alerts">
                    <div class="card-header">
                        <h3>Inventory Alerts</h3>
                        <span class="alert-count">5 items need attention</span>
                    </div>
                    <div class="alerts-list">
                        <div class="alert-item low-stock">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-info">
                                <h4>Cricket Ball - Leather</h4>
                                <p>Only 3 left in stock</p>
                            </div>
                            <button class="btn-restock">Restock</button>
                        </div>
                        
                        <div class="alert-item out-of-stock">
                            <div class="alert-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="alert-info">
                                <h4>Badminton Racket Set</h4>
                                <p>Out of stock</p>
                            </div>
                            <button class="btn-restock">Restock</button>
                        </div>
                        
                        <div class="alert-item low-stock">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-info">
                                <h4>Swimming Goggles</h4>
                                <p>Only 2 left in stock</p>
                            </div>
                            <button class="btn-restock">Restock</button>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card quick-actions">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="/shop-owner/products/create" class="action-btn primary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Product</span>
                        </a>
                        <a href="/shop-owner/orders/process" class="action-btn secondary">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Process Orders</span>
                        </a>
                        <a href="/shop-owner/inventory" class="action-btn warning">
                            <i class="fas fa-warehouse"></i>
                            <span>Check Inventory</span>
                        </a>
                        <a href="/shop-owner/promotions" class="action-btn success">
                            <i class="fas fa-tags"></i>
                            <span>Create Promotion</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card recent-reviews">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                        <a href="/shop-owner/reviews" class="view-all">View All</a>
                    </div>
                    <div class="reviews-list">
                        <div class="review-item">
                            <div class="reviewer-info">
                                <img src="/public/assets/images/user1.jpg" alt="User" class="reviewer-avatar">
                                <div class="reviewer-details">
                                    <h5>Kavinda Ranasighe</h5>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <span class="review-product">Cricket Bat</span>
                            </div>
                            <p class="review-text">"Excellent quality cricket bat! Great value for money. Fast delivery too."</p>
                            <span class="review-time">3 hours ago</span>
                        </div>
                        
                        <div class="review-item">
                            <div class="reviewer-info">
                                <img src="/public/assets/images/user2.jpg" alt="User" class="reviewer-avatar">
                                <div class="reviewer-details">
                                    <h5>Sanduni Rajapakse</h5>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <span class="review-product">Tennis Racket</span>
                            </div>
                            <p class="review-text">"Good tennis racket, but delivery was a bit delayed. Overall satisfied with the quality."</p>
                            <span class="review-time">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>