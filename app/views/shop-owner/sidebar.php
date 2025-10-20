<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            <li class="<?= $_SERVER['REQUEST_URI'] == '/shop-owner/dashboard' ? 'active' : '' ?>">
            <li class="<?= $_SERVER['REQUEST_URI'] == '/shop-owner/dashboard' ? 'active' : '' ?>">
                <a href="/shop-owner/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/products') !== false ? 'active' : '' ?>">
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/products') !== false ? 'active' : '' ?>">
                <a href="/shop-owner/products">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                    
                    
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/orders') !== false ? 'active' : '' ?>">
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/orders') !== false ? 'active' : '' ?>">
                <a href="/shop-owner/orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                    
                    
                </a>
            </li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/inventory') !== false ? 'active' : '' ?>">
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/inventory') !== false ? 'active' : '' ?>">
                <a href="/shop-owner/inventory">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventory</span>
                    
                    
                </a>
            </li>
            <!--<li>
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
            </li>-->
            <li>
                <a href="/shop-owner/reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                    
                    
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/profile') !== false ? 'active' : '' ?>">
            <li class="<?= strpos($_SERVER['REQUEST_URI'], '/shop-owner/profile') !== false ? 'active' : '' ?>">
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
