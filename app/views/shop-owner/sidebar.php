<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
// Determine the active page based on current URL
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$activePage = '';

if (strpos($currentPath, '/shop-owner/dashboard') !== false) {
    $activePage = 'dashboard';
} elseif (strpos($currentPath, '/shop-owner/products') !== false) {
    $activePage = 'products';
} elseif (strpos($currentPath, '/shop-owner/orders') !== false) {
    $activePage = 'orders';
} elseif (strpos($currentPath, '/shop-owner/inventory') !== false) {
    $activePage = 'inventory';
} elseif (strpos($currentPath, '/shop-owner/reviews') !== false) {
    $activePage = 'reviews';
} elseif (strpos($currentPath, '/shop-owner/earnings') !== false) {
    $activePage = 'earnings';
} elseif (strpos($currentPath, '/shop-owner/profile') !== false) {
    $activePage = 'profile';
}
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
            <li class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $activePage === 'products' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/products">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="<?php echo $activePage === 'orders' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="<?php echo $activePage === 'inventory' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/inventory">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <li class="<?php echo $activePage === 'reviews' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li class="<?php echo $activePage === 'earnings' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/earnings">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings & Payouts</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="<?php echo $activePage === 'profile' ? 'active' : ''; ?>">
                <a href="<?= $_base ?>/shop-owner/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= $_base ?>/logout" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Sidebar JavaScript -->
<script src="<?= $_base ?>/public/js/shop-owner-sidebar.js"></script>
