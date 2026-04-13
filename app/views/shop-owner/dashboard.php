<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/shop-owner-dashboard.css">
<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Shop Owner Dashboard - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/shop-owner-sidebar.js', '/public/js/pages/shop-owner-dashboard.js'];

// Initialize default values if not set
$stats = $stats ?? [
    'total_products' => 0,
    'total_orders' => 0,
    'pending_orders' => 0,
    'low_stock' => 0,
    'monthly_revenue' => 0
];
$recentOrders = $recentOrders ?? [];
$topProducts = $topProducts ?? [];
$lowStockProducts = $lowStockProducts ?? [];
$recentReviews = $recentReviews ?? [];
?>

<div class="shop-owner-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

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
                <!--<div class="header-notifications">
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
                    <img src="<?= $_base ?>/public/assets/images/shop-owner-avatar.jpg" alt="Shop Owner" class="profile-avatar">
                    <button class="profile-dropdown">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>-->
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Monthly Revenue</h3>
                        <p class="stat-number">Rs <?php echo number_format($stats['monthly_revenue'], 2); ?></p>
                        <div class="stat-change positive">
                            <i class="fas fa-chart-line"></i>
                            <span>This month</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-number"><?php echo $stats['total_orders']; ?></p>
                        <div class="stat-change <?php echo $stats['pending_orders'] > 0 ? 'warning' : 'neutral'; ?>">
                            <i class="fas fa-clock"></i>
                            <span><?php echo $stats['pending_orders']; ?> pending</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Products</h3>
                        <p class="stat-number"><?php echo $stats['total_products']; ?></p>
                        <div class="stat-change <?php echo $stats['low_stock'] > 0 ? 'warning' : 'positive'; ?>">
                            <i class="fas fa-<?php echo $stats['low_stock'] > 0 ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                            <span><?php echo $stats['low_stock']; ?> low stock</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Shipped Orders</h3>
                        <p class="stat-number"><?php echo $stats['shipped_orders'] ?? 0; ?></p>
                        <div class="stat-change positive">
                            <i class="fas fa-shipping-fast"></i>
                            <span>In transit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Revenue Overview -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Revenue Overview</h3>
                        <div class="card-actions">
                            <span class="period-badge">This Month</span>
                        </div>
                    </div>
                    <div class="revenue-summary">
                        <div class="revenue-item">
                            <div class="revenue-label">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Monthly Revenue</span>
                            </div>
                            <div class="revenue-value">Rs <?php echo number_format($stats['monthly_revenue'], 2); ?></div>
                        </div>
                        <div class="revenue-item">
                            <div class="revenue-label">
                                <i class="fas fa-chart-line"></i>
                                <span>Total Revenue</span>
                            </div>
                            <div class="revenue-value">Rs <?php echo number_format($stats['total_revenue'], 2); ?></div>
                        </div>
                        <div class="revenue-item">
                            <div class="revenue-label">
                                <i class="fas fa-receipt"></i>
                                <span>Completed Order</span>
                            </div>
                            <div class="revenue-value"><?php echo $stats['delivered_orders'] ?? 0; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-card recent-orders">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                        <a href="<?= $_base ?>/shop-owner/orders" class="view-all">View All</a>
                    </div>
                    <div class="orders-list">
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <h4>Order #<?php echo htmlspecialchars($order['id']); ?></h4>
                                        <p><?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?></p>
                                        <span class="order-details"><?php echo $order['total_items'] ?? 1; ?> item(s) • Rs <?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                    <div class="order-status">
                                        <span class="status-badge <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span>
                                        <span class="order-time"><?php 
                                            $orderTime = strtotime($order['created_at']);
                                            $diff = time() - $orderTime;
                                            if ($diff < 3600) echo floor($diff/60) . ' min ago';
                                            elseif ($diff < 86400) echo floor($diff/3600) . ' hours ago';
                                            else echo floor($diff/86400) . ' days ago';
                                        ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No recent orders</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="dashboard-card top-products">
                    <div class="card-header">
                        <h3>Top Selling Products</h3>
                        <div class="performance-period">This Month</div>
                    </div>
                    <div class="products-list">
                        <?php if (!empty($topProducts)): ?>
                            <?php foreach ($topProducts as $product): 
                                $salesCount = $product['sales_count'] ?? 0;
                                $maxSales = !empty($topProducts) ? max(array_column($topProducts, 'sales_count')) : 1;
                                $percentage = $maxSales > 0 ? round(($salesCount / $maxSales) * 100) : 0;
                            ?>
                                <div class="product-item">
                                    <div class="product-info">
                                        <?php 
                                        $images = !empty($product['images']) ? (is_array($product['images']) ? $product['images'] : json_decode($product['images'], true)) : [];
                                        $imagePath = !empty($images) && isset($images[0]) ? $images[0] : null;
                                        ?>
                                        <?php if ($imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)): ?>
                                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Product" class="product-image">
                                        <?php else: ?>
                                            <div class="product-image-placeholder">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="product-details">
                                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                            <p>Rs <?php echo number_format($product['price'], 2); ?> • <?php echo $salesCount; ?> sold</p>
                                        </div>
                                    </div>
                                    <div class="product-performance">
                                        <div class="performance-bar">
                                            <div class="performance-fill" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <span class="performance-score"><?php echo $percentage; ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>No sales data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Inventory Alerts -->
                <div class="dashboard-card inventory-alerts">
                    <div class="card-header">
                        <h3>Inventory Alerts</h3>
                        <span class="alert-count"><?php echo count($lowStockProducts); ?> items need attention</span>
                    </div>
                    <div class="alerts-list">
                        <?php if (!empty($lowStockProducts)): ?>
                            <?php foreach ($lowStockProducts as $product): 
                                $stock = $product['stock_quantity'] ?? 0;
                                $alertClass = $stock == 0 ? 'out-of-stock' : 'low-stock';
                            ?>
                                <div class="alert-item <?php echo $alertClass; ?>">
                                    <div class="alert-icon">
                                        <i class="fas fa-<?php echo $stock == 0 ? 'times-circle' : 'exclamation-triangle'; ?>"></i>
                                    </div>
                                    <div class="alert-info">
                                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <p><?php echo $stock == 0 ? 'Out of stock' : 'Only ' . $stock . ' left in stock'; ?></p>
                                    </div>
                                    <a href="<?= $_base ?>/shop-owner/inventory" class="btn-restock">Restock</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p>All products are well stocked</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card quick-actions">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                        <p class="card-subtitle">Manage your shop efficiently</p>
                    </div>
                    <div class="actions-grid">
                        <a href="<?= $_base ?>/shop-owner/products" class="action-card purple">
                            <div class="action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="action-content">
                                <h4>Add Product</h4>
                                <p>Create new product listing</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                        
                        <a href="<?= $_base ?>/shop-owner/orders" class="action-card blue">
                            <div class="action-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="action-content">
                                <h4>View All Orders</h4>
                                <p><?php echo $stats['pending_orders']; ?> pending orders</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                        
                        <a href="<?= $_base ?>/shop-owner/reviews" class="action-card orange">
                            <div class="action-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="action-content">
                                <h4>Manage Reviews</h4>
                                <p>View customer feedback</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                        
                        <a href="<?= $_base ?>/shop-owner/inventory" class="action-card green">
                            <div class="action-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="action-content">
                                <h4>View Inventory</h4>
                                <p><?php echo $stats['low_stock']; ?> low stock items</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card recent-reviews">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                        <a href="<?= $_base ?>/shop-owner/reviews" class="view-all">View All</a>
                    </div>
                    <div class="reviews-list">
                        <?php if (!empty($recentReviews)): ?>
                            <?php foreach ($recentReviews as $review): ?>
                                <div class="review-item">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h5><?php echo htmlspecialchars($review['customer_name'] ?? 'Anonymous'); ?></h5>
                                            <div class="review-rating">
                                                <?php 
                                                $rating = $review['rating'] ?? 5;
                                                for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= $rating ? '' : ' far'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <span class="review-product"><?php echo htmlspecialchars($review['product_name'] ?? 'Product'); ?></span>
                                    </div>
                                    <p class="review-text">"<?php echo htmlspecialchars(substr($review['review_text'] ?? 'Great product!', 0, 100)); ?><?php echo strlen($review['review_text'] ?? '') > 100 ? '...' : ''; ?>"</p>
                                    <span class="review-time"><?php 
                                        $reviewTime = strtotime($review['created_at'] ?? 'now');
                                        $diff = time() - $reviewTime;
                                        if ($diff < 3600) echo floor($diff/60) . ' min ago';
                                        elseif ($diff < 86400) echo floor($diff/3600) . ' hours ago';
                                        else echo floor($diff/86400) . ' days ago';
                                    ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-star"></i>
                                <p>No reviews yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>