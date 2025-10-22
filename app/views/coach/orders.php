<?php $currentPage = 'orders'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - GoPlay Coach</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition: all 0.2s ease-in-out;
        }

        .main-content {
            padding: 2rem;
            background: var(--background-light);
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: white;
            color: var(--primary-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .orders-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: #dbeafe;
            color: var(--primary-color);
        }

        .stat-icon.delivered {
            background: #d1fae5;
            color: var(--success-color);
        }

        .stat-icon.pending {
            background: #fef3c7;
            color: var(--warning-color);
        }

        .stat-icon.amount {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .stat-content h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .stat-content p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .orders-filters {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            background: white;
            border-radius: 2rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .filter-tab:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-tab.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            min-width: 250px;
        }

        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 0.9rem;
        }

        .search-box i {
            color: var(--text-light);
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .order-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: var(--transition);
        }

        .order-card:hover {
            box-shadow: var(--shadow-md);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: var(--background-light);
            border-bottom: 1px solid var(--border-color);
        }

        .order-info {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .order-id {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .order-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-delivered {
            background: #d1fae5;
            color: var(--success-color);
        }

        .status-pending {
            background: #fef3c7;
            color: var(--warning-color);
        }

        .status-processing {
            background: #dbeafe;
            color: var(--primary-color);
        }

        .status-cancelled {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .order-body {
            padding: 1.5rem;
        }

        .order-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .order-item {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: var(--border-radius);
            object-fit: cover;
            background: var(--background-light);
            border: 1px solid var(--border-color);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .item-meta {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .order-total {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .total-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .order-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .tracking-info {
            background: var(--primary-light);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .tracking-info i {
            color: var(--primary-color);
        }

        .tracking-text {
            flex: 1;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .orders-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: 100%;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .order-info {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .order-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .order-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>
                        <i class="fas fa-shopping-bag"></i>
                        My Orders
                    </h1>
                    <p>Track and manage your equipment orders</p>
                </div>
                <div class="header-actions">
                    <a href="/shop" class="btn btn-primary">
                        <i class="fas fa-store"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <div class="orders-container">
                <!-- Order Statistics -->
                <div class="orders-stats">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>8</h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon delivered">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>5</h3>
                            <p>Delivered</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>3</h3>
                            <p>In Progress</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon amount">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="stat-content">
                            <h3>LKR 45,200</h3>
                            <p>Total Spent</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="orders-filters">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">
                            All Orders
                        </button>
                        <button class="filter-tab" data-filter="processing">
                            Processing
                        </button>
                        <button class="filter-tab" data-filter="delivered">
                            Delivered
                        </button>
                        <button class="filter-tab" data-filter="cancelled">
                            Cancelled
                        </button>
                    </div>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search orders..." id="searchOrders">
                    </div>
                </div>

                <!-- Orders List -->
                <div class="orders-list" id="ordersList">
                    <!-- Order 1 - Processing -->
                    <div class="order-card" data-status="processing">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="order-id">
                                    <i class="fas fa-hashtag"></i> ORDER-2025-001
                                </span>
                                <span class="order-date">
                                    <i class="far fa-calendar"></i> October 20, 2025
                                </span>
                            </div>
                            <span class="order-status status-processing">
                                <i class="fas fa-shipping-fast"></i> Processing
                            </span>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <div class="order-item">
                                    <img src="/public/assets/images/products/cricket-bat.jpg" 
                                         alt="Cricket Bat" 
                                         class="item-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e5e7eb\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EProduct%3C/text%3E%3C/svg%3E'">
                                    <div class="item-details">
                                        <div class="item-name">Professional Cricket Bat</div>
                                        <div class="item-meta">Quantity: 1 | Size: Standard</div>
                                    </div>
                                    <div class="item-price">LKR 8,500</div>
                                </div>
                                <div class="order-item">
                                    <img src="/public/assets/images/products/cricket-ball.jpg" 
                                         alt="Cricket Balls" 
                                         class="item-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e5e7eb\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EProduct%3C/text%3E%3C/svg%3E'">
                                    <div class="item-details">
                                        <div class="item-name">Cricket Balls (Pack of 6)</div>
                                        <div class="item-meta">Quantity: 1 | Type: Leather</div>
                                    </div>
                                    <div class="item-price">LKR 3,600</div>
                                </div>
                            </div>
                            <div class="tracking-info">
                                <i class="fas fa-truck"></i>
                                <span class="tracking-text">
                                    Your order is being processed and will be shipped soon.
                                    <strong>Tracking ID: TRK2025001</strong>
                                </span>
                            </div>
                            <div class="order-footer">
                                <div class="order-total">
                                    <span class="total-label">Order Total</span>
                                    <span class="total-amount">LKR 12,100</span>
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-outline btn-sm" onclick="viewOrderDetails('ORDER-2025-001')">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="trackOrder('TRK2025001')">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Track Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order 2 - Delivered -->
                    <div class="order-card" data-status="delivered">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="order-id">
                                    <i class="fas fa-hashtag"></i> ORDER-2025-002
                                </span>
                                <span class="order-date">
                                    <i class="far fa-calendar"></i> October 15, 2025
                                </span>
                            </div>
                            <span class="order-status status-delivered">
                                <i class="fas fa-check-circle"></i> Delivered
                            </span>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <div class="order-item">
                                    <img src="/public/assets/images/products/training-cones.jpg" 
                                         alt="Training Cones" 
                                         class="item-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e5e7eb\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EProduct%3C/text%3E%3C/svg%3E'">
                                    <div class="item-details">
                                        <div class="item-name">Training Cones Set (20pcs)</div>
                                        <div class="item-meta">Quantity: 2 | Color: Orange</div>
                                    </div>
                                    <div class="item-price">LKR 4,200</div>
                                </div>
                            </div>
                            <div class="order-footer">
                                <div class="order-total">
                                    <span class="total-label">Order Total</span>
                                    <span class="total-amount">LKR 4,200</span>
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-outline btn-sm" onclick="viewOrderDetails('ORDER-2025-002')">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="reorder('ORDER-2025-002')">
                                        <i class="fas fa-redo"></i>
                                        Reorder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order 3 - Pending -->
                    <div class="order-card" data-status="processing">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="order-id">
                                    <i class="fas fa-hashtag"></i> ORDER-2025-003
                                </span>
                                <span class="order-date">
                                    <i class="far fa-calendar"></i> October 18, 2025
                                </span>
                            </div>
                            <span class="order-status status-pending">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <div class="order-item">
                                    <img src="/public/assets/images/products/sports-bag.jpg" 
                                         alt="Sports Bag" 
                                         class="item-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e5e7eb\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EProduct%3C/text%3E%3C/svg%3E'">
                                    <div class="item-details">
                                        <div class="item-name">Professional Sports Bag</div>
                                        <div class="item-meta">Quantity: 1 | Color: Black</div>
                                    </div>
                                    <div class="item-price">LKR 6,500</div>
                                </div>
                            </div>
                            <div class="tracking-info">
                                <i class="fas fa-info-circle"></i>
                                <span class="tracking-text">
                                    Payment confirmation pending. Please complete payment to proceed.
                                </span>
                            </div>
                            <div class="order-footer">
                                <div class="order-total">
                                    <span class="total-label">Order Total</span>
                                    <span class="total-amount">LKR 6,500</span>
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-outline btn-sm" onclick="viewOrderDetails('ORDER-2025-003')">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="completePayment('ORDER-2025-003')">
                                        <i class="fas fa-credit-card"></i>
                                        Complete Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order 4 - Delivered -->
                    <div class="order-card" data-status="delivered">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="order-id">
                                    <i class="fas fa-hashtag"></i> ORDER-2025-004
                                </span>
                                <span class="order-date">
                                    <i class="far fa-calendar"></i> October 10, 2025
                                </span>
                            </div>
                            <span class="order-status status-delivered">
                                <i class="fas fa-check-circle"></i> Delivered
                            </span>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <div class="order-item">
                                    <img src="/public/assets/images/products/gloves.jpg" 
                                         alt="Gloves" 
                                         class="item-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e5e7eb\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EProduct%3C/text%3E%3C/svg%3E'">
                                    <div class="item-details">
                                        <div class="item-name">Batting Gloves Premium</div>
                                        <div class="item-meta">Quantity: 1 | Size: Large</div>
                                    </div>
                                    <div class="item-price">LKR 5,800</div>
                                </div>
                            </div>
                            <div class="order-footer">
                                <div class="order-total">
                                    <span class="total-label">Order Total</span>
                                    <span class="total-amount">LKR 5,800</span>
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-outline btn-sm" onclick="viewOrderDetails('ORDER-2025-004')">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="writeReview('ORDER-2025-004')">
                                        <i class="fas fa-star"></i>
                                        Write Review
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Filter orders
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                const orders = document.querySelectorAll('.order-card');

                orders.forEach(order => {
                    if (filter === 'all') {
                        order.style.display = 'block';
                    } else {
                        order.style.display = order.dataset.status === filter ? 'block' : 'none';
                    }
                });
            });
        });

        // Search orders
        document.getElementById('searchOrders').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const orders = document.querySelectorAll('.order-card');

            orders.forEach(order => {
                const orderId = order.querySelector('.order-id').textContent.toLowerCase();
                const itemNames = Array.from(order.querySelectorAll('.item-name'))
                    .map(item => item.textContent.toLowerCase())
                    .join(' ');

                if (orderId.includes(searchTerm) || itemNames.includes(searchTerm)) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            });
        });

        // View order details
        function viewOrderDetails(orderId) {
            alert(`Viewing details for ${orderId}`);
            // In production: window.location.href = `/coach/orders/${orderId}`;
        }

        // Track order
        function trackOrder(trackingId) {
            alert(`Tracking order: ${trackingId}`);
            // In production: window.location.href = `/track-order/${trackingId}`;
        }

        // Reorder
        function reorder(orderId) {
            if (confirm('Do you want to reorder these items?')) {
                alert(`Reordering items from ${orderId}`);
                // In production: Add items to cart and redirect
            }
        }

        // Complete payment
        function completePayment(orderId) {
            alert(`Redirecting to payment page for ${orderId}`);
            // In production: window.location.href = `/payment/${orderId}`;
        }

        // Write review
        function writeReview(orderId) {
            alert(`Write a review for ${orderId}`);
            // In production: Open review modal or redirect
        }
    </script>
</body>
</html>
