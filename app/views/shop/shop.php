<?php 
$title = 'Shop - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Unified Design System */
        :root {
            --primary-color: #28a745;
            --primary-dark: #218838;
            --primary-light: #d4edda;
            --secondary-color: #20c997;
            --accent-color: #007bff;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-light: #adb5bd;
            --background-light: #f8f9fa;
            --background-white: #ffffff;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--background-light);
        }

        .shop-container {
            min-height: 100vh;
        }

        /* Header Section */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q25,0 50,10 T100,10 V20 H0 Z" fill="rgba(255,255,255,0.1)"/></svg>') repeat-x;
            background-size: 200px 20px;
            animation: wave 20s linear infinite;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-200px); }
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-text h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .header-text p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .header-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-primary, .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--background-white);
            color: var(--primary-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Search Section */
        .search-section {
            background: var(--background-white);
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .search-bar {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 1rem;
        }

        .filter-select {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .search-btn {
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
        }

        .results-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .results-subtitle {
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .sort-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            cursor: pointer;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .product-image {
            height: 250px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            overflow: hidden;
        }

        .product-icon {
            font-size: 4rem;
            opacity: 0.8;
            color: white;
        }

        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .product-badge.new {
            background: var(--warning-color);
            color: var(--text-primary);
        }

        .product-badge.sale {
            background: var(--danger-color);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .quick-view-btn {
            background: var(--background-white);
            color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .quick-view-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .product-content {
            padding: 2rem;
        }

        .product-category {
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .product-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .stars i {
            color: var(--warning-color);
            font-size: 0.9rem;
        }

        .rating-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .price-current {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-original {
            font-size: 1rem;
            color: var(--text-light);
            text-decoration: line-through;
        }

        .price-discount {
            background: var(--danger-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-outline {
            padding: 0.75rem 1rem;
            border: 2px solid var(--primary-color);
            background: transparent;
            color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
            flex: 1;
            text-align: center;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .product-actions .btn-primary {
            padding: 0.75rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            flex: 1;
        }

        .product-actions .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Categories Section */
        .categories-section {
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .category-item {
            text-align: center;
            padding: 1.5rem 1rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-primary);
        }

        .category-item:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .category-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Cart Summary */
        .cart-summary {
            position: fixed;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            background: var(--background-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-heavy);
            min-width: 200px;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
        }

        .cart-summary.visible {
            opacity: 1;
            pointer-events: all;
        }

        .cart-icon {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-heavy);
            transition: var(--transition);
        }

        .cart-icon:hover {
            transform: scale(1.1);
            background: var(--primary-dark);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }

            .header-stats {
                justify-content: center;
            }

            .search-bar {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .filter-group {
                flex-direction: column;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .results-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 1rem;
            }

            .search-container {
                padding: 1rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .product-actions {
                flex-direction: column;
            }

            .cart-icon {
                bottom: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="shop-container">
        <!-- Navbar Component -->
        <div id="navbar-container"></div>

        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Sports Equipment Shop</h1>
                    <p>Premium sports gear and equipment for all your athletic needs</p>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24h</span>
                            <span class="stat-label">Delivery</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5</span>
                            <span class="stat-label">Rating</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn-primary">
                        <i class="fas fa-tag"></i>
                        View Deals
                    </button>
                    <button class="btn-secondary">
                        <i class="fas fa-heart"></i>
                        Wishlist
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-products" placeholder="Search for sports equipment..." class="search-input">
                    </div>
                    <div class="filter-group">
                        <select id="category-filter" class="filter-select">
                            <option value="">All Categories</option>
                            <option value="football">Football</option>
                            <option value="tennis">Tennis</option>
                            <option value="basketball">Basketball</option>
                            <option value="cricket">Cricket</option>
                            <option value="badminton">Badminton</option>
                            <option value="swimming">Swimming</option>
                            <option value="fitness">Fitness</option>
                        </select>
                        <select id="price-filter" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-50">LKR 0 - 5,000</option>
                            <option value="50-100">LKR 5,000 - 10,000</option>
                            <option value="100+">LKR 10,000+</option>
                        </select>
                    </div>
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Categories Section -->
            <div class="categories-section">
                <h2>Shop by Category</h2>
                <div class="categories-grid">
                    <a href="#" class="category-item" data-category="football">
                        <div class="category-icon">½</div>
                        <div class="category-name">Football</div>
                    </a>
                    <a href="#" class="category-item" data-category="tennis">
                        <div class="category-icon"><¾</div>
                        <div class="category-name">Tennis</div>
                    </a>
                    <a href="#" class="category-item" data-category="basketball">
                        <div class="category-icon"><À</div>
                        <div class="category-name">Basketball</div>
                    </a>
                    <a href="#" class="category-item" data-category="cricket">
                        <div class="category-icon"><Ï</div>
                        <div class="category-name">Cricket</div>
                    </a>
                    <a href="#" class="category-item" data-category="badminton">
                        <div class="category-icon"><ø</div>
                        <div class="category-name">Badminton</div>
                    </a>
                    <a href="#" class="category-item" data-category="swimming">
                        <div class="category-icon"><Ê</div>
                        <div class="category-name">Swimming</div>
                    </a>
                    <a href="#" class="category-item" data-category="fitness">
                        <div class="category-icon">=ª</div>
                        <div class="category-name">Fitness</div>
                    </a>
                </div>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    <h2>Featured Products</h2>
                    <p class="results-subtitle">Discover our top-rated sports equipment</p>
                </div>
                <div class="sort-controls">
                    <select class="sort-select">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid" id="products-grid">
                <!-- Product cards will be dynamically loaded here -->
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading products...</p>
                </div>
            </div>
        </div>

        <!-- Cart Icon -->
        <button class="cart-icon" onclick="toggleCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cart-count">0</span>
        </button>

        <!-- Cart Summary -->
        <div class="cart-summary" id="cart-summary">
            <h3>Cart Summary</h3>
            <div id="cart-items"></div>
            <div style="border-top: 1px solid var(--border-color); margin-top: 1rem; padding-top: 1rem;">
                <strong>Total: LKR <span id="cart-total">0</span></strong>
            </div>
            <button class="btn-primary" style="width: 100%; margin-top: 1rem;" onclick="checkout()">
                Checkout
            </button>
        </div>

        <!-- Footer Component -->
        <div id="footer-container"></div>
    </div>

    <script>
        // Sample product data
        const products = [
            {
                id: 1,
                name: "Professional Football",
                category: "football",
                price: 3500,
                originalPrice: 4000,
                description: "FIFA approved professional football for matches and training",
                rating: 4.8,
                reviews: 124,
                image: "/images/football.jpg",
                badge: "sale",
                inStock: true
            },
            {
                id: 2,
                name: "Tennis Racket Pro",
                category: "tennis",
                price: 15000,
                originalPrice: null,
                description: "Lightweight carbon fiber tennis racket for professional play",
                rating: 4.9,
                reviews: 89,
                image: "/images/tennis-racket.jpg",
                badge: "new",
                inStock: true
            },
            {
                id: 3,
                name: "Basketball Shoes",
                category: "basketball",
                price: 12000,
                originalPrice: 15000,
                description: "High-performance basketball shoes with superior grip",
                rating: 4.7,
                reviews: 156,
                image: "/images/basketball-shoes.jpg",
                badge: "sale",
                inStock: true
            },
            {
                id: 4,
                name: "Cricket Bat",
                category: "cricket",
                price: 8500,
                originalPrice: null,
                description: "Premium willow cricket bat for professional players",
                rating: 4.6,
                reviews: 73,
                image: "/images/cricket-bat.jpg",
                badge: null,
                inStock: true
            },
            {
                id: 5,
                name: "Badminton Set",
                category: "badminton",
                price: 5500,
                originalPrice: 6500,
                description: "Complete badminton set with 2 rackets and shuttlecocks",
                rating: 4.5,
                reviews: 92,
                image: "/images/badminton-set.jpg",
                badge: "sale",
                inStock: true
            },
            {
                id: 6,
                name: "Swimming Goggles",
                category: "swimming",
                price: 2000,
                originalPrice: null,
                description: "Anti-fog swimming goggles with UV protection",
                rating: 4.8,
                reviews: 234,
                image: "/images/swimming-goggles.jpg",
                badge: "new",
                inStock: true
            },
            {
                id: 7,
                name: "Fitness Dumbbells",
                category: "fitness",
                price: 7500,
                originalPrice: 9000,
                description: "Adjustable dumbbells set for home fitness training",
                rating: 4.7,
                reviews: 167,
                image: "/images/dumbbells.jpg",
                badge: "sale",
                inStock: true
            },
            {
                id: 8,
                name: "Yoga Mat",
                category: "fitness",
                price: 2500,
                originalPrice: null,
                description: "Non-slip premium yoga mat for all types of exercise",
                rating: 4.9,
                reviews: 298,
                image: "/images/yoga-mat.jpg",
                badge: null,
                inStock: true
            }
        ];

        // Shopping cart
        let cart = [];

        // Load navbar component
        function loadNavbar() {
            fetch('/app/views/components/navbar.php')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                });
        }

        // Load footer component  
        function loadFooter() {
            fetch('/app/views/components/footer.php')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                })
                .catch(() => {
                    document.getElementById('footer-container').innerHTML = `
                        <footer style="background: var(--text-primary); color: white; padding: 2rem 0; text-align: center; margin-top: 3rem;">
                            <p>&copy; 2024 GoPlay Sports Platform. All rights reserved.</p>
                        </footer>
                    `;
                });
        }

        // Render products
        function renderProducts(productsToRender = products) {
            const grid = document.getElementById('products-grid');
            
            if (productsToRender.length === 0) {
                grid.innerHTML = `
                    <div class="loading-state">
                        <div style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No products found</h3>
                        <p>Try adjusting your search criteria or filters.</p>
                    </div>
                `;
                return;
            }

            const productCards = productsToRender.map(product => {
                const stars = generateStars(product.rating);
                const discountPercent = product.originalPrice 
                    ? Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)
                    : 0;

                return `
                    <div class="product-card">
                        <div class="product-image">
                            ${product.badge ? `<div class="product-badge ${product.badge}">${product.badge === 'sale' ? `${discountPercent}% OFF` : 'NEW'}</div>` : ''}
                            <div class="product-icon">${getCategoryIcon(product.category)}</div>
                            <div class="product-overlay">
                                <button class="quick-view-btn" onclick="viewProduct(${product.id})">
                                    <i class="fas fa-eye"></i>
                                    Quick View
                                </button>
                            </div>
                        </div>
                        <div class="product-content">
                            <div class="product-category">${product.category.toUpperCase()}</div>
                            <h3 class="product-name">${product.name}</h3>
                            <p class="product-description">${product.description}</p>
                            <div class="product-rating">
                                <div class="stars">${stars}</div>
                                <span class="rating-text">(${product.reviews} reviews)</span>
                            </div>
                            <div class="product-price">
                                <span class="price-current">LKR ${product.price.toLocaleString()}</span>
                                ${product.originalPrice ? `<span class="price-original">LKR ${product.originalPrice.toLocaleString()}</span>` : ''}
                                ${product.badge === 'sale' ? `<span class="price-discount">${discountPercent}% OFF</span>` : ''}
                            </div>
                            <div class="product-actions">
                                <button class="btn-outline" onclick="addToWishlist(${product.id})">
                                    <i class="fas fa-heart"></i>
                                    Wishlist
                                </button>
                                <button class="btn-primary" onclick="addToCart(${product.id})">
                                    <i class="fas fa-cart-plus"></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            grid.innerHTML = productCards;
        }

        // Generate star rating HTML
        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;
            
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fas fa-star"></i>';
            }
            
            if (hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            }
            
            const remainingStars = 5 - Math.ceil(rating);
            for (let i = 0; i < remainingStars; i++) {
                stars += '<i class="far fa-star"></i>';
            }
            
            return stars;
        }

        // Get category icon
        function getCategoryIcon(category) {
            const icons = {
                'football': '½',
                'tennis': '<¾',
                'basketball': '<À',
                'cricket': '<Ï',
                'badminton': '<ø',
                'swimming': '<Ê',
                'fitness': '=ª'
            };
            return icons[category] || '<ß';
        }

        // Add to cart
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                const existingItem = cart.find(item => item.id === productId);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({...product, quantity: 1});
                }
                updateCartDisplay();
                showCartSummary();
            }
        }

        // Add to wishlist
        function addToWishlist(productId) {
            console.log('Added to wishlist:', productId);
            // Implement wishlist functionality
        }

        // View product
        function viewProduct(productId) {
            console.log('View product:', productId);
            // Implement product detail view
        }

        // Update cart display
        function updateCartDisplay() {
            const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
            const cartTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            
            document.getElementById('cart-count').textContent = cartCount;
            document.getElementById('cart-total').textContent = cartTotal.toLocaleString();
            
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = cart.map(item => `
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>${item.name} (${item.quantity})</span>
                    <span>LKR ${(item.price * item.quantity).toLocaleString()}</span>
                </div>
            `).join('');
        }

        // Toggle cart summary
        function toggleCart() {
            const cartSummary = document.getElementById('cart-summary');
            cartSummary.classList.toggle('visible');
        }

        // Show cart summary temporarily
        function showCartSummary() {
            const cartSummary = document.getElementById('cart-summary');
            cartSummary.classList.add('visible');
            setTimeout(() => {
                cartSummary.classList.remove('visible');
            }, 3000);
        }

        // Checkout
        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            // Redirect to checkout page
            window.location.href = '/payment?type=shop';
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadNavbar();
            loadFooter();
            setTimeout(renderProducts, 1000); // Simulate loading
        });
    </script>
</body>
</html>