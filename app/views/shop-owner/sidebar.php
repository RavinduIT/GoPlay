<link rel="stylesheet" href="/public/css/pages/shop owner/sidebar.css">
<?php 
$title = 'Shop Owner Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/shop owner/sidebar.css'];
$additionalJS = ['/public/js/pages/shop owner/sidebar.js'];
?>
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
                <a href="inventory.php">
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
                <a href="reviews.php">
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
<script src="/public/js/pages/shop owner/sidebar.js" defer></script>
